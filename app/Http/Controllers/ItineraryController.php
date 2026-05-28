<?php

namespace App\Http\Controllers;

use App\Models\Itinerary;
use App\Models\Destination;
use App\Services\ItineraryService;
use Illuminate\Http\Request;
use Razorpay\Api\Api as RazorpayApi;
use Illuminate\Support\Facades\Cache;

class ItineraryController extends Controller
{
    public function __construct(private ItineraryService $itineraryService) {}

    public function index()
    {
        $itineraries = auth()->user()->itineraries()
            ->with('destination')->latest()->paginate(10);
        return view('itineraries.index', compact('itineraries'));
    }

    public function create()
    {
        $destinations = Cache::remember('active_destinations', 3600, function() {
            return Destination::active()->orderBy('name')->get();
        });
        
        $localPlaces = $destinations->map(function($d) {
            return [
                'name' => $d->name,
                'city' => $d->city ?? $d->name,
                'state' => $d->state ?? '',
                'country' => $d->country ?? 'India',
            ];
        })->values()->toArray();

        $hasPremiumAccess = \App\Models\Booking::where('user_id', auth()->id())
            ->where('booking_type', 'itinerary')
            ->where('payment_status', 'paid')
            ->exists();

        return view('itineraries.create', compact('destinations', 'localPlaces', 'hasPremiumAccess'));
    }

    public function searchCity(Request $request)
    {
        $query = $request->query('q');
        if (!$query) return response()->json([]);

        $url = "https://nominatim.openstreetmap.org/search";
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'User-Agent' => 'TravelMate-App/1.0 (travelmate@localhost.local)'
        ])->get($url, [
            'q' => $query,
            'format' => 'json',
            'addressdetails' => 1,
            'limit' => 5
        ]);

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json([]);
    }

    public function generate(Request $request)
    {
        $request->validate([
            'origin'           => 'required|string|max:100',
            'destination_name' => 'required|string|max:200',
            'start_date'       => 'required|date|after_or_equal:today',
            'duration_days'    => 'required|integer|min:1|max:30',
            'budget'           => 'required|numeric|min:100',
            'interests'        => 'nullable|array',
            'group_type'       => 'nullable|string',
            'include_food'     => 'nullable|boolean',
        ]);

        $destName = trim(explode(',', $request->destination_name)[0]);
        $destCountry = str_contains($request->destination_name, ',') ? trim(explode(',', $request->destination_name)[1]) : 'Unknown';
        if (strtolower($destName) === 'london') $destCountry = 'United Kingdom';
        elseif (strtolower($destName) === 'paris') $destCountry = 'France';
        elseif (strtolower($destName) === 'new york') $destCountry = 'United States';
        elseif (strtolower($destName) === 'tokyo') $destCountry = 'Japan';
        elseif (strtolower($destName) === 'dubai') $destCountry = 'United Arab Emirates';
        elseif (strtolower($destName) === 'singapore') $destCountry = 'Singapore';
        elseif (strtolower($destName) === 'bangkok') $destCountry = 'Thailand';
        elseif (strtolower($destName) === 'sydney') $destCountry = 'Australia';
        
        $destination = Destination::firstOrCreate(
            ['name' => $destName],
            [
                'country' => $destCountry,
                'city' => $destName,
                'description' => 'AI-generated destination from user search.',
                'status' => 'active',
                'category' => 'custom',
                'image_url' => 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?auto=format&fit=crop&q=80&w=1000'
            ]
        );

        if ($destination->country === 'Unknown' && $destCountry !== 'Unknown') {
            $destination->update(['country' => $destCountry]);
        }

        $plan = $this->itineraryService->generate(
            userId:        auth()->id(),
            origin:        $request->origin,
            destinationId: $destination->id,
            days:          $request->duration_days,
            startDate:     $request->start_date,
            budget:        $request->budget,
            interests:     $request->interests ?? ['urban', 'heritage'],
            groupType:     $request->group_type ?? 'solo',
            includeFood:   $request->boolean('include_food'),
        );

        // Check if user has a valid booking to get this for free or has bought an itinerary before
        $isPaid = false;
        
        $hasBoughtItinerary = \App\Models\Booking::where('user_id', auth()->id())
            ->where('booking_type', 'itinerary')
            ->where('payment_status', 'paid')
            ->exists();

        if ($hasBoughtItinerary) {
            $isPaid = true;
        } elseif ($request->filled('booking_id')) {
            $booking = \App\Models\Booking::where('id', $request->booking_id)
                ->where('user_id', auth()->id())
                ->first();
            if ($booking) $isPaid = true;
        }

        // Store the generated itinerary
        $itinerary   = Itinerary::create([
            'user_id'        => auth()->id(),
            'destination_id' => $destination->id,
            'title'          => 'Trip to ' . $destination->name,
            'start_date'     => $request->start_date,
            'end_date'       => \Carbon\Carbon::parse($request->start_date)->addDays($request->duration_days - 1),
            'duration_days'  => $request->duration_days,
            'budget'         => $request->budget,
            'days'           => $plan['days'],
            'is_paid'        => $isPaid,
            'preferences'    => [
                'origin'       => $request->origin,
                'interests'    => $request->interests,
                'group_type'   => $request->group_type,
                'include_food' => $request->boolean('include_food'),
                'financials'   => $plan['financial_summary'] ?? null,
                'currency'     => $plan['currency'] ?? ['symbol' => '₹', 'code' => 'INR', 'rate' => 1],
            ],
            'status'         => 'planning',
        ]);

        return response()->json([
            'success'    => true,
            'itinerary'  => $itinerary,
            'plan'       => $plan,
            'redirect'   => route('itineraries.show', $itinerary),
        ]);
    }

    public function show(Itinerary $itinerary)
    {
        $this->authorize('view', $itinerary);
        $itinerary->load('destination', 'expenses');
        return view('itineraries.show', compact('itinerary'));
    }

    public function downloadPdf(Itinerary $itinerary)
    {
        $this->authorize('view', $itinerary);
        if (!$itinerary->is_paid) {
            return redirect()->route('itineraries.show', $itinerary)
                ->with('error', 'This is a premium feature. Please unlock this itinerary to download the PDF.');
        }
        $itinerary->load('destination');
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('itineraries.pdf', compact('itinerary'));
        return $pdf->download('TravelMate-Itinerary-'.$itinerary->id.'.pdf');
    }

    public function edit(Itinerary $itinerary)
    {
        $this->authorize('update', $itinerary);
        $destinations = Destination::active()->orderBy('name')->get();
        return view('itineraries.edit', compact('itinerary', 'destinations'));
    }

    public function update(Request $request, Itinerary $itinerary)
    {
        $this->authorize('update', $itinerary);

        $request->validate([
            'title'  => 'required|string|max:200',
            'status' => 'required|in:planning,active,completed,cancelled',
            'budget' => 'nullable|numeric',
        ]);

        $itinerary->update($request->only('title','description','status','budget','is_public','is_collaborative'));

        return redirect()->route('itineraries.show', $itinerary)
            ->with('success', 'Itinerary updated!');
    }

    public function destroy(Itinerary $itinerary)
    {
        $this->authorize('delete', $itinerary);
        $itinerary->delete();
        return redirect()->route('itineraries.index')->with('success', 'Itinerary deleted.');
    }

    public function replan(Request $request, Itinerary $itinerary)
    {
        $this->authorize('update', $itinerary);

        $days = $this->itineraryService->replan(
            $itinerary,
            $request->current_day ?? 1,
            $request->completed_slots ?? 0
        );

        return response()->json(['success' => true, 'days' => $days]);
    }

    /** Share link — public view */
    public function share(string $token)
    {
        $itinerary = Itinerary::where('share_token', $token)
            ->where('is_public', true)
            ->with('destination', 'user')
            ->firstOrFail();

        return view('itineraries.share', compact('itinerary'));
    }

    public function unlockOrder(Itinerary $itinerary)
    {
        try {
            $api = new RazorpayApi(
                config('services.razorpay.key_id'),
                config('services.razorpay.key_secret')
            );

            $amount = 9900; // ₹99 in paise

            $order = $api->order->create([
                'amount'          => $amount,
                'currency'        => 'INR',
                'receipt'         => 'ITIN-' . strtoupper(\Illuminate\Support\Str::random(8)),
                'payment_capture' => 1,
                'notes'           => [
                    'itinerary_id' => (string) $itinerary->id,
                    'user_id'      => (string) auth()->id(),
                ],
            ]);

            return response()->json([
                'order_id'   => $order->id,
                'amount'     => $amount,
                'currency'   => 'INR',
                'key_id'     => config('services.razorpay.key_id'),
                'name'       => auth()->user()->name,
                'email'      => auth()->user()->email,
                'description'=> 'Unlock Premium TravelMate Itinerary: ' . $itinerary->title,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Razorpay Unlock Itinerary Order Creation Failed: ' . $e->getMessage(), [
                'itinerary_id' => $itinerary->id,
                'user_id'      => auth()->id(),
                'trace'        => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'success' => false,
                'error'   => 'Failed to initialize payment: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function verifyPayment(Request $request, Itinerary $itinerary)
    {
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

        $itinerary->update(['is_paid' => true]);

        // Create a corresponding Booking record for the transaction history page
        try {
            \App\Models\Booking::create([
                'user_id'           => auth()->id(),
                'itinerary_id'      => $itinerary->id,
                'booking_type'      => 'itinerary',
                'total_amount'      => 99.00,
                'paid_amount'       => 99.00,
                'payment_status'    => 'paid',
                'booking_status'    => 'confirmed',
                'payment_method'    => 'razorpay',
                'transaction_id'    => $request->razorpay_payment_id,
                'razorpay_order_id' => $request->razorpay_order_id,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to create Booking record for paid Itinerary: ' . $e->getMessage());
        }

        return response()->json([
            'success'      => true,
            'redirect_url' => route('itineraries.show', $itinerary) . '?unlocked=true',
        ]);
    }
}
