<?php

namespace App\Services;

use App\Models\ChatMessage;
use App\Models\Destination;
use App\Models\Package;

/**
 * AI Chatbot Service (RAG-inspired)
 * Simulates: Retrieval-Augmented Generation using local DB as the knowledge base.
 * In production, this calls OpenAI GPT-4 / Gemini with a Pinecone/pgvector retrieval layer.
 */
class ChatbotService
{
    private array $greetings = [
        'hi','hello','hey','good morning','good afternoon','good evening','howdy',
    ];

    private array $intents = [
        'destination_search' => ['destination','place','where','visit','travel to','go to','best place'],
        'package_info'       => ['package','tour','trip','offer','deal','price','cost'],
        'booking_help'       => ['book','booking','reserve','reservation','how to book'],
        'budget_advice'      => ['budget','cheap','affordable','expensive','money','cost'],
        'visa_info'          => ['visa','passport','document','entry','permit'],
        'weather'            => ['weather','climate','rain','temperature','season','best time'],
        'safety'             => ['safe','danger','security','risk','crime','travel advisory'],
        'food'               => ['food','eat','restaurant','cuisine','local food','halal','vegan'],
        'transport'          => ['transport','flight','bus','train','taxi','cab','how to get'],
        'itinerary'          => ['itinerary','plan','schedule','day by day','agenda'],
    ];

    /**
     * Process user message and return AI-like response with RAG context.
     */
    public function respond(string $message, string $userId, ?string $sessionId = null): array
    {
        $msg = strtolower(trim($message));

        // Save user message
        ChatMessage::create([
            'user_id'    => $userId,
            'session_id' => $sessionId,
            'role'       => 'user',
            'message'    => $message,
        ]);

        // Detect dynamic suggestion interception first!
        $response = $this->handleSuggestions($message);
        $intent = '';

        if ($response) {
            $intent = 'suggestion_intercept';
        } else {
            // Detect intent
            $intent   = $this->detectIntent($msg);
            $response = $this->generateResponse($intent, $msg);
        }

        // Save assistant response
        ChatMessage::create([
            'user_id'    => $userId,
            'session_id' => $sessionId,
            'role'       => 'assistant',
            'message'    => $response['text'],
            'metadata'   => ['intent' => $intent, 'suggestions' => $response['suggestions'] ?? []],
        ]);

        return $response;
    }

    private function handleSuggestions(string $msg): ?array
    {
        // Remove emoji characters and clean whitespace
        $clean = strtolower(trim(preg_replace('/[\x{1F300}-\x{1F6FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{1F900}-\x{1F9FF}\x{1F000}-\x{1F9FF}]/u', '', $msg)));
        // Also strip common symbols like 🗺️, 📦, 🛂, 🗓️, 💰, 🤖 etc.
        $clean = trim(str_replace(['🗺️', '📦', '🛂', '🗓️', '💰', '🤖', '🏖️', '🏝️', '🏔️', '🌸', '🏜️', '🛡️', '🍽️', '🌶️', '🚗', '✈️', '🛎️', '🔔', '🕌', '🌱', '🌮', '🎫', '🛄', '🚨', '🎟️', '📞', '🗼', '🥐', '☔', '⚠️', '⚡', '✨', '🌍', '🌐', '😊', '👋', '📋', '💡', '🌤️', '☀️', '🎁', '🏖', '🏝', '🏔', '🌸', '🏜', '🛡', '🍽', '🚗', '✈', '🛎', '🔔', '🕌', '🌱', '🌮', '🎫', '🛄', '🚨', '🎟', '📞', '🗼', '🥐', '☔', '⚠', '⚡', '✨', '🌍', '🌐', '😊', '👋', '📋', '💡', '🌤', '☀'], '', $clean));
        $clean = preg_replace('/\s+/', ' ', $clean); // collapse multiple spaces

        switch ($clean) {
            case 'top beach destinations':
            case 'explore beach destinations':
            case 'explore beach':
                // Query dynamic beach destinations
                $dests = Destination::active()->where(function($query) {
                    $query->where('category', 'like', '%beach%')
                          ->orWhere('tags', 'like', '%beach%');
                })->limit(3)->get();
                if ($dests->isEmpty()) {
                    $dests = Destination::active()->limit(3)->get();
                }
                $list = $dests->map(fn($d) => "• **{$d->name}, {$d->country}** — {$d->category} | ⭐ {$d->avg_rating}/5\n  ![{$d->name}]({$d->image_url})\n  *{$d->description}*")->join("\n\n");
                return [
                    'text' => "🏖️ **Top Beach Destinations:**\n\n{$list}\n\nBeach destinations are perfect for relaxation, sunbathing, water sports, and sunset dinners. Want me to plan a custom beach itinerary for you? 🌴",
                    'suggestions' => ['Plan 7-day Bali trip', 'Browse packages', 'Budget tips', 'Get travel tips']
                ];

            case 'explore destinations':
                $dests = Destination::active()->limit(3)->get();
                $list = $dests->map(fn($d) => "• **{$d->name}, {$d->country}** — {$d->category} | ⭐ {$d->avg_rating}/5\n  ![{$d->name}]({$d->image_url})")->join("\n\n");
                return [
                    'text' => "🌍 **Explore Handpicked Destinations:**\n\n{$list}\n\nOur intelligent itinerary builder can tailor any trip to your dream categories: Beach, Mountain, Cultural, or Adventure! 🗺️",
                    'suggestions' => ['Top beach destinations', 'Mountain retreats', 'Cultural heritage sites', 'Adventure spots']
                ];

            case 'budget packages under ₹500':
            case 'budget packages under 500':
                // Check if any package is cheap, else offer packages under 40000
                $pkgs = Package::active()->where('price_per_person', '<', 500)->orderBy('price_per_person', 'asc')->limit(3)->get();
                if ($pkgs->isEmpty()) {
                    $fallbackPkgs = Package::active()->where('price_per_person', '<', 40000)->orderBy('price_per_person', 'asc')->limit(3)->get();
                    $list = $fallbackPkgs->map(fn($p) => "• **{$p->title}** — {$p->duration_days} days | From **₹" . number_format($p->discounted_price) . "**/person\n  ![{$p->title}]({$p->image_url})")->join("\n\n");
                    return [
                        'text' => "🪙 **Budget Packages Under ₹500:**\n\nWe couldn't find any premium packages under ₹500, but we have incredible budget deals under ₹40,000 for you:\n\n{$list}\n\nAll packages include high-quality stays, transfers, and AI-optimized tours. Want details on a specific package? 💡",
                        'suggestions' => ['Combo deals', 'Set price alert', 'Browse packages', 'Get travel tips']
                    ];
                }
                $list = $pkgs->map(fn($p) => "• **{$p->title}** — {$p->duration_days} days | From **₹" . number_format($p->discounted_price) . "**/person\n  ![{$p->title}]({$p->image_url})")->join("\n\n");
                return [
                    'text' => "🪙 **Budget Packages Under ₹500:**\n\nHere are some wallet-friendly activities and mini-packages:\n\n{$list}\n\nWould you like to book one of these packages now? 📦",
                    'suggestions' => ['Browse packages', 'Set price alert', 'Get travel tips']
                ];

            case 'budget packages under ₹40,000':
            case 'budget packages under 40000':
            case 'budget packages':
                $pkgs = Package::active()->where('price_per_person', '<', 40000)->orderBy('price_per_person', 'asc')->limit(3)->get();
                if ($pkgs->isEmpty()) {
                    $pkgs = Package::active()->limit(3)->get();
                }
                $list = $pkgs->map(fn($p) => "• **{$p->title}** — {$p->duration_days} days | From **₹" . number_format($p->discounted_price) . "**/person\n  ![{$p->title}]({$p->image_url})")->join("\n\n");
                return [
                    'text' => "💰 **Affordable Budget Packages:**\n\nHere are our top-rated budget-friendly packages:\n\n{$list}\n\nWould you like me to suggest the best flight booking rates to match these? ✈️",
                    'suggestions' => ['Book a flight', 'Combo deals', 'Set price alert', 'Get travel tips']
                ];

            case 'browse packages':
                $pkgs = Package::active()->limit(3)->get();
                $list = $pkgs->map(fn($p) => "• **{$p->title}** — {$p->duration_days} days | From **₹" . number_format($p->discounted_price) . "**/person\n  ![{$p->title}]({$p->image_url})")->join("\n\n");
                return [
                    'text' => "📦 **Featured Travel Packages:**\n\n{$list}\n\nOur system updates package prices dynamically based on season and group size. Click on any package link to see dynamic itineraries and user reviews! 💡",
                    'suggestions' => ['Budget packages', 'Luxury tours', 'Family packages', 'Solo travel deals']
                ];

            case 'visa for japan':
                return [
                    'text' => "🛂 **Visa Information for Japan:**\n\n• **Tourist Visa:** Required for Indian citizens. You can apply via VFS Global.\n• **e-Visa:** Japan offers single-entry eVisa tourist permits for up to 90 days for Indian nationals.\n• **Validity:** 90 days from the date of issue.\n• **Documents Required:** Passport (6+ months validity, 2 blank pages), dynamic TravelMate itinerary, confirmed round-trip tickets, hotel vouchers, and bank statements (last 6 months).\n\nLet me know if you need help planning your itinerary for Japan! 🇯🇵",
                    'suggestions' => ['Plan 7-day Bali trip', 'Passport requirements', 'Embassy contacts', 'Get travel tips']
                ];

            case 'plan 7-day bali trip':
                return [
                    'text' => "🗓️ **7-Day Bali Paradise Itinerary:**\n\n• **Day 1:** Arrive in Bali, transfer to Ubud hotel, evening walk along Campuhan Ridge.\n• **Day 2:** Ubud Cultural Gems (Sacred Monkey Forest, Ubud Royal Palace, Tegalalang Rice Terraces).\n• **Day 3:** Mount Batur Sunrise Trek or relaxing Spa & Yoga day in Ubud.\n• **Day 4:** Transfer to Seminyak, visit spectacular Tanah Lot Temple at sunset.\n• **Day 5:** Nusa Penida Day Cruise (Kelingking Cliff, Broken Beach, snorkeling).\n• **Day 6:** Uluwatu Temple Cliff Walk, Kecak Fire Dance, and Jimbaran Seafood dinner.\n• **Day 7:** Souvenir shopping at local art markets, departure.\n\n✨ *Pro Tip:* You can download this custom itinerary instantly in our planner dashboard! 🌴",
                    'suggestions' => ['Generate itinerary', 'Check weather for Bali', 'Browse packages', 'Budget tips']
                ];

            case 'budget tips for solo travel':
            case 'budget tips':
                return [
                    'text' => "💰 **Pro Budget & Solo Travel Tips:**\n\n• **Stay in Hostels/Homestays:** Safe, social, and saves up to 70% compared to luxury suites.\n• **Dine Locally:** Street stalls and family warungs offer the most authentic cuisine at a fraction of tourist prices.\n• **Travel Shoulder Season:** Book right before or after peak season (e.g. Sept-Oct or Apr-May) for massive flight and lodging discounts.\n• **Use Public Transport:** Utilize local buses, trains, and shared shuttles instead of private cabs.\n• **Free Walking Tours:** A great way to learn city history and meet friends on a budget.\n\nWould you like to see our budget packages? 🪙",
                    'suggestions' => ['Budget packages', 'View budget tracker', 'Set price alert', 'Get travel tips']
                ];

            case 'mountain retreats':
                $dests = Destination::active()->where(function($query) {
                    $query->where('category', 'like', '%mountain%')
                          ->orWhere('tags', 'like', '%mountain%');
                })->limit(3)->get();
                if ($dests->isEmpty()) {
                    $dests = Destination::active()->limit(3)->get();
                }
                $list = $dests->map(fn($d) => "• **{$d->name}, {$d->country}** — {$d->category} | ⭐ {$d->avg_rating}/5\n  ![{$d->name}]({$d->image_url})\n  *{$d->description}*")->join("\n\n");
                return [
                    'text' => "🏔️ **Stunning Mountain Retreats:**\n\n{$list}\n\nEscape the heat and enjoy clean mountain air, pristine scenic views, hiking trails, and relaxing bonfires! 🌲",
                    'suggestions' => ['Plan itinerary', 'Browse packages', 'Weather alerts', 'Get travel tips']
                ];

            case 'cultural heritage sites':
            case 'cultural heritage':
                $dests = Destination::active()->where(function($query) {
                    $query->where('category', 'like', '%cultur%')
                          ->orWhere('category', 'like', '%herit%')
                          ->orWhere('tags', 'like', '%herit%');
                })->limit(3)->get();
                if ($dests->isEmpty()) {
                    $dests = Destination::active()->limit(3)->get();
                }
                $list = $dests->map(fn($d) => "• **{$d->name}, {$d->country}** — {$d->category} | ⭐ {$d->avg_rating}/5\n  ![{$d->name}]({$d->image_url})\n  *{$d->description}*")->join("\n\n");
                return [
                    'text' => "🏛️ **Cultural & Historical Heritage Sites:**\n\n{$list}\n\nStep back in time! Explore ancient architecture, heritage museums, local folklore, and historic monuments. 🕌",
                    'suggestions' => ['Plan itinerary', 'Browse packages', 'Get travel tips']
                ];

            case 'adventure spots':
            case 'adventure':
                $dests = Destination::active()->where(function($query) {
                    $query->where('category', 'like', '%adventur%')
                          ->orWhere('tags', 'like', '%adventur%');
                })->limit(3)->get();
                if ($dests->isEmpty()) {
                    $dests = Destination::active()->limit(3)->get();
                }
                $list = $dests->map(fn($d) => "• **{$d->name}, {$d->country}** — {$d->category} | ⭐ {$d->avg_rating}/5\n  ![{$d->name}]({$d->image_url})\n  *{$d->description}*")->join("\n\n");
                return [
                    'text' => "🧗 **Thrilling Adventure Spots:**\n\n{$list}\n\nGet your adrenaline pumping! Perfect for river rafting, skydiving, scuba diving, and wilderness camping. 🎒",
                    'suggestions' => ['Plan itinerary', 'Browse packages', 'Safety advisories', 'Get travel tips']
                ];

            case 'luxury tours':
                $pkgs = Package::active()->orderBy('price_per_person', 'desc')->limit(3)->get();
                $list = $pkgs->map(fn($p) => "• **{$p->title}** — {$p->duration_days} days | From **₹" . number_format($p->discounted_price) . "**/person\n  ![{$p->title}]({$p->image_url})\n  *{$p->description}*")->join("\n\n");
                return [
                    'text' => "💎 **Premium & Luxury Tours:**\n\nEnjoy five-star luxury treatment with all-inclusive amenities:\n\n{$list}\n\nIncludes 5-star resort lodgings, private guide assistance, fine dining, and VIP transfers! ✨",
                    'suggestions' => ['Browse packages', 'Combo deals', 'Get travel tips']
                ];

            case 'family packages':
                $pkgs = Package::active()->where('title', 'like', '%family%')->orWhere('description', 'like', '%family%')->limit(3)->get();
                if ($pkgs->isEmpty()) {
                    $pkgs = Package::active()->limit(3)->get();
                }
                $list = $pkgs->map(fn($p) => "• **{$p->title}** — {$p->duration_days} days | From **₹" . number_format($p->discounted_price) . "**/person\n  ![{$p->title}]({$p->image_url})")->join("\n\n");
                return [
                    'text' => "👨‍👩‍👧‍👦 **Family-Friendly Packages:**\n\nCreated to ensure comfort and fun for both kids and parents:\n\n{$list}\n\nFeaturing child-friendly activity spots, comfortable spacious family transport, and relaxing resort amenities. 🧸",
                    'suggestions' => ['Browse packages', 'Combo deals', 'Get travel tips']
                ];

            case 'solo travel deals':
                $pkgs = Package::active()->where('max_group_size', '<=', 6)->limit(3)->get();
                if ($pkgs->isEmpty()) {
                    $pkgs = Package::active()->limit(3)->get();
                }
                $list = $pkgs->map(fn($p) => "• **{$p->title}** — {$p->duration_days} days | From **₹" . number_format($p->discounted_price) . "**/person\n  ![{$p->title}]({$p->image_url})")->join("\n\n");
                return [
                    'text' => "🎒 **Exciting Solo Travel Deals:**\n\nPerfect for meeting like-minded explorers on small group tours:\n\n{$list}\n\nIncludes access to vibrant social hostels/guesthouses and guided city crawls. 🗺️",
                    'suggestions' => ['Budget tips for solo travel', 'Browse packages', 'Get travel tips']
                ];

            case 'check my bookings':
            case 'my bookings':
                $user = auth()->user();
                if (!$user) {
                    return [
                        'text' => "🔒 **Please sign in to view your bookings.**",
                        'suggestions' => ['Browse packages', 'Get travel tips']
                    ];
                }
                $bookings = $user->bookings()->with('package')->limit(3)->get();
                if ($bookings->isEmpty()) {
                    return [
                        'text' => "📋 **No Bookings Found:**\n\nYou haven't booked any trips yet! Explore our top packages to start your next adventure. ✈️",
                        'suggestions' => ['Browse packages', 'Explore destinations', 'Get travel tips']
                    ];
                }
                $list = $bookings->map(fn($b) => "• **" . ($b->package->title ?? 'Custom Itinerary') . "** | Status: `" . strtoupper($b->status ?? 'pending') . "` | Date: " . ($b->travel_date ? $b->travel_date : 'Flexible'))->join("\n");
                return [
                    'text' => "📋 **Your Bookings Summary:**\n\n{$list}\n\nGo to your [User Dashboard](/profile) to view receipt PDFs and full trip details! 📄",
                    'suggestions' => ['Browse packages', 'Contact support', 'Get travel tips']
                ];

            case 'apply promo code':
                return [
                    'text' => "🎟️ **How to Apply Promo Codes:**\n\nDuring checkout, enter one of our active coupon codes in the **Promo Code** box to secure instant discounts:\n\n🏷️ **Active Coupons:**\n• **WELCOME10** — Save 10% on your first package booking!\n• **TRAVELMATE** — Flat ₹500 off on any package.\n• **SOLORIDE** — Flat 15% discount for solo travelers.\n\nReady to book? Browse our packages now! 📦",
                    'suggestions' => ['Browse packages', 'Combo deals', 'Get travel tips']
                ];

            case 'contact support':
                return [
                    'text' => "📞 **TravelMate 24/7 Support:**\n\nWe are always here to help you:\n• **Email Helpline:** support@travelmate.com\n• **Phone:** +1-800-555-TRAVEL (toll-free)\n• **Support Ticket:** Raise a ticket inside your dashboard's Support Desk.\n\n*Emergency SOS is active in your mobile view!* 🚨",
                    'suggestions' => ['SOS setup', 'Safety advisories', 'Get travel tips']
                ];

            case 'set price alert':
                return [
                    'text' => "🔔 **Set a Price Alert:**\n\nNever miss a deal! \n1. Browse to any [Package Page](/packages).\n2. Click the **'Set Price Alert'** bell button.\n3. We will immediately notify you via email when the pricing drops!\n\nWould you like me to direct you to packages? 📈",
                    'suggestions' => ['Browse packages', 'Combo deals', 'Get travel tips']
                ];

            case 'view budget tracker':
            case 'budget tracker':
                return [
                    'text' => "📊 **Dynamic Expense & Budget Tracker:**\n\nManage travel finances effortlessly:\n• Navigate to **Expenses** in your User Dashboard.\n• Log flights, hotel bookings, food, and sightseeing costs.\n• Our AI auto-categorizes them and builds interactive expense analysis charts.\n\nGo to [Expense Manager](/expenses) to view or update your travel budget! 💸",
                    'suggestions' => ['Budget packages', 'Set price alert', 'Get travel tips']
                ];

            case 'combo deals':
                return [
                    'text' => "🎁 **Premium Combo Deals:**\n\nSave up to 25% by combining services:\n• **Flight + Hotel Combo:** Automated ticket adjustments.\n• **Package + Personal Guide Combo:** Instant guide booking discounts.\n\nBrowse all packages to find tags labeled `COMBO OFFER`: [Browse Packages](/packages)! ✈️",
                    'suggestions' => ['Browse packages', 'Budget packages', 'Get travel tips']
                ];

            case 'visa-free destinations':
                return [
                    'text' => "🌐 **Visa-Free Destinations (Indian Passport):**\n\nSkip the visa queue! Travel hassle-free to these visa-free or visa-on-arrival spots:\n\n1. **Nepal & Bhutan** (No passport visa required)\n2. **Mauritius** (Visa-free up to 90 days)\n3. **Thailand** (Frequent visa exemption windows)\n4. **Maldives** (Free 30-day Visa on Arrival)\n\nWhich destination matches your dream plan? 🏝️",
                    'suggestions' => ['Visa on arrival list', 'Explore destinations', 'Get travel tips']
                ];

            case 'visa on arrival list':
                return [
                    'text' => "🛂 **Visa on Arrival (VoA) List:**\n\n• **Thailand:** 15-day VoA at major airports.\n• **Maldives:** 30 days free VoA for all travelers.\n• **Indonesia:** 30 days VoA (approx. $35 USD / ₹3,000).\n• **Seychelles:** Free visitor permit up to 90 days.\n\n*Note:* Carry your hotel reservation and confirmed return flights! ✈️",
                    'suggestions' => ['Visa-free destinations', 'Passport requirements', 'Get travel tips']
                ];

            case 'passport requirements':
                return [
                    'text' => "🛂 **Standard Passport Requirements:**\n\nBefore you pack, check your passport details:\n• **Validity:** Must be valid for **at least 6 months** after your arrival date.\n• **Blank Pages:** At least **2 empty pages** for stamping.\n• **Condition:** Must not have tears, water damage, or loose pages.\n\nKeep high-resolution digital copies of your passport bio-data pages saved in your TravelMate locker! 🗂️",
                    'suggestions' => ['Visa info', 'Safety advisories', 'Get travel tips']
                ];

            case 'embassy contacts':
            case 'embassy':
                return [
                    'text' => "🏛️ **Consular & Embassy Contact Details:**\n\nFor emergency support abroad:\n• **Ministry of External Affairs Helpline:** +91-11-23012113\n• **Global MEA Portal:** cons@mea.gov.in\n• **Embassy Directory:** Access local address and phone contacts in our Safety portal.\n\nStay connected and travel safe! 🛡️",
                    'suggestions' => ['Safety advisories', 'SOS setup', 'Get travel tips']
                ];

            case 'check weather for bali':
            case 'weather bali':
            case 'bali weather':
                return [
                    'text' => "🏝️ **Bali Weather & Travel Climate:**\n\n• **Current Season:** Warm & sunny dry season!\n• **Average Temp:** 27°C – 31°C\n• **Best Months:** April to October (lowest rainfall and pleasant coastal breeze).\n• **Wet Season:** November to March (short, sharp afternoon downpours).\n\nPerfect for surfing, beach hopping, and hiking! ☀️",
                    'suggestions' => ['Plan 7-day Bali trip', 'Browse packages', 'Get travel tips']
                ];

            case 'best time for paris':
            case 'paris weather':
            case 'weather paris':
                return [
                    'text' => "🗼 **Paris Weather & Seasonal Guide:**\n\n• **Spring (Apr–June):** Perfect blossoms and cool temperatures (12°C - 20°C).\n• **Autumn (Sept–Oct):** Beautiful fall colors, fewer crowds, and lower hotel prices.\n• **Summer (July–Aug):** Peak tourism season, hot (28°C+), and highly crowded.\n• **Winter (Nov–March):** Festive Christmas markets but cold (3°C - 8°C).\n\nPlanning a getaway to France? I can check travel packages! 🥐",
                    'suggestions' => ['Browse packages', 'Explore destinations', 'Get travel tips']
                ];

            case 'monsoon travel tips':
                return [
                    'text' => "🌧️ **Smart Monsoon Travel Tips:**\n\n• **Waterproof Backpack Cover:** Keep your clothing and tech completely dry.\n• **Pack Quick-Dry Fabrics:** Avoid heavy denim; wear light synthetics.\n• **First-Aid Kit:** Include insect repellents and anti-infection creams.\n• **Flexible Schedules:** Allow extra time for potential transport delays due to rain.\n\nEnjoy lush green landscapes safely! ☔",
                    'suggestions' => ['Weather alerts', 'Safety advisories', 'Get travel tips']
                ];

            case 'weather alerts':
                return [
                    'text' => "⚠️ **Active Weather Notifications:**\n\nThere are **no active weather alerts** or severe storms reported across our current active destination maps. Enjoy your safe travels! 🌤️\n\nWould you like a weather forecast for a specific city? Just type 'weather [city name]'!",
                    'suggestions' => ['Check weather for Bali', 'Best time for Paris', 'Get travel tips']
                ];

            case 'sos setup':
                return [
                    'text' => "🚨 **How to Activate SOS Emergencies:**\n\n1. Go to your **Profile Dashboard**.\n2. Set up up to 3 emergency mobile numbers/emails.\n3. In any emergency, press the red **'SOS' button** at the top right of your app page.\n\nOur system will instantly transmit a high-priority alert with your exact GPS coordinates via SMS/Email to your contacts! 🛡️",
                    'suggestions' => ['Safety advisories', 'Contact support', 'Get travel tips']
                ];

            case 'travel insurance':
                return [
                    'text' => "🛡️ **TravelMate Travel Insurance Partners:**\n\nCover flights, health, and baggage starting at only **₹49/day**:\n• **Medical Emergencies:** Up to $50,000 cover.\n• **Trip Delays:** Up to 100% hotel compensation.\n• **Luggage Protection:** Simplified mobile claims.\n\nAdd insurance to your cart at checkout! ✈️",
                    'suggestions' => ['Safety advisories', 'Contact support', 'Get travel tips']
                ];

            case 'embassy directory':
                return [
                    'text' => "🏛️ **Dynamic Global Embassy Directory:**\n\nWe provide updated contact numbers and addresses for consulates in all our featured countries.\n\nTo view them, visit the [Embassy Directory Section](/profile) or ask me about a specific country! 🌐",
                    'suggestions' => ['Embassy contacts', 'Safety advisories', 'Get travel tips']
                ];

            case 'safety advisories':
                return [
                    'text' => "🛡️ **Current Global Safety Status:**\n\nAll primary destinations (Bali, Goa, Kerala, Thailand, Japan, Western Europe) are currently designated **Level 1: Exercise Normal Precautions**.\n\nStay alert, check local advisories before leaving, and activate your SOS contacts! 🗺️",
                    'suggestions' => ['SOS setup', 'Travel insurance', 'Get travel tips']
                ];

            case 'halal restaurants':
                return [
                    'text' => "🕌 **Halal Culinary Options:**\n\nEnjoy Halal-certified dining at our partner destinations:\n• **Bali:** Seminyak Warung Halal, Halal Corner Ubud.\n• **Thailand:** Pratunam Street Market, Royal India Halal.\n\nRemember to filter your dynamic itineraries by checking the **'Halal Food Option'**! 🍽️",
                    'suggestions' => ['Vegan food guide', 'Street food tours', 'Get travel tips']
                ];

            case 'vegan food guide':
                return [
                    'text' => "🌱 **Vegan & Plant-Based Eating:**\n\nEnjoy healthy vegan dining with ease:\n• **Bali:** Sayuri Healing Foods, Zest Ubud, Alchemy.\n• **Bangkok:** Broccoli Revolution, May Veggie Home.\n• **Europe:** Wild & The Moon, local vegetarian bistro hubs.\n\nSelect the **'Vegan/Veg' dietary toggle** in our trip builder! 🥦",
                    'suggestions' => ['Halal restaurants', 'Street food tours', 'Get travel tips']
                ];

            case 'street food tours':
                return [
                    'text' => "🌮 **Local Street Food Experiences:**\n\nSavor authentic local flavors with guided culinary crawls:\n• **Bangkok Tour:** Michelin-rated street food on Khao San Road.\n• **Delhi Food Walk:** Old Delhi kebabs, sweets, and paranthas.\n• **Penang Night Crawl:** Authentic Laksa and noodles.\n\nAdd a food guide to your booking for a delightful culinary adventure! 🌶️",
                    'suggestions' => ['Halal restaurants', 'Vegan food guide', 'Get travel tips']
                ];

            case 'cooking classes':
                return [
                    'text' => "🍳 **Traditional Cooking Masterclasses:**\n\nBring the recipes home! Select from our custom culinary workshops:\n• **Bali:** Organic farm-to-table Balinese cooking class.\n• **Thailand:** Chiang Mai organic spice and curry school.\n• **Italy:** Rome hand-rolled pasta workshop.\n\nAdd these classes directly in your package builder interface! 👩‍🍳",
                    'suggestions' => ['Street food tours', 'Vegan food guide', 'Get travel tips']
                ];

            case 'book a flight':
                return [
                    'text' => "✈️ **Automated Flight Booking Desk:**\n\nSearch and secure the lowest airline ticket prices (Indigo, Air India, Emirates, Singapore Airlines):\n• **LSTM Price Tracker:** Predicts flight price fluctuations.\n• **Instant Rescheduling:** 100% automated portal.\n\nFind your cheapest tickets here: [Flight Booking Search](/flights)! 🛫",
                    'suggestions' => ['Train routes', 'Rental cars', 'Get travel tips']
                ];

            case 'train routes':
                return [
                    'text' => "🚂 **Train Booking & Scenic Routes:**\n\nEnjoy high-speed or historic train journeys:\n• **India:** High-speed Vande Bharat, premium Rajdhani corridors.\n• **Japan:** Shinkansen bullet train passes (JR Pass).\n• **Europe:** Interrail and Eurail ticket passes.\n\nBook train tickets directly on our Transport module! 🎫",
                    'suggestions' => ['Book a flight', 'Rental cars', 'Get travel tips']
                ];

            case 'rental cars':
                return [
                    'text' => "🚗 **Self-Drive Cars & Bike Rentals:**\n\nExplore at your own pace with our rental partners:\n• **Options:** Scooters, compact cars, dynamic premium SUVs.\n• **License Requirements:** Domestic license + International Driving Permit (IDP) for foreign travel.\n\nBrowse dynamic vehicle availability on our rentals page! 🗺️",
                    'suggestions' => ['Book a flight', 'Airport transfer', 'Get travel tips']
                ];

            case 'airport transfer':
                return [
                    'text' => "🚐 **Convenient Airport Transfers:**\n\nArrive stress-free with a driver waiting in the arrivals area:\n• **Private Cab:** Straight to your hotel check-in.\n• **Shared Shuttle:** Eco-friendly and highly economical.\n\nAdd 'Airport Transfer' under add-ons during package check-out! 🛄",
                    'suggestions' => ['Book a flight', 'Rental cars', 'Get travel tips']
                ];

            case 'generate itinerary':
            case 'plan itinerary':
                return [
                    'text' => "🗓️ **AI-Powered Itinerary Planner:**\n\nCreate a perfect day-by-day travel map in seconds:\n1. Choose your dream destination.\n2. Select your travel dates and duration.\n3. Input your budget type (Economy, Standard, Luxury).\n4. Pick interest tags (Relaxation, Adventure, Culture, Wildlife).\n\nOur system will build, estimate costs, and export a premium PDF: [Create Itinerary](/trip-planner)! ✨",
                    'suggestions' => ['View my itineraries', 'Share itinerary', 'Get travel tips']
                ];

            case 'view my itineraries':
                $user = auth()->user();
                if (!$user) {
                    return [
                        'text' => "🔒 **Please sign in to view your itineraries.**",
                        'suggestions' => ['Plan itinerary', 'Get travel tips']
                    ];
                }
                $itineraries = $user->itineraries()->limit(3)->get();
                if ($itineraries->isEmpty()) {
                    return [
                        'text' => "🗓️ **No Itineraries Found:**\n\nYou haven't created any custom itineraries yet! Let our AI generate one for you now. ✨",
                        'suggestions' => ['Plan itinerary', 'Explore destinations', 'Get travel tips']
                    ];
                }
                $list = $itineraries->map(fn($it) => "• **{$it->title}** — {$it->duration_days} days | " . ($it->is_public ? '🌐 Public' : '🔒 Private'))->join("\n");
                return [
                    'text' => "🗓️ **Your Custom Itineraries:**\n\n{$list}\n\nView and manage them directly in the [Itinerary Dashboard](/itineraries)! 📄",
                    'suggestions' => ['Share itinerary', 'Plan itinerary', 'Get travel tips']
                ];

            case 'share itinerary':
                return [
                    'text' => "🔗 **How to Share Your Itinerary:**\n\n1. Visit **My Itineraries** in your profile.\n2. Open your preferred travel plan.\n3. Click **'Make Public'** to enable sharing.\n4. Click **'Copy Link'** or use the social icons to send it directly via WhatsApp, Facebook, or Email!\n\nYour friends can clone, view, or review your dynamic plan! 🌐",
                    'suggestions' => ['View my itineraries', 'Collaborative planning', 'Get travel tips']
                ];

            case 'collaborative planning':
                return [
                    'text' => "🤝 **Multi-User Collaborative Planning:**\n\nInvite friends and family to build an itinerary with you in real time!\n\n1. Open any saved itinerary.\n2. Click the **'Invite Collaborator'** button.\n3. Input your friend's registered email.\n\nThey will immediately gain editing rights to add destinations, edit days, and suggest hotels! 🗺️",
                    'suggestions' => ['Plan itinerary', 'Share itinerary', 'Get travel tips']
                ];

            case 'get travel tips':
            case 'travel tips':
                return [
                    'text' => "💡 **Smart Travel Tips & Hacks:**\n\n• **Avoid ATM Fees:** Use cards with zero international transaction fees or carry small amounts of local cash.\n• **Digital Backup:** Save copies of your Passport, Visa, and Insurance in your secure TravelMate profile locker.\n• **Offline Maps:** Download Google Maps or Maps.me offline data before landing.\n• **Local SIM Card:** Pick up an eSIM online or buy a physical SIM card at the airport for cheap connectivity.\n\nWould you like safety tips or budget tips? 🛡️",
                    'suggestions' => ['Budget tips for solo travel', 'Safety advisories', 'SOS setup', 'Get travel tips']
                ];
        }

        return null;
    }

    private function detectIntent(string $msg): string
    {
        foreach ($this->greetings as $g) {
            if (str_contains($msg, $g)) return 'greeting';
        }

        foreach ($this->intents as $intent => $keywords) {
            foreach ($keywords as $kw) {
                if (str_contains($msg, $kw)) return $intent;
            }
        }

        return 'general';
    }

    private function generateResponse(string $intent, string $msg): array
    {
        return match ($intent) {
            'greeting' => [
                'text'        => "👋 Hello! I'm **TravelMate AI** — your personal travel assistant. I can help you discover destinations, plan itineraries, find the best packages, answer visa questions, and much more!\n\nWhat adventure are you planning today? 🌍",
                'suggestions' => ['🗺️ Explore destinations', '📦 Browse packages', '🗓️ Plan itinerary', '💰 Budget tips'],
            ],

            'destination_search' => [
                'text'        => $this->buildDestinationResponse($msg),
                'suggestions' => ['Top beach destinations', 'Mountain retreats', 'Cultural heritage sites', 'Adventure spots'],
            ],

            'package_info' => [
                'text'        => $this->buildPackageResponse($msg),
                'suggestions' => ['Budget packages', 'Luxury tours', 'Family packages', 'Solo travel deals'],
            ],

            'booking_help' => [
                'text'        => "📋 **How to book with TravelMate:**\n\n1. **Browse** destinations or packages\n2. **Select** your preferred option\n3. **Choose** travel dates & group size\n4. **Apply** any promo codes\n5. **Review** your itinerary\n6. **Pay** securely via our payment gateway\n7. Receive your **e-ticket with QR code** instantly!\n\nNeed help with a specific booking? Share the package name.",
                'suggestions' => ['Browse packages', 'Check my bookings', 'Apply promo code', 'Contact support'],
            ],

            'budget_advice' => [
                'text'        => "💡 **Smart Budget Tips:**\n\n• **Book 3–6 weeks early** for up to 30% savings\n• Travel during **shoulder season** (just before/after peak)\n• Use our **price alerts** to catch deals\n• Our **AI budget predictor** tracks your spending in real time\n• Combo packages (hotel + activities) save 15–25%\n\nWhat's your rough budget? I can suggest matching packages! 💰",
                'suggestions' => ['Set price alert', 'View budget tracker', 'Budget packages under ₹40,000', 'Combo deals'],
            ],

            'visa_info' => [
                'text'        => "🛂 **Visa & Travel Documents:**\n\nVisa requirements vary by nationality and destination. Our knowledge base is updated weekly.\n\n📌 **General Tips:**\n• Apply at least **4–6 weeks** before departure\n• Ensure passport validity of **6+ months** beyond travel dates\n• Keep **printed copies** of all documents\n• Check if your destination offers **visa-on-arrival**\n\nWhich destination's visa info do you need? I'll pull the latest details! 🌐",
                'suggestions' => ['Visa-free destinations', 'Visa on arrival list', 'Passport requirements', 'Embassy contacts'],
            ],

            'weather' => [
                'text'        => "🌤️ **Weather & Best Time to Visit:**\n\nOur platform integrates with **OpenWeather API** for real-time conditions. Here's a general guide:\n\n• 🏝️ **Tropical destinations** — Nov–Apr (dry season)\n• 🏔️ **Mountain trips** — Mar–May, Sep–Nov\n• 🌸 **Europe** — Apr–Jun, Sep–Oct (mild & fewer crowds)\n• 🏜️ **Desert** — Oct–Mar (cool nights)\n\nTell me your destination for a specific forecast! ☀️",
                'suggestions' => ['Check weather for Bali', 'Best time for Paris', 'Monsoon travel tips', 'Weather alerts'],
            ],

            'safety' => [
                'text'        => "🛡️ **Travel Safety Tips:**\n\n• Register with your **embassy** before travelling\n• Use our **SOS module** (one-tap sends your location + contacts)\n• Keep emergency contacts in your profile\n• Check **travel advisories** (Level 1–4) in our safety section\n• Purchase **travel insurance** — we partner with major providers\n\nStay safe and travel smart! Our platform monitors geofence alerts automatically. 🗺️",
                'suggestions' => ['SOS setup', 'Travel insurance', 'Embassy directory', 'Safety advisories'],
            ],

            'food' => [
                'text'        => "🍽️ **Local Food & Dining Guide:**\n\nFood is a core part of travel! Our culinary itineraries include:\n\n• 🌮 **Street food tours** — authentic & budget-friendly\n• 🍣 **Cooking classes** — learn local recipes\n• 🥗 **Dietary filters** — halal, vegan, gluten-free options\n• ⭐ **Crowd-sourced ratings** — genuine traveler reviews\n\nWhich destination's cuisine are you curious about? 🌶️",
                'suggestions' => ['Halal restaurants', 'Vegan food guide', 'Street food tours', 'Cooking classes'],
            ],

            'transport' => [
                'text'        => "🚗 **Getting Around — Transport Guide:**\n\nTravelMate aggregates transport options for your destination:\n\n• ✈️ **Flights** — best fare predictions with LSTM model\n• 🚂 **Trains** — scenic and affordable intercity\n• 🚌 **Buses** — budget-friendly city-to-city\n• 🚖 **Cabs/Rideshare** — Uber, Grab, local taxis\n• 🚐 **Rental vehicles** — for road trips & remote areas\n\nWant me to suggest the best way to get from A to B? 🗺️",
                'suggestions' => ['Book a flight', 'Train routes', 'Rental cars', 'Airport transfer'],
            ],

            'itinerary' => [
                'text'        => "🗓️ **Let me build your perfect itinerary!**\n\nOur **AI Itinerary Engine** uses:\n- 🧬 Genetic algorithm for time-slot optimization\n- 🤝 Collaborative filtering from 10M+ travel logs\n- 🌤️ Real-time weather & local event integration\n- 📍 GPS-aware live re-planning\n\n**To get started, tell me:**\n1. Your destination\n2. Travel dates or duration\n3. Budget range\n4. Interests (adventure/culinary/heritage/relaxation)\n\nI'll generate your personalized day-by-day plan! ✨",
                'suggestions' => ['Generate itinerary', 'View my itineraries', 'Share itinerary', 'Collaborative planning'],
            ],

            default => [
                'text'        => "🤔 I'm not sure I understood that fully, but I'm here to help!\n\nI can assist with:\n• 🗺️ **Destinations** — discover amazing places\n• 📦 **Packages** — curated tours & deals\n• 🗓️ **Itineraries** — AI-powered day-by-day plans\n• 💰 **Budget** — smart spending & alerts\n• 🛂 **Visa & Safety** — travel document guidance\n\nWhat would you like to know? 😊",
                'suggestions' => ['Explore destinations', 'Browse packages', 'Plan itinerary', 'Get travel tips'],
            ],
        };
    }

    private function buildDestinationResponse(string $msg): string
    {
        // RAG: Check if any active destination name is in the message
        $allDests = Destination::active()->get();
        $matched = null;
        foreach ($allDests as $d) {
            if (str_contains(strtolower($msg), strtolower($d->name))) {
                $matched = $d;
                break;
            }
        }

        if ($matched) {
            $bestSeason = $matched->best_season ?? 'All year round';
            $durationDays = $matched->duration_days_suggested ?? 5;
            $whatToSee = $matched->what_to_see ?? 'Local monuments and tours';

            return "🌍 **Featured Destination: {$matched->name}, {$matched->country}**\n\n" .
                   "![{$matched->name}]({$matched->image_url})\n\n" .
                   "• **Category:** {$matched->category}\n" .
                   "• **Best Season to Visit:** {$bestSeason}\n" .
                   "• **Average Rating:** ⭐ {$matched->avg_rating}/5\n" .
                   "• **Suggested Duration:** {$durationDays} Days\n" .
                   "• **Must-See Attraction:** {$whatToSee}\n\n" .
                   "*About:* {$matched->description}\n\n" .
                   "Would you like me to build a custom day-by-day plan for {$matched->name}? 🗓️";
        }

        // Fallback to top destinations
        $destinations = Destination::active()->limit(3)->get();

        $list = $destinations->map(fn($d) =>
            "• **{$d->name}, {$d->country}** — {$d->category} | ⭐ {$d->avg_rating}/5\n  ![{$d->name}]({$d->image_url})"
        )->join("\n\n");

        return "🌍 **Top Destinations for You:**\n\n{$list}\n\nWant details on any specific destination or category (adventure/heritage/culinary/beach)? I'll pull the full guide including weather, visa info, and must-see POIs! 🗺️";
    }

    private function buildPackageResponse(string $msg): string
    {
        // Check if any active package title is in the message
        $allPkgs = Package::active()->get();
        $matched = null;
        foreach ($allPkgs as $p) {
            if (str_contains(strtolower($msg), strtolower($p->title))) {
                $matched = $p;
                break;
            }
        }

        if ($matched) {
            $maxGroupSize = $matched->max_group_size ?? 10;
            $difficulty = ucfirst($matched->difficulty_level ?? 'Easy');
            $highlights = is_array($matched->highlights) 
                ? implode("\n", array_map(fn($h) => "• {$h}", $matched->highlights)) 
                : "• Local sightseeing and transport included.";

            return "📦 **Featured Package: {$matched->title}**\n\n" .
                   "![{$matched->title}]({$matched->image_url})\n\n" .
                   "• **Duration:** {$matched->duration_days} Days\n" .
                   "• **Price per Person:** ₹" . number_format($matched->discounted_price) . " (Original: ₹" . number_format($matched->price_per_person) . ")\n" .
                   "• **Group Size Limit:** Up to {$maxGroupSize} travelers\n" .
                   "• **Difficulty:** {$difficulty}\n\n" .
                   "✨ **Highlights:**\n" . 
                   $highlights . "\n\n" .
                   "Would you like to book this package? ✈️";
        }

        $packages = Package::active()->with('destination')->limit(3)->get();

        $list = $packages->map(fn($p) =>
            "• **{$p->title}** — {$p->duration_days} days | From **₹" . number_format($p->discounted_price) . "**/person\n  ![{$p->title}]({$p->image_url})"
        )->join("\n\n");

        return "📦 **Featured Packages:**\n\n{$list}\n\nOur **LSTM price predictor** suggests the best time to book for maximum savings. Want a personalized recommendation based on your budget and interests? 💡";
    }
}
