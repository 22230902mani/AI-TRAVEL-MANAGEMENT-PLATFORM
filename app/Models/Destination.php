<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use MongoDB\Laravel\Eloquent\Model;

class Destination extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'country', 'city', 'description', 'image', 'banner_image',
        'latitude', 'longitude', 'climate', 'best_season', 'avg_rating',
        'review_count', 'is_active', 'is_featured', 'category', 'tags',
        'safety_tips', 'visa_info',
        // India state pricing
        'state', 'state_code', 'is_state_capital', 'region',
        'base_price_economy', 'base_price_standard', 'base_price_luxury',
        'duration_days_suggested', 'transport_mode', 'what_to_see',
    ];

    protected $casts = [
        'tags'                  => 'array',
        'safety_tips'           => 'array',
        'visa_info'             => 'array',
        'is_active'             => 'boolean',
        'is_featured'           => 'boolean',
        'is_state_capital'      => 'boolean',
        'latitude'              => 'float',
        'longitude'             => 'float',
        'base_price_economy'    => 'float',
        'base_price_standard'   => 'float',
        'base_price_luxury'     => 'float',
    ];

    // Curated Unsplash search keywords per destination
    protected static array $unsplashKeywords = [
        'New Delhi'          => 'india gate new delhi landmark',
        'Delhi'              => 'red fort delhi india historic',
        'Agra & Lucknow'     => 'taj mahal agra india sunrise',
        'Jaipur'             => 'hawa mahal jaipur rajasthan palace',
        'Shimla'             => 'shimla snow hills himachal india',
        'Dehradun'           => 'mussoorie hills green uttarakhand india',
        'Chandigarh'         => 'chandigarh sukhna lake india',
        'Srinagar'           => 'dal lake kashmir houseboat shikara',
        'Leh'                => 'ladakh pangong lake mountains monastery',
        'Chennai'            => 'marina beach chennai india sunrise',
        'Thiruvananthapuram' => 'kerala backwaters alleppey houseboat',
        'Bengaluru'          => 'bangalore garden city india palace',
        'Amaravati'          => 'tirupati temple andhra pradesh india',
        'Hyderabad'          => 'charminar hyderabad india old city',
        'Panaji'             => 'goa beach sunset ocean palm trees',
        'Puducherry'         => 'pondicherry french quarter colorful street',
        'Kolkata'            => 'howrah bridge kolkata india river',
        'Bhubaneswar'        => 'konark sun temple odisha india',
        'Patna'              => 'bodh gaya buddhist temple bihar india',
        'Ranchi'             => 'waterfall jharkhand india nature',
        'Mumbai'             => 'gateway india mumbai marine drive',
        'Gandhinagar'        => 'rann of kutch gujarat white desert india',
        'Bhopal'             => 'sanchi stupa madhya pradesh india',
        'Raipur'             => 'chitrakote falls chhattisgarh waterfall',
        'Dispur'             => 'kaziranga rhino assam india wildlife',
        'Shillong'           => 'meghalaya living root bridges waterfalls',
        'Gangtok'            => 'gangtok sikkim mountains monastery',
        'Itanagar'           => 'tawang monastery arunachal pradesh',
        'Imphal'             => 'loktak lake manipur india',
        'Aizawl'             => 'mizoram valley mountains northeast india',
        'Kohima'             => 'nagaland tribal culture hills india',
        'Agartala'           => 'ujjayanta palace tripura india heritage',
        'Port Blair'         => 'andaman beach turquoise water tropical',
        'Kavaratti'          => 'lakshadweep lagoon coral beach tropical',
        'Silvassa'           => 'forest lake nature green india serene',
        'Daman'              => 'daman beach india coast sunset',
        'Goa'                => 'goa beach sunset palms india ocean',
        'Kerala'             => 'kerala backwaters houseboat boat green',
        'Agra'               => 'taj mahal agra sunrise marble india',
        'Varanasi'           => 'varanasi ghats ganges india spiritual',
        'Manali'             => 'manali snow mountains himachal river',
        'Darjeeling'         => 'darjeeling tea garden hills mist india',
        'Hampi'              => 'hampi ruins karnataka india ancient stone',
        'Rishikesh'          => 'rishikesh ganges yoga river india',
        'Udaipur'            => 'udaipur lake palace rajasthan india',
        'Ooty'               => 'ooty tea estate nilgiris hills',
        'Coorg'              => 'coorg coffee plantation karnataka mist',
        'Puri'               => 'puri jagannath temple beach odisha',
        'Kashmir'            => 'kashmir shikara dal lake mountains snow',
        'Pushkar'            => 'pushkar lake camel rajasthan india',
        'Rajasthan'          => 'rajasthan fort desert palace camel',
        'Taj Mahal'          => 'taj mahal agra india sunrise marble',
        'Mysore'             => 'mysore palace karnataka india illuminated',
        'Amritsar'           => 'golden temple amritsar punjab india',
        'Jodhpur'            => 'jodhpur blue city mehrangarh fort',
        'Jaisalmer'          => 'jaisalmer golden fort desert camel sand',
        'Kochi'              => 'kochi kerala chinese fishing nets sea',
        'Munnar'             => 'munnar tea garden kerala green hills mist',
        'Nainital'           => 'nainital lake hills uttarakhand india',
        'Mussoorie'          => 'mussoorie hills cloud uttarakhand',
        'Haridwar'           => 'haridwar ganga aarti india river',
        'Alleppey'           => 'alleppey backwaters houseboat kerala',
        'Andaman'            => 'andaman beach turquoise water tropical palm',
        'New York City'      => 'new york skyline manhattan times square',
        'Grand Canyon'       => 'grand canyon arizona sunset red rock',
        'Banff National Park'=> 'banff lake louise canada mountains turquoise',
        'Cancun'             => 'cancun beach caribbean turquoise mexico',
        'Rio de Janeiro'     => 'rio de janeiro christ redeemer sugarloaf',
        'Machu Picchu'       => 'machu picchu peru andes mountains ancient',
        'London'             => 'london tower bridge thames big ben',
        'Rome'               => 'colosseum rome italy ancient ruins',
        'Barcelona'          => 'barcelona sagrada familia spain gaudi',
        'Swiss Alps'         => 'swiss alps matterhorn snow switzerland',
        'Berlin'             => 'berlin brandenburger tor germany historic',
        'Athens'             => 'acropolis athens greece parthenon ancient',
        'Moscow'             => 'moscow red square kremlin russia',
        'Great Wall of China'=> 'great wall china beijing mountains',
        'Seoul'              => 'seoul korea gyeongbokgung palace city',
        'Bangkok'            => 'bangkok grand palace temple thailand',
        'Sigiriya'           => 'sigiriya rock sri lanka ancient fortress',
        'Singapore'          => 'singapore marina bay sands gardens',
        'Kathmandu'          => 'kathmandu boudhanath stupa nepal himalaya',
        'Dubai'              => 'dubai burj khalifa skyline uae',
        'Al Ula'             => 'al ula nabataean tombs saudi desert',
        'Cappadocia'         => 'cappadocia hot air balloons turkey sunrise',
        'Cape Town'          => 'cape town table mountain south africa',
        'Pyramids of Giza'   => 'pyramids giza egypt camel desert',
        'Sydney'             => 'sydney opera house harbour bridge australia',
        'Paris'              => 'paris eiffel tower france night',
        'Bali'               => 'bali rice terraces temple indonesia',
        'Tokyo'              => 'tokyo japan shibuya crossing city night',
        'Maldives'           => 'maldives overwater bungalow turquoise ocean',
        'Santorini'          => 'santorini blue dome oia greece sunset',
        'Amsterdam'          => 'amsterdam canal tulips netherlands',
        'Prague'             => 'prague castle charles bridge czech',
        'Vienna'             => 'vienna schoenbrunn palace austria',
        'Istanbul'           => 'istanbul blue mosque turkey bosphorus',
        'Cairo'              => 'cairo egypt pyramids sphinx ancient',
        'Nairobi'            => 'kenya safari savanna elephants africa',
        'Petra'              => 'petra jordan treasury rose city',
        'Marrakech'          => 'marrakech medina morocco souks colorful',
        'Kyoto'              => 'kyoto fushimi inari shrine japan bamboo',
        'Osaka'              => 'osaka castle japan dotonbori night',
        'Phuket'             => 'phuket beach thailand limestone islands',
        'Bora Bora'          => 'bora bora overwater bungalow lagoon',
        'Miami'              => 'miami south beach art deco florida',
        'Las Vegas'          => 'las vegas strip night nevada neon',
        'San Francisco'      => 'golden gate bridge san francisco bay',
        'Toronto'            => 'toronto cn tower canada skyline',
        'Lisbon'             => 'lisbon tram alfama portugal colorful',
        'Madrid'             => 'madrid spain plaza mayor royal palace',
        'Florence'           => 'florence duomo tuscany italy cathedral',
        'Venice'             => 'venice canal gondola italy romantic',
        'Dubrovnik'          => 'dubrovnik old town walls croatia adriatic',
        'Hong Kong'          => 'hong kong skyline harbour night victoria',
        'Kuala Lumpur'       => 'kuala lumpur petronas towers malaysia',
        'Ho Chi Minh City'   => 'ho chi minh city saigon vietnam',
        'Hanoi'              => 'hanoi hoan kiem lake vietnam',
        'Colombo'            => 'colombo sri lanka ocean beach',
        'Auckland'           => 'auckland sky tower new zealand harbour',
        'Melbourne'          => 'melbourne cbd yarra river australia',
        'Johannesburg'       => 'johannesburg south africa city',
        'Havana'             => 'havana cuba classic cars colorful',
        'Buenos Aires'       => 'buenos aires argentina tango architecture',
        'Mexico City'        => 'mexico city zocalo cathedral culture',
    ];

    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    public function hotels()
    {
        return $this->hasMany(Hotel::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function itineraries()
    {
        return $this->hasMany(Itinerary::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function travelPosts()
    {
        return $this->hasMany(TravelPost::class);
    }

    /**
     * Get the HD image URL for this destination.
     *
     * Priority:
     *  1. Stored 'image' field in MongoDB (set by FetchDestinationImages command) — instant
     *  2. Stored 'image_url' field (GlobalDestinationsSeeder) — instant
     *  3. Local storage path — instant
     *  4. Unsplash API call with 7-day cache — fetched once, then cached
     *  5. Generic beautiful fallback image
     */
    public function getImageUrlAttribute(): string
    {
        // 1. Direct stored image URL (HTTP)
        if ($this->image && str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        // 2. Raw 'image_url' field in MongoDB document
        $directUrl = $this->getRawOriginal('image_url');
        if ($directUrl && is_string($directUrl) && str_starts_with($directUrl, 'http')) {
            return $directUrl;
        }

        // 3. Local storage path
        if ($this->image) {
            return asset('storage/' . $this->image);
        }

        // 4. Unsplash Official API with 7-day cache
        $accessKey = config('services.unsplash.access_key');
        if ($accessKey) {
            $name    = trim($this->name);
            $country = trim($this->country ?? '');

            $query = isset(self::$unsplashKeywords[$name])
                ? self::$unsplashKeywords[$name]
                : strtolower($name) . ' ' . strtolower($country) . ' travel city landmark';

            $cacheKey = 'unsplash_img_' . md5($name . $country);

            $cachedUrl = Cache::remember($cacheKey, now()->addDays(7), function () use ($query, $accessKey) {
                try {
                    $resp = Http::timeout(8)->get('https://api.unsplash.com/photos/random', [
                        'query'       => $query,
                        'orientation' => 'landscape',
                        'client_id'   => $accessKey,
                    ]);
                    if ($resp->successful()) {
                        $url = $resp->json('urls.regular');
                        return $url ? $url . '&w=800&q=80' : null;
                    }
                } catch (\Exception $e) {
                    Log::warning('Unsplash API failed for: ' . $query . ' — ' . $e->getMessage());
                }
                return null;
            });

            if ($cachedUrl) {
                return $cachedUrl;
            }
        }

        // 5. Beautiful generic fallback — world travel photo
        return 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=800&q=80';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
