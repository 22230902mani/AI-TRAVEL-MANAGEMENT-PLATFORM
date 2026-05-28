<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Booking;
use App\Models\Destination;
use App\Services\BookingService;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Razorpay\Api\Api as RazorpayApi;
use Carbon\Carbon;

class HotelController extends Controller
{
    public function __construct(private BookingService $bookingService) {}

    /**
     * Show recommended hotels after a package booking is confirmed.
     * Called from the booking confirmation page.
     */
    public function recommend(Booking $booking)
    {
        $this->authorize('view', $booking);
        $booking->load('package.destination');

        $destination = $booking->package?->destination;

        // Fetch hotels for this destination
        $hotels = collect();
        if ($destination) {
            $hotels = Hotel::where('destination_id', (string) $destination->id)
                ->where('is_active', true)
                ->get();
        }

        // If no hotels in DB, generate smart dummy hotels using knowledge base
        if ($hotels->isEmpty()) {
            $hotels = $this->generateSmartHotels($destination, $booking);
        } else {
            // Augment with smart room suggestions based on budget
            $hotels = $hotels->map(fn($h) => $this->augmentHotel($h, $booking));
        }

        // Sort by smart recommendation score
        $hotels = $hotels->sortByDesc('recommendation_score')->values();

        $checkIn  = $booking->check_in ? Carbon::parse($booking->check_in) : now()->addDays(7);
        $checkOut = $checkIn->copy()->addDays(3);
        $nights   = max(1, $checkIn->diffInDays($checkOut));

        return view('hotels.recommend', compact('booking', 'destination', 'hotels', 'checkIn', 'checkOut', 'nights'));
    }

    /**
     * Show a single hotel detail page.
     */
    public function show(Hotel $hotel, Request $request)
    {
        $hotel->load('destination');
        $checkIn  = $request->check_in  ? Carbon::parse($request->check_in)  : now()->addDays(7);
        $checkOut = $request->check_out ? Carbon::parse($request->check_out) : $checkIn->copy()->addDays(3);
        $nights   = max(1, $checkIn->diffInDays($checkOut));
        $bookingId = $request->booking_id;

        // Generate room types if not set
        $roomTypes = $hotel->room_types;
        if (empty($roomTypes)) {
            $base = $hotel->price_per_night ?? 3000;
            $roomTypes = [
                ['type' => 'Standard Room',   'price' => $base,           'beds' => 1, 'size' => '220 sq ft', 'amenities' => ['AC', 'WiFi', 'TV', 'Hot Water']],
                ['type' => 'Deluxe Room',     'price' => $base * 1.5,     'beds' => 1, 'size' => '320 sq ft', 'amenities' => ['AC', 'WiFi', 'TV', 'Mini Bar', 'City View']],
                ['type' => 'Suite',           'price' => $base * 2.5,     'beds' => 2, 'size' => '500 sq ft', 'amenities' => ['AC', 'WiFi', 'TV', 'Jacuzzi', 'Butler Service', 'Balcony']],
            ];
        }

        return view('hotels.show', compact('hotel', 'checkIn', 'checkOut', 'nights', 'roomTypes', 'bookingId'));
    }

    /**
     * Show hotel booking form (room selection + guest details).
     */
    public function book(Hotel $hotel, Request $request)
    {
        $hotel->load('destination');
        $checkIn   = Carbon::parse($request->check_in  ?? now()->addDays(7));
        $checkOut  = Carbon::parse($request->check_out ?? now()->addDays(10));
        $roomType  = $request->room_type ?? 'Standard Room';
        $roomPrice = (float) ($request->room_price ?? $hotel->price_per_night ?? 3000);
        $nights    = max(1, $checkIn->diffInDays($checkOut));
        $total     = $roomPrice * $nights;
        $bookingId = $request->booking_id;

        return view('hotels.book', compact('hotel', 'checkIn', 'checkOut', 'nights', 'roomType', 'roomPrice', 'total', 'bookingId'));
    }

    /**
     * Store hotel booking.
     */
    public function storeBooking(Request $request, Hotel $hotel)
    {
        $request->validate([
            'check_in'    => 'required|date',
            'check_out'   => 'required|date|after:check_in',
            'room_type'   => 'required|string',
            'room_price'  => 'required|numeric|min:0',
            'adults'      => 'required|integer|min:1',
            'children'    => 'nullable|integer|min:0',
        ]);

        $nights = max(1, Carbon::parse($request->check_in)->diffInDays(Carbon::parse($request->check_out)));
        $total  = (float) $request->room_price * $nights;

        $booking = Booking::create([
            'user_id'          => auth()->id(),
            'hotel_id'         => (string) $hotel->id,
            'booking_type'     => 'hotel',
            'check_in'         => $request->check_in,
            'check_out'        => $request->check_out,
            'adults'           => $request->adults,
            'children'         => $request->children ?? 0,
            'total_amount'     => $total,
            'paid_amount'      => 0,
            'payment_status'   => 'pending',
            'booking_status'   => 'pending',
            'special_requests' => $request->special_requests,
            'traveler_details' => [
                'room_type'  => $request->room_type,
                'room_price' => $request->room_price,
                'nights'     => $nights,
            ],
        ]);

        $booking->appendEvent('hotel_booking_created', [
            'hotel'      => $hotel->name,
            'room_type'  => $request->room_type,
            'nights'     => $nights,
            'amount'     => $total,
        ]);

        return redirect()->route('hotels.payment', $booking)
            ->with('success', 'Hotel selected! Complete payment to confirm your room.');
    }

    /**
     * Hotel payment page.
     */
    public function payment(Booking $booking)
    {
        $this->authorize('view', $booking);
        $booking->load('hotel.destination');
        return view('hotels.payment', compact('booking'));
    }

    /**
     * Create Razorpay order for hotel payment.
     */
    public function createRazorpayOrder(Booking $booking)
    {
        $this->authorize('view', $booking);

        $api = new RazorpayApi(
            config('services.razorpay.key_id'),
            config('services.razorpay.key_secret')
        );

        $amountInPaise = (int) round($booking->total_amount * 100);

        if (str_starts_with(config('services.razorpay.key_id'), 'rzp_test_')) {
            $amountInPaise = min($amountInPaise, 4000000);
        }

        try {
            $order = $api->order->create([
                'amount'          => $amountInPaise,
                'currency'        => 'INR',
                'receipt'         => $booking->booking_reference,
                'payment_capture' => 1,
                'notes'           => [
                    'booking_id' => (string) $booking->id,
                    'type'       => 'hotel',
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => 'Payment initiation failed: ' . $e->getMessage(),
            ], 422);
        }

        $booking->update(['razorpay_order_id' => $order->id]);

        return response()->json([
            'order_id'    => $order->id,
            'amount'      => $amountInPaise,
            'currency'    => 'INR',
            'key_id'      => config('services.razorpay.key_id'),
            'name'        => auth()->user()->name,
            'email'       => auth()->user()->email,
            'booking_ref' => $booking->booking_reference,
            'description' => 'Hotel Room — ' . ($booking->traveler_details['room_type'] ?? 'Room'),
        ]);
    }

    /**
     * Verify hotel payment and confirm booking.
     */
    public function verifyPayment(Request $request, Booking $booking)
    {
        $this->authorize('view', $booking);

        $request->validate([
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id'   => 'required|string',
            'razorpay_signature'  => 'required|string',
        ]);

        $api = new RazorpayApi(
            config('services.razorpay.key_id'),
            config('services.razorpay.key_secret')
        );

        try {
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id'   => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature'  => $request->razorpay_signature,
            ]);
        } catch (\Razorpay\Api\Errors\SignatureVerificationError $e) {
            return response()->json(['success' => false, 'message' => 'Payment verification failed.'], 422);
        }

        $this->bookingService->confirmBooking($booking, $request->razorpay_payment_id);

        return response()->json([
            'success'      => true,
            'redirect_url' => route('hotels.confirmation', $booking),
        ]);
    }

    /**
     * Hotel booking confirmation page.
     */
    public function confirmation(Booking $booking)
    {
        $this->authorize('view', $booking);
        $booking->load('hotel.destination');
        return view('hotels.confirmation', compact('booking'));
    }

    // ─────────────────────────────────────────────────────────────────
    // Private Helpers
    // ─────────────────────────────────────────────────────────────────

    private function generateSmartHotels(?Destination $destination, Booking $booking): \Illuminate\Support\Collection
    {
        $destName = $destination?->name ?? 'the destination';
        $budget   = $booking->total_amount ?? 5000;
        $budgetPerNight = round($budget * 0.15); // ~15% of trip budget for hotel

        $imageMap = [
            'luxury'   => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&q=80',
            'boutique' => 'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=800&q=80',
            'budget'   => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=800&q=80',
            'resort'   => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=800&q=80',
            'heritage' => 'https://images.unsplash.com/photo-1582719508461-905c673771fd?w=800&q=80',
        ];

        $hotels = collect([
            [
                'id'               => 'smart_1',
                'name'             => 'The Grand ' . $destName . ' Palace',
                'star_rating'      => 5,
                'price_per_night'  => $budgetPerNight * 2.5,
                'image'            => $imageMap['luxury'],
                'image_url'        => $imageMap['luxury'],
                'description'      => 'Experience unparalleled luxury at this 5-star landmark. Rooftop infinity pool, Michelin-star dining, and breathtaking views await.',
                'address'          => 'Central Business District, ' . $destName,
                'amenities'        => ['Pool', 'Spa', 'Gym', 'Fine Dining', 'Airport Transfer', 'Butler Service', 'WiFi'],
                'avg_rating'       => 4.9,
                'review_count'     => rand(300, 1200),
                'distance_km'      => 0.8,
                'type'             => 'luxury',
                'recommendation_score' => 95,
                'highlights'       => ['Award-winning spa', 'Rooftop pool', 'City view suites'],
                'is_smart'         => true,
            ],
            [
                'id'               => 'smart_2',
                'name'             => $destName . ' Heritage Boutique',
                'star_rating'      => 4,
                'price_per_night'  => $budgetPerNight * 1.2,
                'image'            => $imageMap['boutique'],
                'image_url'        => $imageMap['boutique'],
                'description'      => 'A charming boutique hotel blending local culture with contemporary comfort. Each room tells a story of the destination\'s rich history.',
                'address'          => 'Old Town Quarter, ' . $destName,
                'amenities'        => ['Rooftop Bar', 'Cultural Tours', 'Yoga', 'Organic Breakfast', 'WiFi'],
                'avg_rating'       => 4.7,
                'review_count'     => rand(150, 600),
                'distance_km'      => 1.5,
                'type'             => 'boutique',
                'recommendation_score' => 88,
                'highlights'       => ['Local cultural experiences', 'Heritage architecture', 'Organic farm breakfast'],
                'is_smart'         => true,
            ],
            [
                'id'               => 'smart_3',
                'name'             => $destName . ' Comfort Stay',
                'star_rating'      => 3,
                'price_per_night'  => $budgetPerNight * 0.7,
                'image'            => $imageMap['budget'],
                'image_url'        => $imageMap['budget'],
                'description'      => 'Smart value accommodation with modern amenities. Perfect for budget-conscious travellers who don\'t want to compromise on comfort.',
                'address'          => 'Travellers Lane, ' . $destName,
                'amenities'        => ['WiFi', 'AC', 'Hot Water', 'Breakfast', 'Parking'],
                'avg_rating'       => 4.3,
                'review_count'     => rand(80, 300),
                'distance_km'      => 2.2,
                'type'             => 'budget',
                'recommendation_score' => 78,
                'highlights'       => ['Best value', 'Central location', 'Free breakfast'],
                'is_smart'         => true,
            ],
            [
                'id'               => 'smart_4',
                'name'             => $destName . ' Resort & Spa',
                'star_rating'      => 5,
                'price_per_night'  => $budgetPerNight * 3,
                'image'            => $imageMap['resort'],
                'image_url'        => $imageMap['resort'],
                'description'      => 'An all-inclusive resort retreat with world-class spa, private beach access, and curated wellness experiences for the discerning traveller.',
                'address'          => 'Scenic Beach Road, ' . $destName,
                'amenities'        => ['Private Beach', 'Infinity Pool', 'Full Spa', 'Water Sports', 'Fine Dining', 'Kids Club', 'WiFi'],
                'avg_rating'       => 4.8,
                'review_count'     => rand(200, 900),
                'distance_km'      => 4.5,
                'type'             => 'resort',
                'recommendation_score' => 82,
                'highlights'       => ['Private beach', 'All-inclusive option', 'Kids Club'],
                'is_smart'         => true,
            ],
        ]);

        return $hotels->map(function ($h) {
            return (object) array_merge($h, [
                'room_types' => [
                    ['type' => 'Standard Room',   'price' => $h['price_per_night'],       'beds' => 1, 'size' => '220 sq ft'],
                    ['type' => 'Deluxe Room',     'price' => $h['price_per_night'] * 1.5, 'beds' => 1, 'size' => '320 sq ft'],
                    ['type' => 'Suite',           'price' => $h['price_per_night'] * 2.5, 'beds' => 2, 'size' => '500 sq ft'],
                ],
            ]);
        });
    }

    private function augmentHotel(Hotel $hotel, Booking $booking): Hotel
    {
        $budget = $booking->total_amount ?? 5000;
        $score  = ($hotel->star_rating * 15) + rand(5, 20);
        $hotel->recommendation_score = $score;
        $hotel->distance_km          = round(rand(5, 45) / 10, 1);
        $hotel->avg_rating           = $hotel->avg_rating ?? round(rand(38, 50) / 10, 1);
        $hotel->review_count         = $hotel->review_count ?? rand(50, 500);
        return $hotel;
    }
}
