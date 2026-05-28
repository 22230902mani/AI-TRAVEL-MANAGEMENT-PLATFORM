<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Razorpay\Api\Api as RazorpayApi;

class TripPlannerController extends Controller
{
    // ─── Free Estimate Page ────────────────────────────────────────────
    public function index()
    {
        return redirect()->route('home');
    }

    // ─── Calculate estimates (AJAX) ───────────────────────────────────
    public function calculate(Request $request)
    {
        $request->validate([
            'from'      => 'required|string|max:100',
            'to'        => 'required|string|max:100',
            'travelers' => 'required|integer|min:1|max:20',
            'days'      => 'required|integer|min:1|max:30',
            'budget'    => 'required|in:budget,standard,luxury',
        ]);

        $from     = $request->from;
        $to       = $request->to;
        $n        = (int) $request->travelers;
        $days     = (int) $request->days;
        $tier     = $request->budget;

        // ── Smart budget multipliers ──────────────────────────────────
        $multiplier = ['budget' => 1, 'standard' => 2.2, 'luxury' => 5][$tier] ?? 1;

        // ── Transport estimates (per person one-way) ──────────────────
        $baseFlightPP  = $this->estimateFlight($from, $to, $tier);
        $baseTrainPP   = $this->estimateTrain($from, $to, $tier);
        $baseBusPP     = $this->estimateBus($from, $to, $tier);

        $flight = $baseFlightPP * $n;
        $train  = $baseTrainPP  * $n;
        $bus    = $baseBusPP    * $n;

        // ── Daily costs per person ─────────────────────────────────────
        $hotelPPN = ['budget'=>800,'standard'=>2500,'luxury'=>7000][$tier];
        $foodPPN  = ['budget'=>300,'standard'=>700, 'luxury'=>2000][$tier];
        $localPPN = ['budget'=>200,'standard'=>500, 'luxury'=>1500][$tier];

        $hotelTotal = $hotelPPN * $days * $n;
        $foodTotal  = $foodPPN  * $days * $n;
        $localTotal = $localPPN * $days * $n;

        $totalWithFlight = $flight + $hotelTotal + $foodTotal + $localTotal;
        $totalWithTrain  = $train  + $hotelTotal + $foodTotal + $localTotal;
        $totalWithBus    = $bus    + $hotelTotal + $foodTotal + $localTotal;

        return response()->json([
            'from'      => $from,
            'to'        => $to,
            'travelers' => $n,
            'days'      => $days,
            'tier'      => $tier,
            'transport' => [
                'flight' => ['cost' => $flight, 'per_person' => $baseFlightPP, 'duration' => $this->flightDuration($from, $to), 'label' => 'Flight'],
                'train'  => ['cost' => $train,  'per_person' => $baseTrainPP,  'duration' => $this->trainDuration($from, $to),  'label' => 'Train'],
                'bus'    => ['cost' => $bus,     'per_person' => $baseBusPP,    'duration' => $this->busDuration($from, $to),    'label' => 'Bus'],
            ],
            'daily' => [
                'hotel' => ['total' => $hotelTotal, 'per_person_per_day' => $hotelPPN],
                'food'  => ['total' => $foodTotal,  'per_person_per_day' => $foodPPN],
                'local' => ['total' => $localTotal, 'per_person_per_day' => $localPPN],
            ],
            'totals' => [
                'with_flight' => $totalWithFlight,
                'with_train'  => $totalWithTrain,
                'with_bus'    => $totalWithBus,
                'cheapest'    => min($totalWithFlight, $totalWithTrain, $totalWithBus),
            ],
            'per_person' => [
                'with_flight' => round($totalWithFlight / $n),
                'with_train'  => round($totalWithTrain  / $n),
                'with_bus'    => round($totalWithBus    / $n),
            ],
            'savings_vs_flight' => $totalWithFlight - min($totalWithTrain, $totalWithBus),
        ]);
    }

    // ─── Create Razorpay order for Premium Unlock ─────────────────────
    public function createPremiumOrder(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Please login to unlock premium features.'], 401);
        }

        $planData = $request->validate([
            'from'      => 'required|string',
            'to'        => 'required|string',
            'travelers' => 'required|integer|min:1',
            'days'      => 'required|integer|min:1',
            'budget'    => 'required|string',
        ]);

        try {
            $api = new RazorpayApi(
                config('services.razorpay.key_id'),
                config('services.razorpay.key_secret')
            );

            $amount = 19900; // ₹199 in paise

            $order = $api->order->create([
                'amount'          => $amount,
                'currency'        => 'INR',
                'receipt'         => 'PLAN-' . strtoupper(\Illuminate\Support\Str::random(8)),
                'payment_capture' => 1,
                'notes'           => array_merge($planData, ['user_id' => (string) auth()->id()]),
            ]);

            // Store plan data in session for after payment
            session(['premium_plan_data' => $planData]);

            return response()->json([
                'order_id'   => $order->id,
                'amount'     => $amount,
                'currency'   => 'INR',
                'key_id'     => config('services.razorpay.key_id'),
                'name'       => auth()->user()->name,
                'email'      => auth()->user()->email,
                'description'=> 'TravelMate Premium AI Trip Plan — ' . $planData['from'] . ' → ' . $planData['to'],
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Razorpay Create Premium Plan Order Failed: ' . $e->getMessage(), [
                'plan_data' => $planData,
                'user_id'   => auth()->id(),
                'trace'     => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error'   => 'Failed to initialize premium plan payment: ' . $e->getMessage(),
            ], 422);
        }
    }

    // ─── Verify Premium Payment & Generate AI Plan ─────────────────────
    public function verifyPremium(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

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

        $planData = session('premium_plan_data') ?? $request->all();

        // Generate AI itinerary
        $aiPlan = $this->generateAIPlan($planData);

        // Store in session for display
        session(['premium_ai_plan' => $aiPlan, 'premium_transaction' => $request->razorpay_payment_id]);

        return response()->json([
            'success'      => true,
            'redirect_url' => route('planner.premium') . '?' . http_build_query($planData),
        ]);
    }

    // ─── Premium Results Page ─────────────────────────────────────────
    public function premium(Request $request)
    {
        $planData = $request->only(['from','to','travelers','days','budget']);
        $aiPlan   = session('premium_ai_plan') ?? $this->generateAIPlan($planData);
        $transaction = session('premium_transaction', 'DEMO');

        return view('planner.premium', compact('planData', 'aiPlan', 'transaction'));
    }

    // ─── Private Helpers ──────────────────────────────────────────────

    private function generateAIPlan(array $data): array
    {
        $from = $data['from'] ?? 'Origin';
        $to   = $data['to']   ?? 'Destination';
        $days = (int)($data['days'] ?? 3);
        $n    = (int)($data['travelers'] ?? 1);
        $tier = $data['budget'] ?? 'standard';

        // Try Gemini first
        $prompt = "Create a {$days}-day travel itinerary from {$from} to {$to} for {$n} traveler(s) on a {$tier} budget. Return ONLY valid JSON with keys: 'days' (array of {day_number, theme, activities (array of {time,activity,description,cost_inr})}), 'hotels' (array of {name,type,price_per_night,rating,highlights}), 'weather' (object with {best_time,temperature,tip}), 'budget_tips' (array of 4 strings), 'nearby_attractions' (array of {name,distance,entry_fee}). All costs in INR.";

        $json = GeminiService::generateText($prompt);
        if ($json) {
            $json = trim(str_replace(['```json','```'], '', $json));
            $parsed = json_decode($json, true);
            if (is_array($parsed) && isset($parsed['days'])) {
                return $parsed;
            }
        }

        // Fallback curated plan
        return $this->fallbackPlan($from, $to, $days, $n, $tier);
    }

    private function fallbackPlan(string $from, string $to, int $days, int $n, string $tier): array
    {
        $hotelOptions = [
            'budget'   => [['name'=>'Comfort Inn '.$to,'type'=>'Budget Hotel','price_per_night'=>900,'rating'=>4.1,'highlights'=>['Free WiFi','AC','Hot Water']],['name'=>'Travellers Lodge '.$to,'type'=>'Hostel','price_per_night'=>600,'rating'=>3.9,'highlights'=>['Dorm Rooms','Common Kitchen','Locker']]],
            'standard' => [['name'=>'The '.$to.' Grand','type'=>'3-Star Hotel','price_per_night'=>2500,'rating'=>4.4,'highlights'=>['Pool','Breakfast Included','Gym']],['name'=>'Cityside Hotel '.$to,'type'=>'Business Hotel','price_per_night'=>2200,'rating'=>4.3,'highlights'=>['Room Service','Parking','Conference Room']]],
            'luxury'   => [['name'=>'Palace by the '.$to.' Collection','type'=>'5-Star Luxury','price_per_night'=>8000,'rating'=>4.9,'highlights'=>['Infinity Pool','Spa','Fine Dining','Butler Service']],['name'=>'The Royal '.$to.' Suites','type'=>'Boutique Luxury','price_per_night'=>6500,'rating'=>4.8,'highlights'=>['Private Plunge Pool','Rooftop Bar','Chauffeur']]],
        ];

        $activities = [
            'Explore the city center and local markets',
            'Visit the famous historical monuments',
            'Try authentic local street food tour',
            'Museum and cultural heritage walk',
            'Nature walk or scenic viewpoint visit',
            'Shopping at local craft bazaars',
            'Sunset river or lake cruise',
            'Day trip to nearby villages',
            'Adventure activity (trekking/cycling)',
            'Spa and wellness evening',
        ];

        $daysArr = [];
        for ($i = 1; $i <= $days; $i++) {
            $cost = ['budget'=>300,'standard'=>700,'luxury'=>2000][$tier] ?? 500;
            $daysArr[] = [
                'day_number' => $i,
                'theme'      => "Day $i: " . ['Arrival & Exploration', 'City Deep Dive', 'Cultural Heritage', 'Adventure & Nature', 'Food & Shopping', 'Day Trip', 'Relaxation & Departure'][$i % 7],
                'activities' => [
                    ['time'=>'8:00 AM', 'activity'=>'Breakfast',       'description'=>'Start the day with local breakfast', 'cost_inr'=>$cost*0.15],
                    ['time'=>'10:00 AM','activity'=>$activities[($i*2)%10], 'description'=>'Guided exploration of key attractions', 'cost_inr'=>$cost*0.4],
                    ['time'=>'1:00 PM', 'activity'=>'Lunch',           'description'=>'Local cuisine experience',             'cost_inr'=>$cost*0.2],
                    ['time'=>'3:00 PM', 'activity'=>$activities[($i*2+1)%10],'description'=>'Afternoon activity',             'cost_inr'=>$cost*0.25],
                    ['time'=>'7:00 PM', 'activity'=>'Dinner & Evening','description'=>'Evening at a local restaurant',        'cost_inr'=>$cost*0.2],
                ],
            ];
        }

        return [
            'days'   => $daysArr,
            'hotels' => $hotelOptions[$tier] ?? $hotelOptions['standard'],
            'weather'=> ['best_time'=>'October to March', 'temperature'=>'18–28°C', 'tip'=>'Carry light layers; mornings can be cool'],
            'budget_tips' => [
                'Book flights 3–4 weeks in advance for 20–30% savings',
                'Use local transport (auto/bus) instead of cabs for short distances',
                'Eat at local dhabas/thalis — better food, lower prices',
                'Visit free attractions first and budget for 1–2 paid experiences per day',
            ],
            'nearby_attractions' => [
                ['name'=>'Old City Heritage Walk',   'distance'=>'1.2 km', 'entry_fee'=>'Free'],
                ['name'=>'Local Art Gallery',        'distance'=>'2.5 km', 'entry_fee'=>'₹50'],
                ['name'=>'Botanical Garden',         'distance'=>'4 km',   'entry_fee'=>'₹30'],
                ['name'=>'Night Market Bazaar',      'distance'=>'0.8 km', 'entry_fee'=>'Free'],
                ['name'=>'Scenic Viewpoint / Hills', 'distance'=>'8 km',   'entry_fee'=>'₹20'],
            ],
        ];
    }

    // ── Distance-based transport estimators ──────────────────────────
    private function estimateFlight(string $from, string $to, string $tier): int
    {
        $base = $this->isLongDistance($from, $to) ? 4500 : 2500;
        return (int) ($base * ['budget'=>1,'standard'=>1.5,'luxury'=>3][$tier]);
    }
    private function estimateTrain(string $from, string $to, string $tier): int
    {
        $base = $this->isLongDistance($from, $to) ? 800 : 350;
        return (int) ($base * ['budget'=>1,'standard'=>2,'luxury'=>4][$tier]);
    }
    private function estimateBus(string $from, string $to, string $tier): int
    {
        $base = $this->isLongDistance($from, $to) ? 500 : 200;
        return (int) ($base * ['budget'=>1,'standard'=>1.5,'luxury'=>2.5][$tier]);
    }
    private function isLongDistance(string $from, string $to): bool
    {
        $longPairs = ['delhi'=>['mumbai','chennai','kolkata','goa','bangalore','hyderabad','kochi','pune'],
                      'mumbai'=>['delhi','kolkata','chennai','ahmedabad','jaipur']];
        $fl = strtolower($from); $tl = strtolower($to);
        foreach ($longPairs as $city => $dests) {
            if (str_contains($fl,$city) && collect($dests)->contains(fn($d)=>str_contains($tl,$d))) return true;
        }
        return strlen($to) > 0 && strtolower(substr($to,0,2)) !== strtolower(substr($from,0,2));
    }
    private function flightDuration(string $from, string $to): string { return $this->isLongDistance($from,$to) ? '2h 15m' : '1h 10m'; }
    private function trainDuration(string $from, string $to): string  { return $this->isLongDistance($from,$to) ? '14h 30m' : '4h 45m'; }
    private function busDuration(string $from, string $to): string    { return $this->isLongDistance($from,$to) ? '18h' : '5h 30m'; }
}
