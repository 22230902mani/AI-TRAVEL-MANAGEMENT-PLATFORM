<?php

namespace App\Console\Commands;

use App\Models\Destination;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchDestinationImages extends Command
{
    protected $signature   = 'destinations:fetch-images {--force : Re-fetch even if image already set}';
    protected $description = 'Fetch HD images for all destinations from the Unsplash API and store them in MongoDB.';

    // Curated search keywords per destination name for best photo results
    protected array $keywordMap = [
        // Indian State Capitals & UTs
        'New Delhi'          => 'india gate new delhi landmark',
        'Delhi'              => 'red fort delhi india historic',
        'Agra & Lucknow'     => 'taj mahal agra india sunrise',
        'Jaipur'             => 'hawa mahal jaipur rajasthan palace',
        'Shimla'             => 'shimla snow hills himachal india',
        'Dehradun'           => 'mussoorie hills green uttarakhand',
        'Chandigarh'         => 'chandigarh rock garden sukhna lake',
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
        'Bhubaneswar'        => 'konark sun temple odisha india stone',
        'Patna'              => 'bodh gaya buddhist temple bihar india',
        'Ranchi'             => 'hundru falls jharkhand waterfall india',
        'Mumbai'             => 'gateway india mumbai marine drive sea',
        'Gandhinagar'        => 'rann of kutch gujarat white desert india',
        'Bhopal'             => 'sanchi stupa madhya pradesh india ancient',
        'Raipur'             => 'chitrakote falls chhattisgarh waterfall',
        'Dispur'             => 'kaziranga rhino assam india wildlife',
        'Shillong'           => 'cherrapunji living root bridges meghalaya',
        'Gangtok'            => 'gangtok sikkim mountains monastery clouds',
        'Itanagar'           => 'tawang monastery arunachal pradesh mountains',
        'Imphal'             => 'loktak lake manipur india floating islands',
        'Aizawl'             => 'mizoram valley mountains northeast india',
        'Kohima'             => 'nagaland hornbill festival tribal culture',
        'Agartala'           => 'ujjayanta palace tripura india heritage',
        'Port Blair'         => 'andaman radhanagar beach turquoise water',
        'Kavaratti'          => 'lakshadweep lagoon coral beach tropical',
        'Silvassa'           => 'forest lake nature green india serene',
        'Daman'              => 'daman fort beach india coast sunset',
        // Popular Indian Tourist Destinations
        'Goa'                => 'goa beach sunset palms india ocean',
        'Kerala'             => 'kerala backwaters houseboat boat green',
        'Agra'               => 'taj mahal agra sunrise marble india',
        'Varanasi'           => 'varanasi ghats ganges india spiritual',
        'Manali'             => 'manali snow mountains himachal river',
        'Darjeeling'         => 'darjeeling tea garden hills mist india',
        'Hampi'              => 'hampi ruins karnataka india ancient stone',
        'Rishikesh'          => 'rishikesh ganges yoga river india bridge',
        'Udaipur'            => 'udaipur lake palace rajasthan india water',
        'Ooty'               => 'ooty tea estate nilgiris hills greenery',
        'Coorg'              => 'coorg coffee plantation karnataka mist',
        'Puri'               => 'puri jagannath temple beach odisha india',
        'Kashmir'            => 'kashmir shikara dal lake mountains snow',
        'Pushkar'            => 'pushkar lake camel fair rajasthan india',
        'Rajasthan'          => 'rajasthan fort desert palace camel india',
        'Taj Mahal'          => 'taj mahal agra india sunrise marble',
        'Mysore'             => 'mysore palace karnataka india illuminated',
        'Amritsar'           => 'golden temple amritsar punjab india sikh',
        'Jodhpur'            => 'jodhpur blue city mehrangarh fort rajasthan',
        'Jaisalmer'          => 'jaisalmer golden fort desert camel sand',
        'Kochi'              => 'kochi fort kerala chinese fishing nets sea',
        'Munnar'             => 'munnar tea garden kerala green hills mist',
        'Nainital'           => 'nainital lake hills uttarakhand india boats',
        'Mussoorie'          => 'mussoorie hills cloud uttarakhand scenic',
        'Haridwar'           => 'haridwar ganga aarti india spiritual river',
        'Alleppey'           => 'alleppey backwaters houseboat kerala green',
        'Andaman'            => 'andaman beach turquoise water tropical palm',
        // Global Destinations
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
        'Moscow'             => 'moscow red square kremlin russia cathedral',
        'Great Wall of China'=> 'great wall china beijing mountains',
        'Seoul'              => 'seoul korea gyeongbokgung palace night city',
        'Bangkok'            => 'bangkok grand palace temple wat thailand',
        'Sigiriya'           => 'sigiriya rock sri lanka ancient fortress',
        'Singapore'          => 'singapore marina bay sands gardens bay',
        'Kathmandu'          => 'kathmandu boudhanath stupa nepal himalaya',
        'Dubai'              => 'dubai burj khalifa skyline uae modern',
        'Al Ula'             => 'al ula nabataean tombs saudi desert ancient',
        'Cappadocia'         => 'cappadocia hot air balloons turkey sunrise',
        'Cape Town'          => 'cape town table mountain south africa',
        'Pyramids of Giza'   => 'pyramids giza egypt camel desert ancient',
        'Sydney'             => 'sydney opera house harbour bridge australia',
        'Paris'              => 'paris eiffel tower france night city',
        'Bali'               => 'bali rice terraces temple indonesia tropical',
        'Tokyo'              => 'tokyo japan shibuya crossing city night',
        'Maldives'           => 'maldives overwater bungalow turquoise ocean',
        'Santorini'          => 'santorini blue dome oia greece sunset white',
        'Amsterdam'          => 'amsterdam canal tulips netherlands bike',
        'Prague'             => 'prague castle charles bridge czech river',
        'Vienna'             => 'vienna schoenbrunn palace austria baroque',
        'Istanbul'           => 'istanbul blue mosque turkey bosphorus',
        'Cairo'              => 'cairo egypt pyramids sphinx ancient',
        'Nairobi'            => 'kenya safari savanna elephants africa',
        'Petra'              => 'petra jordan treasury rose city ancient',
        'Marrakech'          => 'marrakech medina morocco souks colorful',
        'Kyoto'              => 'kyoto fushimi inari shrine japan bamboo',
        'Osaka'              => 'osaka castle japan dotonbori night neon',
        'Phuket'             => 'phuket beach thailand limestone islands blue',
        'Bora Bora'          => 'bora bora overwater bungalow lagoon tropical',
        'Miami'              => 'miami south beach art deco florida ocean',
        'Las Vegas'          => 'las vegas strip night nevada neon casino',
        'San Francisco'      => 'golden gate bridge san francisco bay fog',
        'Toronto'            => 'toronto cn tower canada skyline city',
        'Lisbon'             => 'lisbon tram alfama portugal colorful',
        'Madrid'             => 'madrid spain plaza mayor royal palace',
        'Florence'           => 'florence duomo tuscany italy cathedral',
        'Venice'             => 'venice canal gondola italy romantic',
        'Dubrovnik'          => 'dubrovnik old town walls croatia adriatic',
        'Hong Kong'          => 'hong kong skyline harbour night victoria peak',
        'Kuala Lumpur'       => 'kuala lumpur petronas towers malaysia night',
        'Ho Chi Minh City'   => 'ho chi minh city saigon vietnam streets',
        'Hanoi'              => 'hanoi hoan kiem lake vietnam old quarter',
        'Colombo'            => 'colombo sri lanka ocean beach city',
        'Auckland'           => 'auckland sky tower new zealand harbour',
        'Melbourne'          => 'melbourne cbd yarra river australia city',
        'Johannesburg'       => 'johannesburg south africa city skyline',
        'Havana'             => 'havana cuba classic cars colorful street',
        'Buenos Aires'       => 'buenos aires argentina tango architecture',
        'Mexico City'        => 'mexico city zocalo cathedral culture',
    ];

    public function handle(): int
    {
        $accessKey = config('services.unsplash.access_key');

        if (! $accessKey) {
            $this->error('UNSPLASH_ACCESS_KEY not set in .env');
            return 1;
        }

        $force       = $this->option('force');
        $destinations = Destination::all();
        $total       = $destinations->count();
        $updated     = 0;
        $skipped     = 0;
        $failed      = 0;

        $this->info("🌍 Fetching Unsplash images for {$total} destinations...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($destinations as $dest) {
            $bar->advance();

            // Skip if already has a stored HTTP image URL (unless --force)
            if (! $force && $dest->image && str_starts_with($dest->image, 'http')) {
                $skipped++;
                continue;
            }

            $name    = trim($dest->name);
            $country = trim($dest->country ?? '');

            // Build search query
            if (isset($this->keywordMap[$name])) {
                $query = $this->keywordMap[$name];
            } else {
                $query = strtolower($name) . ' ' . strtolower($country) . ' travel landmark';
            }

            // Call Unsplash API
            try {
                $response = Http::timeout(10)->get('https://api.unsplash.com/photos/random', [
                    'query'       => $query,
                    'orientation' => 'landscape',
                    'client_id'   => $accessKey,
                ]);

                if ($response->successful()) {
                    $imageUrl = $response->json('urls.regular');
                    if ($imageUrl) {
                        // Append quality and width params
                        $imageUrl .= '&w=800&q=80';
                        $dest->image = $imageUrl;
                        $dest->save();
                        $updated++;
                    }
                } else {
                    $failed++;
                }
            } catch (\Exception $e) {
                $failed++;
                $this->newLine();
                $this->warn("  Failed: {$name} — " . $e->getMessage());
            }

            // Unsplash free tier: 50 req/hour = ~1.2 req/sec. Sleep 1.5s to be safe.
            usleep(1500000); // 1.5 seconds
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ Done! Updated: {$updated} | Skipped: {$skipped} | Failed: {$failed}");

        return 0;
    }
}
