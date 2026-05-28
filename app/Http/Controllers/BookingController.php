<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Package;
use App\Models\Promotion;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Razorpay\Api\Api as RazorpayApi;

class BookingController extends Controller
{
    public function __construct(private BookingService $bookingService) {}

    public function index()
    {
        $bookings = auth()->user()->bookings()
            ->with(['package.destination', 'hotel'])
            ->latest()->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    public function create(Request $request)
    {
        $package = Package::with('destination')->findOrFail($request->package_id);
        return view('bookings.create', compact('package'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'package_id'       => 'required|exists:packages,id',
            'check_in'         => 'required|date|after_or_equal:today',
            'adults'           => 'required|integer|min:1|max:20',
            'children'         => 'nullable|integer|min:0|max:10',
            'promo_code'       => 'nullable|string',
            'special_requests' => 'nullable|string|max:500',
        ]);

        $booking = $this->bookingService->createBooking(
            $request->only('package_id', 'check_in', 'adults', 'children', 'promo_code', 'special_requests', 'booking_type'),
            auth()->id()
        );

        if (! isset($booking->id)) {
            return back()->with('error', 'Booking failed. Please try again.');
        }

        $booking->update([
            'booking_type' => 'package',
            'complimentary_addons' => [
                'Free Local Guide',
                'Custom Trip Plan',
                'Visa Assistance',
                'Welcome Kit'
            ]
        ]);

        return redirect()->route('bookings.payment', $booking)
            ->with('success', 'Booking created! Please complete payment.');
    }

    public function payment(Booking $booking)
    {
        $this->authorize('view', $booking);
        $booking->load('package.destination');
        return view('bookings.payment', compact('booking'));
    }

    /**
     * Create a Razorpay order — called via AJAX when the user clicks Pay.
     */
    public function createRazorpayOrder(Request $request, Booking $booking)
    {
        $this->authorize('view', $booking);

        $api = new RazorpayApi(
            config('services.razorpay.key_id'),
            config('services.razorpay.key_secret')
        );

        // Razorpay amounts are in paise (1 INR = 100 paise)
        $amountInPaise = (int) round($booking->total_amount * 100);

        // In test mode, Razorpay caps maximum amount per transaction.
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
                    'user_email' => auth()->user()->email,
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
            'description' => $booking->package?->title ?? 'TravelMate Booking',
        ]);
    }

    /**
     * Verify Razorpay payment signature and confirm the booking.
     */
    public function verifyRazorpayPayment(Request $request, Booking $booking)
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
            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed. Please contact support.',
            ], 422);
        }

        $this->bookingService->confirmBooking($booking, $request->razorpay_payment_id);

        return response()->json([
            'success'      => true,
            'redirect_url' => route('bookings.confirmation', $booking),
        ]);
    }

    public function processPayment(Request $request, Booking $booking)
    {
        $this->authorize('view', $booking);
        $transactionId = 'TXN' . strtoupper(substr(md5(uniqid()), 0, 12));
        $booking = $this->bookingService->confirmBooking($booking, $transactionId);
        return redirect()->route('bookings.confirmation', $booking)
            ->with('success', '🎉 Payment successful! Your trip is confirmed.');
    }

    public function confirmation(Booking $booking)
    {
        $this->authorize('view', $booking);
        $booking->load('package.destination', 'hotel');
        return view('bookings.confirmation', compact('booking'));
    }

    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);
        $booking->load('package.destination', 'hotel');
        return view('bookings.show', compact('booking'));
    }

    public function cancel(Booking $booking, Request $request)
    {
        $this->authorize('view', $booking);
        $request->validate(['reason' => 'required|string|min:10']);

        if ($booking->isCancelled()) {
            return back()->with('error', 'This booking is already cancelled.');
        }

        $this->bookingService->cancelBooking($booking, $request->reason);

        return redirect()->route('bookings.index')
            ->with('success', 'Booking cancelled. Refund will be processed in 5–7 days.');
    }

    public function bookGuide(Booking $booking, Request $request)
    {
        $this->authorize('view', $booking);
        $request->validate([
            'language' => 'required|string',
            'guide_details' => 'required|string|min:5|max:1000',
        ]);

        // Find or create a mock Guide user in the database
        $guide = \App\Models\User::where('role', 'guide')
            ->orWhere('roles', 'like', '%guide%')
            ->first();
        if (!$guide) {
            $guide = \App\Models\User::create([
                'name' => 'Amit Sharma (Local Guide)',
                'email' => 'amit.guide@travelmate.com',
                'password' => bcrypt('password123'),
                'roles' => ['guide'],
                'role' => 'guide',
                'guide_status' => 'approved',
                'is_active' => true,
            ]);
            
            // Add a profile for the guide to prevent avatar_url bugs
            \App\Models\UserProfile::create([
                'user_id' => $guide->id,
                'phone' => '+91 98765 43210',
                'bio' => 'Certified Local Expert and Travel Manager.',
            ]);
        }

        // Update the booking guide_id
        $booking->update([
            'guide_id' => $guide->id,
            'special_requests' => $booking->special_requests . "\n[Guide Request Details: Language: " . $request->language . " | Notes: " . $request->guide_details . "]"
        ]);

        $booking->appendEvent('guide_booked_by_user', [
            'guide_name' => $guide->name,
            'language' => $request->language,
            'notes' => $request->guide_details
        ]);

        // Create a TravelNotification
        \App\Models\TravelNotification::create([
            'user_id' => auth()->id(),
            'type'    => 'guide_assigned',
            'title'   => 'Guide Booking Confirmed',
            'message' => "Your local travel manager ({$guide->name}) has been successfully booked for booking {$booking->booking_reference}.",
        ]);

        // Dispatch Email Confirmation
        try {
            $userEmail = auth()->user()->email;
            $subject = "Guide Booking Confirmed - Booking #" . $booking->booking_reference;
            
            $messageContent = "Dear " . auth()->user()->name . ",\n\n" .
                              "Congratulations! Your local travel manager / guide booking has been successfully confirmed.\n\n" .
                              "--- Guide & Travel Manager Details ---\n" .
                              "Name: " . $guide->name . "\n" .
                              "Phone: +91 98765 43210\n" .
                              "Email: " . $guide->email . "\n\n" .
                              "--- Your Preferences ---\n" .
                              "Booking Reference: " . $booking->booking_reference . "\n" .
                              "Language Preference: " . $request->language . "\n" .
                              "Special Instructions: " . $request->guide_details . "\n\n" .
                              "Our team and your assigned guide will get in touch with you shortly to coordinate your plans.\n\n" .
                              "Warm regards,\n" .
                              "TravelMate Team";

            // Send confirmation mail to requested email addresses and CC auth user
            \Illuminate\Support\Facades\Mail::raw($messageContent, function ($m) use ($userEmail, $subject) {
                $m->to('manilukka143@gmail.com')
                  ->to('manilukka143@gmail.com4') // Include both specified variations just in case
                  ->cc($userEmail)
                  ->subject($subject);
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Guide assignment email delivery failed: ' . $e->getMessage());
        }

        return back()->with('success', '🎉 Guide booked successfully! A confirmation email has been dispatched to manilukka143@gmail.com.');
    }

    public function applyPromo(Request $request)
    {
        $request->validate([
            'promo_code' => 'required|string',
            'amount'     => 'required|numeric',
        ]);

        $promo = Promotion::where('code', $request->promo_code)->first();

        if (! $promo || ! $promo->isValid()) {
            return response()->json(['error' => 'Invalid or expired promo code.'], 422);
        }

        $discount = $promo->calculateDiscount($request->amount);

        return response()->json([
            'discount'      => $discount,
            'final_amount'  => $request->amount - $discount,
            'discount_type' => $promo->discount_type,
            'discount_value'=> $promo->discount_value,
            'message'       => "🎉 Promo applied! You save ₹{$discount}",
        ]);
    }
}
