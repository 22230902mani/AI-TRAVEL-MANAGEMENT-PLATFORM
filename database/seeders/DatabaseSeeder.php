<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\Hotel;
use App\Models\Package;
use App\Models\Promotion;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
// Removed Spatie imports

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Native Roles Array setup (No pivot tables needed) ─────────

        // ── Admin User ────────────────────────────────────────────────
        $admin = User::firstOrCreate(['email' => 'manilukka143@gmail.com'], [
            'name'     => 'Super Admin',
            'password' => Hash::make('mani@098'),
            'roles'    => ['super_admin', 'admin'],
        ]);
        if (!$admin->wasRecentlyCreated) {
            $admin->update(['password' => Hash::make('mani@098'), 'roles' => ['super_admin', 'admin']]);
        }
        UserProfile::firstOrCreate(['user_id' => $admin->id], [
            'loyalty_level' => 4,
            'total_points'  => 99999,
            'total_trips'   => 50,
        ]);

        // ── Demo Traveler ─────────────────────────────────────────────
        $traveler = User::firstOrCreate(['email' => 'traveler@travelmate.com'], [
            'name'     => 'Alex Journey',
            'password' => Hash::make('password'),
            'roles'    => ['traveler'],
        ]);
        if (!$traveler->wasRecentlyCreated) {
            $traveler->update(['roles' => ['traveler']]);
        }
        UserProfile::firstOrCreate(['user_id' => $traveler->id], [
            'phone'            => '+1-555-0142',
            'nationality'      => 'American',
            'bio'              => 'Adventure seeker, foodie, and photography lover. 30+ countries visited!',
            'travel_interests' => ['adventure','culinary','heritage'],
            'loyalty_level'    => 2,
            'total_points'     => 3450,
            'total_trips'      => 12,
        ]);

        // ── Destinations ──────────────────────────────────────────────
        $destinations = [
            [
                'name' => 'Bali', 'country' => 'Indonesia', 'city' => 'Denpasar',
                'description' => 'The Island of Gods — where terraced rice paddies, ancient temples, volcanic mountains, and pristine beaches create an unparalleled destination. Famous for its spiritual culture, world-class surfing, and vibrant nightlife.',
                'climate' => 'tropical', 'best_season' => 'Apr–Oct', 'category' => 'ecotourism',
                'avg_rating' => 4.8, 'review_count' => 2340, 'is_featured' => true,
                'image' => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=800',
                'tags' => ['beach','surfing','temples','spa','nightlife'],
                'safety_tips' => ['Respect temple dress codes','Bargain at local markets','Beware strong ocean currents'],
                'visa_info' => ['Visa on arrival for 30 days', 'Free for 70+ countries', 'Extension possible'],
            ],
            [
                'name' => 'Paris', 'country' => 'France', 'city' => 'Paris',
                'description' => 'The City of Light — Paris captivates with iconic landmarks, world-class art, haute cuisine, and effortless chic. From the Eiffel Tower to hidden cobblestone arrondissements, every corner tells a story.',
                'climate' => 'temperate', 'best_season' => 'Apr–Jun, Sep–Nov', 'category' => 'heritage',
                'avg_rating' => 4.7, 'review_count' => 5120, 'is_featured' => true,
                'image' => 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=800',
                'tags' => ['art','cuisine','fashion','romance','history'],
                'safety_tips' => ['Watch for pickpockets near tourist sites','Validate metro tickets always'],
                'visa_info' => ['Schengen visa required for non-EU','Apply 3 months in advance','90 days max stay'],
            ],
            [
                'name' => 'Tokyo', 'country' => 'Japan', 'city' => 'Tokyo',
                'description' => 'A mesmerizing blend of ultramodern and traditional — neon-lit skyscrapers beside serene Shinto shrines, anime culture alongside ancient tea ceremonies. Tokyo rewards every curious traveler.',
                'climate' => 'temperate', 'best_season' => 'Mar–May, Oct–Nov', 'category' => 'urban',
                'avg_rating' => 4.9, 'review_count' => 3890, 'is_featured' => true,
                'image' => 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=800',
                'tags' => ['anime','technology','food','culture','shopping'],
                'safety_tips' => ['Extremely safe city','Carry cash — many places don\'t accept cards','Follow quiet carriage rules on trains'],
                'visa_info' => ['Visa-free for 68 countries (90 days)','Apply online for others'],
            ],
            [
                'name' => 'Maldives', 'country' => 'Maldives', 'city' => 'Malé',
                'description' => 'An archipelago of 1,200 coral islands with the clearest turquoise waters on Earth. Overwater bungalows, vibrant coral reefs, and absolute luxury define this paradise. The ultimate honeymoon destination.',
                'climate' => 'tropical', 'best_season' => 'Nov–Apr', 'category' => 'relaxation',
                'avg_rating' => 4.9, 'review_count' => 1230, 'is_featured' => true,
                'image' => 'https://images.unsplash.com/photo-1506929562872-bb421503ef21?w=800',
                'tags' => ['luxury','diving','snorkeling','honeymoon','overwater-bungalow'],
                'safety_tips' => ['Respect local Islamic customs','Do not remove coral','Stay updated on resort transfer options'],
                'visa_info' => ['Free visa on arrival for 30 days','All nationalities welcome'],
            ],
            [
                'name' => 'Rajasthan', 'country' => 'India', 'city' => 'Jaipur',
                'description' => 'The Land of Kings — ancient forts, magnificent palaces, colorful bazaars, and vast Thar Desert. Rajasthan is India\'s most culturally vibrant state, home to some of the most spectacular royal architecture in the world.',
                'climate' => 'desert', 'best_season' => 'Oct–Mar', 'category' => 'heritage',
                'avg_rating' => 4.6, 'review_count' => 1870, 'is_featured' => true,
                'image' => 'https://images.unsplash.com/photo-1477587458883-47145ed94245?w=800',
                'tags' => ['forts','palaces','camels','desert','culture'],
                'safety_tips' => ['Book guides for fort complexes','Dress modestly at religious sites','Bargain at bazaars'],
                'visa_info' => ['e-Visa available for 160+ countries','Apply 4 days before travel','30/60/1-year options'],
            ],
            [
                'name' => 'Santorini', 'country' => 'Greece', 'city' => 'Fira',
                'description' => 'Iconic white-washed buildings and blue-domed churches perched on volcanic cliffs overlooking the Aegean Sea. Santorini offers magical sunsets, world-class wine, and unrivalled Mediterranean beauty.',
                'climate' => 'mediterranean', 'best_season' => 'Jun–Sep', 'category' => 'relaxation',
                'avg_rating' => 4.8, 'review_count' => 2100, 'is_featured' => true,
                'image' => 'https://images.unsplash.com/photo-1570077188670-e3a8d69ac5ff?w=800',
                'tags' => ['sunsets','wine','beaches','romance','photography'],
                'safety_tips' => ['Book ahead in peak summer','Wear non-slip shoes on cobblestone paths'],
                'visa_info' => ['Schengen visa required for non-EU','90 days max stay'],
            ],
        ];

        foreach ($destinations as $destData) {
            Destination::firstOrCreate(['name' => $destData['name']], $destData);
        }

        // ── Packages ──────────────────────────────────────────────────
        $packageData = [
            [
                'destination' => 'Bali',
                'title'   => 'Bali Spiritual & Adventure Week',
                'desc'    => 'An immersive 7-day journey through Bali\'s temples, rice terraces, and adventure spots. Includes Mount Batur sunrise trek, Ubud Monkey Forest, Tegalalang Rice Terraces, and a traditional Kecak dance performance.',
                'days'    => 7, 'price' => 899, 'orig' => 1199, 'type' => 'standard',
                'disc'    => 25, 'featured' => true, 'maxGroup' => 16,
                'difficulty' => 'moderate',
                'inclusions' => ['Return flights from major cities','4-star hotel accommodation','Daily breakfast & 3 dinners','Professional English-speaking guide','All entrance fees','Airport transfers','Mount Batur trek with porter'],
                'highlights' => ['Sunrise trek Mount Batur (1717m)','Tegalalang UNESCO rice terraces','Traditional Kecak fire dance','Tirta Empul holy spring blessing','Seminyak beach sunset'],
            ],
            [
                'destination' => 'Tokyo',
                'title'   => 'Tokyo Tech & Culture Odyssey',
                'desc'    => 'Experience the future and the past in Japan\'s electric capital. Explore Akihabara\'s anime culture, ancient shrines, and cutting-edge robot restaurants over 6 unforgettable days.',
                'days'    => 6, 'price' => 1299, 'orig' => 1599, 'type' => 'luxury',
                'disc'    => 19, 'featured' => true, 'maxGroup' => 12,
                'difficulty' => 'easy',
                'inclusions' => ['Return international flights','5-star hotel in Shinjuku','Daily breakfast','JR Pass (7 days)','Tea ceremony experience','Teamlab Borderless entrance','Professional guide'],
                'highlights' => ['Teamlab Borderless digital art museum','Senso-ji Temple at sunrise','Shibuya Crossing iconic crossing','Day trip to Hakone with Mt Fuji views','Robot restaurant dinner show'],
            ],
            [
                'destination' => 'Maldives',
                'title'   => 'Maldives Overwater Luxury Escape',
                'desc'    => 'The ultimate honeymoon and romantic getaway. Stay in an overwater bungalow with glass floor panels, private plunge pool, and direct lagoon access. World-class snorkeling and spa included.',
                'days'    => 5, 'price' => 2499, 'orig' => 3200, 'type' => 'luxury',
                'disc'    => 22, 'featured' => true, 'maxGroup' => 4,
                'difficulty' => 'easy',
                'inclusions' => ['Seaplane transfers from Malé','Overwater bungalow (5 nights)','All-inclusive dining','Snorkeling equipment','2 couple spa sessions','Sunset dolphin cruise','Underwater dining experience'],
                'highlights' => ['Private overwater villa with glass floor','Bioluminescent beach at night','World-class house reef snorkeling','Sunset dolphin watching cruise','Underwater restaurant dining'],
            ],
            [
                'destination' => 'Rajasthan',
                'title'   => 'Royal Rajasthan Heritage Circuit',
                'desc'    => 'Travel the Golden Triangle and beyond — Jaipur\'s Pink City, Jodhpur\'s Blue City, Udaipur\'s Lake City, and a Thar Desert camel safari under a blanket of stars. Live like royalty.',
                'days'    => 8, 'price' => 749, 'orig' => 999, 'type' => 'standard',
                'disc'    => 25, 'featured' => true, 'maxGroup' => 20,
                'difficulty' => 'easy',
                'inclusions' => ['Domestic flights within Rajasthan','Heritage hotel stays','Daily breakfast & 4 dinners','Private AC vehicle with driver','Licensed guide at major sites','Camel safari in Thar Desert','Cultural dance performance'],
                'highlights' => ['Amer Fort elephant ride','Mehrangarh Fort panoramic views','Lake Pichola boat sunset','Camel safari Thar Desert overnight','Jaisalmer Golden Fort'],
            ],
            [
                'destination' => 'Paris',
                'title'   => 'Paris Romance & Gastronomy Tour',
                'desc'    => 'Fall in love with the City of Light. Private Eiffel Tower access, Louvre highlights tour, wine tasting in Burgundy, and a Michelin-starred dinner make this the ultimate Parisian experience.',
                'days'    => 6, 'price'  => 1599, 'orig' => 2100, 'type' => 'luxury',
                'disc'    => 24, 'featured' => true, 'maxGroup' => 10,
                'difficulty' => 'easy',
                'inclusions' => ['Business class flights available','4-star Seine-view hotel','Daily French breakfast','Louvre skip-the-line pass','Eiffel Tower summit access','Versailles day trip','Burgundy wine tasting','Michelin-starred dinner'],
                'highlights' => ['Eiffel Tower private summit visit','Louvre highlights with art expert','Montmartre at sunset & Sacré-Cœur','Versailles Palace & Marie Antoinette gardens','Burgundy wine-tasting château tour'],
            ],
            [
                'destination' => 'Santorini',
                'title'   => 'Santorini Sunset & Wine Experience',
                'desc'    => 'Watch the world\'s most celebrated sunsets from Oia\'s clifftop, sail the volcanic caldera on a catamaran, and sample indigenous Assyrtiko wines at award-winning wineries.',
                'days'    => 5, 'price' => 1199, 'orig' => 1499, 'type' => 'standard',
                'disc'    => 20, 'featured' => false, 'maxGroup' => 14,
                'difficulty' => 'easy',
                'inclusions' => ['Return flights to Santorini','Caldera-view hotel','Daily breakfast','Catamaran sailing tour','3 winery visits with tastings','Oia sunset dinner reservation','Ferry transfers'],
                'highlights' => ['Oia sunset from the castle ruins','Catamaran caldera sailing with BBQ','Akrotiri Bronze Age excavations','3 wine estate tastings','Red & Black Beach exploration'],
            ],
        ];

        foreach ($packageData as $pd) {
            $dest = Destination::where('name', $pd['destination'])->first();
            if (! $dest) continue;

            Package::firstOrCreate(['title' => $pd['title']], [
                'destination_id'    => $dest->id,
                'description'       => $pd['desc'],
                'duration_days'     => $pd['days'],
                'price_per_person'  => $pd['price'],
                'original_price'    => $pd['orig'],
                'package_type'      => $pd['type'],
                'discount_percent'  => $pd['disc'],
                'is_featured'       => $pd['featured'],
                'max_group_size'    => $pd['maxGroup'],
                'difficulty_level'  => $pd['difficulty'],
                'inclusions'        => $pd['inclusions'],
                'highlights'        => $pd['highlights'],
                'is_active'         => true,
                'availability_count'=> rand(5, 20),
                'cancellation_policy'=> 'Free cancellation up to 7 days before departure.',
            ]);
        }

        // ── Hotels ────────────────────────────────────────────────────
        $hotelData = [
            ['destination' => 'Bali', 'name' => 'Ubud Royal Retreat', 'stars' => 5, 'price' => 180, 'address' => 'Jl. Raya Ubud, Bali', 'amenities' => ['Infinity Pool','Spa','Yoga Studio','Fine Dining','Butler Service']],
            ['destination' => 'Tokyo', 'name' => 'Park Hyatt Tokyo', 'stars' => 5, 'price' => 420, 'address' => '3-7-1-2 Nishi-Shinjuku, Tokyo', 'amenities' => ['City View','Pool','Gym','Multiple Restaurants','Concierge']],
            ['destination' => 'Paris', 'name' => 'Hotel de Crillon', 'stars' => 5, 'price' => 890, 'address' => '10 Place de la Concorde, Paris', 'amenities' => ['Bar Hemingway','Spa','Michelin Restaurant','Butler','Concierge']],
            ['destination' => 'Maldives', 'name' => 'One&Only Reethi Rah', 'stars' => 5, 'price' => 1200, 'address' => 'North Malé Atoll, Maldives', 'amenities' => ['Overwater Villa','Private Pool','Snorkeling','Seaplane Transfer','All-Inclusive']],
            ['destination' => 'Rajasthan', 'name' => 'Umaid Bhawan Palace', 'stars' => 5, 'price' => 650, 'address' => 'Circuit House Rd, Jodhpur', 'amenities' => ['Heritage Rooms','Pool','Spa','Polo Grounds','Museum']],
            ['destination' => 'Santorini', 'name' => 'Canaves Oia Epitome', 'stars' => 5, 'price' => 780, 'address' => 'Oia, Santorini, Greece', 'amenities' => ['Caldera View','Infinity Pool','Wine Bar','Sunset Terrace','Yoga']],
        ];

        foreach ($hotelData as $hd) {
            $dest = Destination::where('name', $hd['destination'])->first();
            if (! $dest) continue;
            Hotel::firstOrCreate(['name' => $hd['name']], [
                'destination_id'  => $dest->id,
                'address'         => $hd['address'],
                'star_rating'     => $hd['stars'],
                'price_per_night' => $hd['price'],
                'amenities'       => $hd['amenities'],
                'is_active'       => true,
            ]);
        }

        // ── Promotions ────────────────────────────────────────────────
        $promos = [
            ['code' => 'WELCOME15', 'title' => '15% Welcome Discount', 'type' => 'percent', 'value' => 15, 'min' => 0,   'limit' => 100],
            ['code' => 'SUMMER25',  'title' => 'Summer 25% Off',       'type' => 'percent', 'value' => 25, 'min' => 500, 'limit' => 50],
            ['code' => 'SAVE100',   'title' => '$100 Off Luxury Tours', 'type' => 'fixed',   'value' => 100,'min' => 1000,'limit' => 30],
            ['code' => 'FAMILY20',  'title' => 'Family 20% Discount',   'type' => 'percent', 'value' => 20, 'min' => 300, 'limit' => null],
        ];

        foreach ($promos as $pd) {
            Promotion::firstOrCreate(['code' => $pd['code']], [
                'title'             => $pd['title'],
                'discount_type'     => $pd['type'],
                'discount_value'    => $pd['value'],
                'min_booking_amount'=> $pd['min'],
                'usage_limit'       => $pd['limit'],
                'valid_from'        => now()->subMonth(),
                'valid_until'       => now()->addYear(),
                'is_active'         => true,
            ]);
        }

        // Seed massive global destinations
        $this->call([
            GlobalDestinationsSeeder::class,
        ]);

        $this->command->info('✅ TravelMate seeded successfully!');
        $this->command->info('   Admin: manilukka143@gmail.com / mani@098');
        $this->command->info('   User:  traveler@travelmate.com / password');
    }
}
