<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\Package;
use App\Models\Review;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    public function index(Request $request)
    {
        $query = Destination::active()->with(['packages', 'reviews']);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('country')) {
            $query->where('country', 'like', '%' . $request->country . '%');
        }
        if ($request->filled('season')) {
            $query->where('best_season', 'like', '%' . $request->season . '%');
        }
        if ($request->filled('budget_max')) {
            $query->where('base_price_economy', '<=', (float) $request->budget_max);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) =>
                $q->where('name','like',"%$s%")
                  ->orWhere('city','like',"%$s%")
                  ->orWhere('country','like',"%$s%")
                  ->orWhere('description','like',"%$s%")
            );
        }

        // Popularity calculation variables
        $sortOption = $request->sort ?? 'latest';

        if ($sortOption === 'rating') {
            $query->orderByDesc('avg_rating');
        } elseif ($sortOption === 'popular') {
            $query->orderByDesc('review_count');
        } elseif ($sortOption === 'budget') {
            $query->orderBy('base_price_economy');
        } elseif ($sortOption === 'trending') {
            $query->orderByDesc('is_featured')->orderByDesc('review_count');
        } else {
            $query->latest();
        }

        $destinations = $query->paginate(9)->withQueryString();

        // Intelligent Recommendation Logic
        $recommended = collect();
        if (auth()->check() && !$request->filled('search') && !$request->filled('category') && !$request->filled('country')) {
            $user = auth()->user();
            $wishlistedDestIds = \App\Models\Wishlist::where('user_id', $user->id)->pluck('destination_id');
            $favCategory = Destination::whereIn('_id', $wishlistedDestIds)->pluck('category')->countBy()->sortDesc()->keys()->first();
            
            if (!$favCategory) {
                $favCategory = 'beaches'; // Default fallback
            }

            $recommended = Destination::active()
                ->where('category', $favCategory)
                ->orderByDesc('avg_rating')
                ->limit(3)
                ->get();
                
            if ($recommended->count() < 3) {
                $recommended = Destination::active()->featured()->get()->shuffle()->take(3);
            }
        } elseif (!$request->filled('search')) {
            $recommended = Destination::active()->featured()->orderByDesc('avg_rating')->limit(3)->get();
        }

        // Apply Budget Breakdown & Dynamic Popularity
        $destinations->getCollection()->transform(function($dest) {
            $simulatedBookings = rand(50, 500);
            $searchFrequency = rand(100, 1000);
            $dest->popularity_score = ($dest->avg_rating * 10) + ($dest->review_count * 2) + ($simulatedBookings * 5) + ($searchFrequency * 0.1);
            
            $base = $dest->base_price_economy ?? 15000;
            $dest->budget_breakdown = [
                'accommodation' => $base * 0.40,
                'transport'     => $base * 0.25,
                'food'          => $base * 0.25,
                'activity'      => $base * 0.10,
            ];
            return $dest;
        });

        // AI Generation logic — triggers on any search with no results (ignores other filter constraints)
        $searchTerm = $request->search;
        $noResultsForSearch = $request->filled('search') && Destination::active()
            ->where(fn($q) => $q->where('name','like',"%{$searchTerm}%")
                ->orWhere('city','like',"%{$searchTerm}%")
                ->orWhere('country','like',"%{$searchTerm}%")
                ->orWhere('description','like',"%{$searchTerm}%")
            )->doesntExist();

        if ($noResultsForSearch) {
            $s = $searchTerm;

            // ── Step 1: Try Gemini AI ────────────────────────────────────────
            $data = null;
            $prompt = "Provide a JSON object for a travel destination matching '{$s}'. Fields: 'name' (city or place name), 'city' (string), 'country' (string), 'description' (2 engaging sentences about the place), 'category' (one of: urban/beaches/mountains/adventure/heritage/nature/relaxation), 'best_season' (e.g. 'Oct-Mar'), 'base_price_economy' (integer INR per day, approx 2000-8000), 'tags' (array of 4 keywords), 'climate' (one word e.g. Tropical). Return ONLY the raw JSON object, no markdown or text.";
            $jsonStr = \App\Services\GeminiService::generateText($prompt);

            if ($jsonStr) {
                $jsonStr = trim(str_replace(['```json', '```'], '', $jsonStr));
                $parsed = json_decode($jsonStr, true);
                if (is_array($parsed) && isset($parsed['name'])) {
                    $data = $parsed;
                }
            }

            // ── Step 2: Smart Fallback Knowledge Base (when Gemini quota exhausted) ──
            if (!$data) {
                $knowledgeBase = [
                    'mumbai'     => ['name'=>'Mumbai','city'=>'Mumbai','country'=>'India','description'=>'The financial capital of India, Mumbai is a dazzling blend of colonial architecture, Bollywood glamour, and vibrant street life. From the iconic Gateway of India to the bustling Dharavi lanes, it never sleeps.','category'=>'urban','best_season'=>'Nov-Feb','base_price_economy'=>4500,'tags'=>['bollywood','gateway','marine-drive','street-food'],'climate'=>'Tropical'],
                    'delhi'      => ['name'=>'Delhi','city'=>'Delhi','country'=>'India','description'=>'India\'s sprawling capital is a living museum where Mughal monuments meet ultra-modern malls. Explore the Red Fort, Qutub Minar and Old Delhi\'s legendary spice markets.','category'=>'heritage','best_season'=>'Oct-Mar','base_price_economy'=>3500,'tags'=>['history','monument','food','culture'],'climate'=>'Semi-arid'],
                    'jaipur'     => ['name'=>'Jaipur','city'=>'Jaipur','country'=>'India','description'=>'The Pink City dazzles with its rose-hued palaces, bustling bazaars, and grand forts perched on Aravalli hills. A crown jewel of India\'s Golden Triangle.','category'=>'heritage','best_season'=>'Oct-Mar','base_price_economy'=>3200,'tags'=>['palace','rajputana','amber-fort','pink-city'],'climate'=>'Semi-arid'],
                    'goa'        => ['name'=>'Goa','city'=>'Panaji','country'=>'India','description'=>'India\'s beach paradise boasts pristine coastlines, Portuguese-era churches, vibrant nightlife, and fresh seafood. Whether you seek serenity or celebration, Goa delivers.','category'=>'beaches','best_season'=>'Nov-Feb','base_price_economy'=>5000,'tags'=>['beach','nightlife','seafood','portuguese'],'climate'=>'Tropical'],
                    'kerala'     => ['name'=>'Kerala','city'=>'Kochi','country'=>'India','description'=>'God\'s Own Country enchants with tranquil backwaters, lush tea estates, and Ayurvedic wellness retreats. Cruise the Alleppey houseboats for an unforgettable experience.','category'=>'nature','best_season'=>'Sep-Mar','base_price_economy'=>4000,'tags'=>['backwaters','ayurveda','houseboat','spices'],'climate'=>'Tropical'],
                    'agra'       => ['name'=>'Agra','city'=>'Agra','country'=>'India','description'=>'Home to the iconic Taj Mahal — one of the Seven Wonders of the World — Agra is a city where Mughal grandeur is frozen in gleaming white marble.','category'=>'heritage','best_season'=>'Oct-Mar','base_price_economy'=>2800,'tags'=>['taj-mahal','mughal','monument','unesco'],'climate'=>'Semi-arid'],
                    'varanasi'   => ['name'=>'Varanasi','city'=>'Varanasi','country'=>'India','description'=>'One of the world\'s oldest living cities, Varanasi sits on the sacred Ganges river. Witness ancient ghats, mesmerising evening aarti ceremonies, and timeless spiritual rituals.','category'=>'heritage','best_season'=>'Oct-Mar','base_price_economy'=>2500,'tags'=>['spiritual','ganges','ghats','holy'],'climate'=>'Humid subtropical'],
                    'manali'     => ['name'=>'Manali','city'=>'Manali','country'=>'India','description'=>'Nestled in the Himalayas, Manali is an adventure haven offering snow-capped peaks, roaring rivers, skiing slopes, and enchanting apple orchards.','category'=>'mountains','best_season'=>'Mar-Jun','base_price_economy'=>3800,'tags'=>['skiing','himalayas','adventure','snow'],'climate'=>'Alpine'],
                    'darjeeling' => ['name'=>'Darjeeling','city'=>'Darjeeling','country'=>'India','description'=>'Perched in the Eastern Himalayas, Darjeeling is famous for its world-class tea gardens, the toy train, and breathtaking sunrise views over Kanchenjunga.','category'=>'mountains','best_season'=>'Mar-May','base_price_economy'=>3200,'tags'=>['tea','himalayas','toy-train','sunrise'],'climate'=>'Highland'],
                    'hampi'      => ['name'=>'Hampi','city'=>'Hampi','country'=>'India','description'=>'A UNESCO World Heritage Site, Hampi is a surreal landscape of boulder-strewn hills and the majestic ruins of the Vijayanagara Empire.','category'=>'heritage','best_season'=>'Oct-Feb','base_price_economy'=>2200,'tags'=>['ruins','unesco','vijayanagara','boulders'],'climate'=>'Tropical'],
                    'rishikesh'  => ['name'=>'Rishikesh','city'=>'Rishikesh','country'=>'India','description'=>'The Yoga Capital of the World sits where the Ganges descends from the Himalayas. Thrill seekers come for white-water rafting while seekers of peace find ashrams and meditation.','category'=>'adventure','best_season'=>'Sep-Jun','base_price_economy'=>2800,'tags'=>['yoga','rafting','ganges','spiritual'],'climate'=>'Subtropical'],
                    'udaipur'    => ['name'=>'Udaipur','city'=>'Udaipur','country'=>'India','description'=>'The City of Lakes enchants with its shimmering water palaces, serene lakes, and ornate havelis. Often called the Venice of the East, it radiates timeless romance.','category'=>'heritage','best_season'=>'Sep-Mar','base_price_economy'=>4200,'tags'=>['lake','palace','romance','rajputana'],'climate'=>'Semi-arid'],
                    'ooty'       => ['name'=>'Ooty','city'=>'Ooty','country'=>'India','description'=>'The Queen of Hill Stations, Ooty captivates with its rolling tea gardens, botanical wonders, and cool misty climate high in the Nilgiri Hills of Tamil Nadu.','category'=>'nature','best_season'=>'Apr-Jun','base_price_economy'=>3000,'tags'=>['hill-station','tea','nilgiris','nature'],'climate'=>'Subtropical highland'],
                    'shimla'     => ['name'=>'Shimla','city'=>'Shimla','country'=>'India','description'=>'The former summer capital of British India, Shimla is a charming hill town of Victorian architecture, pine forests, and magnificent Himalayan panoramas.','category'=>'mountains','best_season'=>'Mar-Jun','base_price_economy'=>3500,'tags'=>['british-era','snow','hills','heritage'],'climate'=>'Highland'],
                    'andaman'    => ['name'=>'Andaman Islands','city'=>'Port Blair','country'=>'India','description'=>'The Andaman Islands are a tropical paradise of pristine turquoise waters, vibrant coral reefs, and white sand beaches, offering world-class diving and snorkelling.','category'=>'beaches','best_season'=>'Nov-May','base_price_economy'=>6000,'tags'=>['islands','diving','coral','tropical'],'climate'=>'Tropical'],
                    'mysore'     => ['name'=>'Mysore','city'=>'Mysore','country'=>'India','description'=>'The City of Palaces is famed for its opulent Mysore Palace, fragrant sandalwood, traditional silk sarees, and the grand Dasara festival celebrations.','category'=>'heritage','best_season'=>'Oct-Feb','base_price_economy'=>2800,'tags'=>['palace','silk','sandalwood','heritage'],'climate'=>'Tropical'],
                    'kolkata'    => ['name'=>'Kolkata','city'=>'Kolkata','country'=>'India','description'=>'The City of Joy, Kolkata is a vibrant metropolis of colonial grandeur, literary culture, and unmatched street food. Home to the Victoria Memorial and the Howrah Bridge.','category'=>'urban','best_season'=>'Oct-Feb','base_price_economy'=>2800,'tags'=>['culture','victoria-memorial','street-food','colonial'],'climate'=>'Tropical'],
                    'chennai'    => ['name'=>'Chennai','city'=>'Chennai','country'=>'India','description'=>'Gateway to South India, Chennai combines Dravidian temple culture, golden Marina Beach — one of the world\'s longest — and a thriving culinary scene.','category'=>'urban','best_season'=>'Nov-Feb','base_price_economy'=>3200,'tags'=>['temple','marina-beach','south-india','culture'],'climate'=>'Tropical'],
                    'bangalore'  => ['name'=>'Bangalore','city'=>'Bangalore','country'=>'India','description'=>'India\'s Silicon Valley is a cosmopolitan city of tech campuses, microbreweries, beautiful parks, and a year-round pleasant climate.','category'=>'urban','best_season'=>'Oct-Feb','base_price_economy'=>4000,'tags'=>['tech','nightlife','parks','cosmopolitan'],'climate'=>'Tropical highland'],
                    'hyderabad'  => ['name'=>'Hyderabad','city'=>'Hyderabad','country'=>'India','description'=>'The City of Pearls is where the Charminar stands proud, the legendary biryani is served, and a booming tech scene meets Nizami heritage.','category'=>'urban','best_season'=>'Oct-Feb','base_price_economy'=>3200,'tags'=>['biryani','charminar','pearls','nawabs'],'climate'=>'Semi-arid'],
                    'amritsar'   => ['name'=>'Amritsar','city'=>'Amritsar','country'=>'India','description'=>'Home to the magnificent Golden Temple, Sikhism\'s holiest shrine, Amritsar is a city of profound spirituality, fierce history, and the famous langar community feast.','category'=>'heritage','best_season'=>'Oct-Mar','base_price_economy'=>2500,'tags'=>['golden-temple','sikh','spiritual','wagah'],'climate'=>'Semi-arid'],
                    'leh'        => ['name'=>'Leh Ladakh','city'=>'Leh','country'=>'India','description'=>'A high-altitude desert of dramatic landscapes, ancient monasteries, and pristine lakes. Leh-Ladakh is the ultimate frontier for adventure travellers and motorcycle enthusiasts.','category'=>'adventure','best_season'=>'Jun-Sep','base_price_economy'=>5000,'tags'=>['ladakh','monastery','pangong','motorcycle'],'climate'=>'Cold desert'],
                    'coorg'      => ['name'=>'Coorg','city'=>'Madikeri','country'=>'India','description'=>'Scotland of India, Coorg is a fragrant paradise of coffee plantations, misty waterfalls, and lush rainforests in the Western Ghats of Karnataka.','category'=>'nature','best_season'=>'Oct-Mar','base_price_economy'=>4500,'tags'=>['coffee','plantation','western-ghats','nature'],'climate'=>'Subtropical highland'],
                    'pushkar'    => ['name'=>'Pushkar','city'=>'Pushkar','country'=>'India','description'=>'A sacred lake town in Rajasthan where Hindu pilgrims, white temples, and the world-famous Camel Fair create a tapestry of colour and devotion.','category'=>'heritage','best_season'=>'Oct-Mar','base_price_economy'=>2000,'tags'=>['camel-fair','sacred-lake','temples','rajasthan'],'climate'=>'Semi-arid'],
                    'puri'       => ['name'=>'Puri','city'=>'Puri','country'=>'India','description'=>'Puri is one of Hinduism\'s Char Dham pilgrimage sites, home to the legendary Jagannath Temple and long stretches of golden beach along the Bay of Bengal.','category'=>'beaches','best_season'=>'Nov-Feb','base_price_economy'=>2200,'tags'=>['jagannath','beach','odisha','pilgrimage'],'climate'=>'Tropical'],
                    'kashmir'    => ['name'=>'Kashmir','city'=>'Srinagar','country'=>'India','description'=>'Paradise on Earth — the Kashmir Valley enchants with its flower-filled meadows, iconic Dal Lake houseboats, snow-draped peaks, and fragrant saffron fields.','category'=>'mountains','best_season'=>'Apr-Oct','base_price_economy'=>5500,'tags'=>['dal-lake','shikara','snow','paradise'],'climate'=>'Alpine'],
                ];

                $sl = strtolower(trim($s));
                $matched = null;

                // Exact or partial match
                foreach ($knowledgeBase as $key => $info) {
                    if (str_contains($sl, $key) || str_contains($key, $sl) ||
                        str_contains(strtolower($info['name']), $sl) ||
                        str_contains(strtolower($info['city']), $sl)) {
                        $matched = $info;
                        break;
                    }
                }

                // Generic fallback for anything not in the knowledge base
                if (!$matched) {
                    $matched = [
                        'name'               => ucwords($s),
                        'city'               => ucwords($s),
                        'country'            => 'India',
                        'description'        => ucwords($s) . ' is a fascinating destination with rich culture, unique local cuisine, and stunning scenery. It offers travellers a chance to explore hidden gems and create unforgettable memories.',
                        'category'           => 'urban',
                        'best_season'        => 'Oct-Mar',
                        'base_price_economy' => rand(2500, 5000),
                        'tags'               => ['travel', 'explore', 'culture', 'adventure'],
                        'climate'            => 'Tropical',
                    ];
                }

                $data = $matched;
            }

            // ── Step 3: Create the Destination ──────────────────────────────
            if ($data) {
                // Pick a smart Unsplash image based on category
                $imageMap = [
                    'beaches'     => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&q=80',
                    'mountains'   => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=800&q=80',
                    'heritage'    => 'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=800&q=80',
                    'nature'      => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=800&q=80',
                    'adventure'   => 'https://images.unsplash.com/photo-1551632811-561732d1e306?w=800&q=80',
                    'relaxation'  => 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?w=800&q=80',
                    'urban'       => 'https://images.unsplash.com/photo-1477959858617-67f85cf4f1df?w=800&q=80',
                ];
                $category = strtolower($data['category'] ?? 'urban');
                $imageUrl = $imageMap[$category] ?? $imageMap['urban'];

                $newDest = Destination::create([
                    'name'               => $data['name'],
                    'city'               => $data['city'] ?? $data['name'],
                    'country'            => $data['country'] ?? 'India',
                    'description'        => $data['description'] ?? 'A beautiful destination waiting to be explored.',
                    'category'           => $category,
                    'best_season'        => $data['best_season'] ?? 'Oct-Mar',
                    'base_price_economy' => (int)($data['base_price_economy'] ?? 3000),
                    'image_url'          => $imageUrl,
                    'avg_rating'         => round(rand(38, 49) / 10, 1),
                    'review_count'       => rand(50, 800),
                    'status'             => 'active',
                    'is_featured'        => true,
                    'tags'               => $data['tags'] ?? [],
                    'climate'            => $data['climate'] ?? 'Tropical',
                ]);

                if (auth()->check()) {
                    try {
                        $itineraryService = app(\App\Services\ItineraryService::class);
                        $days = 3;
                        $startDate = now()->addDays(7)->format('Y-m-d');
                        $budget = (int)($data['base_price_economy'] ?? 3000) * $days;

                        $plan = $itineraryService->generate(
                            userId: auth()->id(),
                            origin: 'Unknown',
                            destinationId: $newDest->id,
                            days: $days,
                            startDate: $startDate,
                            budget: $budget,
                            interests: [$category, 'heritage']
                        );

                        $itinerary = \App\Models\Itinerary::create([
                            'user_id'         => auth()->id(),
                            'destination_id'  => $newDest->id,
                            'title'           => 'Trip to ' . $newDest->name,
                            'start_date'      => $startDate,
                            'end_date'        => \Carbon\Carbon::parse($startDate)->addDays($days - 1)->format('Y-m-d'),
                            'duration_days'   => $days,
                            'budget'          => $budget,
                            'days'            => $plan['days'] ?? [],
                            'preferences'     => [
                                'origin'     => 'Unknown',
                                'financials' => $plan['financial_summary'] ?? null,
                                'currency'   => $plan['currency'] ?? ['symbol'=>'₹','code'=>'INR','rate'=>1],
                            ],
                            'status'          => 'planning',
                        ]);

                        return redirect()->route('itineraries.show', $itinerary)
                            ->with('success', '✨ ' . $newDest->name . ' has been discovered and added! Your AI trip plan is ready.');
                    } catch (\Exception $e) {
                        return redirect()->route('destinations.show', $newDest)
                            ->with('success', '✨ ' . $newDest->name . ' has been discovered and added to TravelMate!');
                    }
                } else {
                    return redirect()->route('destinations.show', $newDest)
                        ->with('success', '✨ ' . $newDest->name . ' has been discovered! Login to auto-generate a trip plan.');
                }
            }
        }

        $categories   = Destination::active()->distinct()->pluck('category')->filter();
        $countries    = Destination::active()->distinct()->pluck('country')->filter();

        return view('destinations.index', compact('destinations', 'categories', 'countries', 'recommended'));
    }

    public function show(Destination $destination)
    {
        $destination->load(['packages', 'hotels', 'reviews.user']);
        $relatedPackages = Package::active()
            ->where('destination_id', $destination->id)
            ->limit(3)->get();
        $reviews = Review::where('destination_id', $destination->id)
            ->notFlagged()->with('user')->latest()->paginate(5);

        $avgRatings = [
            'food'        => round($reviews->avg('food_rating'), 1),
            'cleanliness' => round($reviews->avg('cleanliness_rating'), 1),
            'safety'      => round($reviews->avg('safety_rating'), 1),
            'value'       => round($reviews->avg('value_rating'), 1),
        ];

        $isWishlisted = auth()->check()
            ? auth()->user()->wishlists()->where('destination_id', $destination->id)->exists()
            : false;

        return view('destinations.show', compact(
            'destination', 'relatedPackages', 'reviews', 'avgRatings', 'isWishlisted'
        ));
    }

    public function book(Destination $destination)
    {
        // Find a default package or create one for this destination
        $package = Package::firstOrCreate(
            [
                'destination_id' => $destination->id,
                'title' => "Discover " . $destination->name
            ],
            [
                'description' => "Experience the best of " . $destination->name . " with this exclusive travel package.",
                'package_type' => 'Standard',
                'duration_days' => 5,
                'duration_nights' => 4,
                'price_per_person' => $destination->base_price_economy ?? 5000,
                'discount_percent' => 0,
                'cancellation_policy' => 'Flexible cancellation up to 48 hours before travel.',
                'is_featured' => true,
                'status' => 'active'
            ]
        );

        return redirect()->route('bookings.create', ['package_id' => $package->id]);
    }
}
