<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Destination;

class IndiaStatesSeeder extends Seeder
{
    public function run(): void
    {
        // All 28 States + 8 Union Territories of India with capitals and base prices
        $states = [
            // ── NORTH INDIA ──
            ['state'=>'Delhi',              'state_code'=>'DL', 'name'=>'New Delhi',       'city'=>'New Delhi',       'region'=>'North',     'economy'=>3999,  'standard'=>7999,  'luxury'=>15999, 'days'=>3, 'transport'=>'flight', 'see'=>'Red Fort, India Gate, Qutub Minar, Humayun\'s Tomb'],
            ['state'=>'Uttar Pradesh',      'state_code'=>'UP', 'name'=>'Agra & Lucknow',  'city'=>'Lucknow',         'region'=>'North',     'economy'=>4499,  'standard'=>8999,  'luxury'=>17999, 'days'=>4, 'transport'=>'train',  'see'=>'Taj Mahal, Agra Fort, Lucknow Nawabs, Varanasi Ghats'],
            ['state'=>'Rajasthan',          'state_code'=>'RJ', 'name'=>'Jaipur',           'city'=>'Jaipur',          'region'=>'North',     'economy'=>5499,  'standard'=>10999, 'luxury'=>22999, 'days'=>5, 'transport'=>'flight', 'see'=>'Amber Fort, Hawa Mahal, City Palace, Jaisalmer Desert'],
            ['state'=>'Himachal Pradesh',   'state_code'=>'HP', 'name'=>'Shimla',           'city'=>'Shimla',          'region'=>'North',     'economy'=>4999,  'standard'=>9499,  'luxury'=>18999, 'days'=>5, 'transport'=>'train',  'see'=>'Ridge, Mall Road, Kufri, Rohtang Pass'],
            ['state'=>'Uttarakhand',        'state_code'=>'UK', 'name'=>'Dehradun',         'city'=>'Dehradun',        'region'=>'North',     'economy'=>4499,  'standard'=>8499,  'luxury'=>16999, 'days'=>5, 'transport'=>'train',  'see'=>'Mussoorie, Rishikesh, Haridwar, Valley of Flowers'],
            ['state'=>'Punjab',             'state_code'=>'PB', 'name'=>'Chandigarh',       'city'=>'Chandigarh',      'region'=>'North',     'economy'=>3999,  'standard'=>7499,  'luxury'=>14999, 'days'=>3, 'transport'=>'flight', 'see'=>'Golden Temple, Rock Garden, Wagah Border, Shivalik Hills'],
            ['state'=>'Haryana',            'state_code'=>'HR', 'name'=>'Chandigarh',       'city'=>'Chandigarh',      'region'=>'North',     'economy'=>3499,  'standard'=>6999,  'luxury'=>13999, 'days'=>3, 'transport'=>'train',  'see'=>'Kurukshetra, Sultanpur Bird Sanctuary, Pinjore Gardens'],
            ['state'=>'Jammu & Kashmir',    'state_code'=>'JK', 'name'=>'Srinagar',         'city'=>'Srinagar',        'region'=>'North',     'economy'=>7999,  'standard'=>14999, 'luxury'=>29999, 'days'=>6, 'transport'=>'flight', 'see'=>'Dal Lake, Gulmarg, Pahalgam, Mughal Gardens'],
            ['state'=>'Ladakh',             'state_code'=>'LA', 'name'=>'Leh',              'city'=>'Leh',             'region'=>'North',     'economy'=>8999,  'standard'=>16999, 'luxury'=>32999, 'days'=>7, 'transport'=>'flight', 'see'=>'Pangong Lake, Nubra Valley, Monasteries, Khardung La'],

            // ── SOUTH INDIA ──
            ['state'=>'Tamil Nadu',         'state_code'=>'TN', 'name'=>'Chennai',          'city'=>'Chennai',         'region'=>'South',     'economy'=>4999,  'standard'=>9499,  'luxury'=>18999, 'days'=>5, 'transport'=>'flight', 'see'=>'Marina Beach, Meenakshi Temple, Ooty, Kodaikanal'],
            ['state'=>'Kerala',             'state_code'=>'KL', 'name'=>'Thiruvananthapuram','city'=>'Thiruvananthapuram','region'=>'South',   'economy'=>5999,  'standard'=>11499, 'luxury'=>22999, 'days'=>6, 'transport'=>'flight', 'see'=>'Alleppey Backwaters, Munnar, Kovalam Beach, Periyar'],
            ['state'=>'Karnataka',          'state_code'=>'KA', 'name'=>'Bengaluru',        'city'=>'Bengaluru',       'region'=>'South',     'economy'=>4499,  'standard'=>8999,  'luxury'=>17999, 'days'=>4, 'transport'=>'flight', 'see'=>'Mysore Palace, Coorg, Hampi, Jog Falls'],
            ['state'=>'Andhra Pradesh',     'state_code'=>'AP', 'name'=>'Amaravati',        'city'=>'Amaravati',       'region'=>'South',     'economy'=>4499,  'standard'=>8499,  'luxury'=>16999, 'days'=>4, 'transport'=>'flight', 'see'=>'Tirupati, Araku Valley, Borra Caves, Vizag Beach'],
            ['state'=>'Telangana',          'state_code'=>'TG', 'name'=>'Hyderabad',        'city'=>'Hyderabad',       'region'=>'South',     'economy'=>4499,  'standard'=>8999,  'luxury'=>17999, 'days'=>4, 'transport'=>'flight', 'see'=>'Charminar, Golconda Fort, Hussain Sagar, Ramoji Film City'],
            ['state'=>'Goa',                'state_code'=>'GA', 'name'=>'Panaji',           'city'=>'Panaji',          'region'=>'South',     'economy'=>5999,  'standard'=>11999, 'luxury'=>24999, 'days'=>4, 'transport'=>'flight', 'see'=>'Calangute Beach, Basilica of Bom Jesus, Dudhsagar Falls'],
            ['state'=>'Puducherry',         'state_code'=>'PY', 'name'=>'Puducherry',       'city'=>'Puducherry',      'region'=>'South',     'economy'=>4499,  'standard'=>8499,  'luxury'=>16999, 'days'=>3, 'transport'=>'train',  'see'=>'French Quarter, Auroville, Paradise Beach, Pondicherry Museum'],

            // ── EAST INDIA ──
            ['state'=>'West Bengal',        'state_code'=>'WB', 'name'=>'Kolkata',          'city'=>'Kolkata',         'region'=>'East',      'economy'=>4499,  'standard'=>8499,  'luxury'=>16999, 'days'=>4, 'transport'=>'flight', 'see'=>'Victoria Memorial, Howrah Bridge, Sundarbans, Darjeeling'],
            ['state'=>'Odisha',             'state_code'=>'OD', 'name'=>'Bhubaneswar',      'city'=>'Bhubaneswar',     'region'=>'East',      'economy'=>4499,  'standard'=>8499,  'luxury'=>16499, 'days'=>4, 'transport'=>'flight', 'see'=>'Konark Sun Temple, Puri Jagannath, Chilika Lake'],
            ['state'=>'Bihar',              'state_code'=>'BR', 'name'=>'Patna',            'city'=>'Patna',           'region'=>'East',      'economy'=>3999,  'standard'=>7499,  'luxury'=>14999, 'days'=>4, 'transport'=>'train',  'see'=>'Bodh Gaya, Nalanda, Rajgir, Vaishali'],
            ['state'=>'Jharkhand',          'state_code'=>'JH', 'name'=>'Ranchi',           'city'=>'Ranchi',          'region'=>'East',      'economy'=>3999,  'standard'=>7499,  'luxury'=>14999, 'days'=>4, 'transport'=>'flight', 'see'=>'Hundru Falls, Betla National Park, Deoghar'],

            // ── WEST INDIA ──
            ['state'=>'Maharashtra',        'state_code'=>'MH', 'name'=>'Mumbai',           'city'=>'Mumbai',          'region'=>'West',      'economy'=>5499,  'standard'=>10999, 'luxury'=>21999, 'days'=>4, 'transport'=>'flight', 'see'=>'Gateway of India, Marine Drive, Ajanta Ellora, Lonavala'],
            ['state'=>'Gujarat',            'state_code'=>'GJ', 'name'=>'Gandhinagar',      'city'=>'Gandhinagar',     'region'=>'West',      'economy'=>4499,  'standard'=>8999,  'luxury'=>17999, 'days'=>4, 'transport'=>'flight', 'see'=>'Rann of Kutch, Gir National Park, Somnath, Dwarka'],
            ['state'=>'Madhya Pradesh',     'state_code'=>'MP', 'name'=>'Bhopal',           'city'=>'Bhopal',          'region'=>'Central',   'economy'=>4499,  'standard'=>8499,  'luxury'=>16999, 'days'=>5, 'transport'=>'flight', 'see'=>'Khajuraho, Bandhavgarh, Kanha, Sanchi Stupa'],
            ['state'=>'Chhattisgarh',       'state_code'=>'CG', 'name'=>'Raipur',           'city'=>'Raipur',          'region'=>'Central',   'economy'=>4499,  'standard'=>8499,  'luxury'=>16499, 'days'=>4, 'transport'=>'flight', 'see'=>'Bastar Waterfalls, Chitrakote Falls, Tirathgarh'],

            // ── NORTHEAST INDIA ──
            ['state'=>'Assam',              'state_code'=>'AS', 'name'=>'Dispur',           'city'=>'Dispur',          'region'=>'Northeast', 'economy'=>5999,  'standard'=>11499, 'luxury'=>22999, 'days'=>5, 'transport'=>'flight', 'see'=>'Kaziranga National Park, Majuli, Kamakhya Temple, Sibsagar'],
            ['state'=>'Meghalaya',          'state_code'=>'ML', 'name'=>'Shillong',         'city'=>'Shillong',        'region'=>'Northeast', 'economy'=>5999,  'standard'=>11499, 'luxury'=>22999, 'days'=>5, 'transport'=>'flight', 'see'=>'Cherrapunji, Living Root Bridges, Elephant Falls, Umiam Lake'],
            ['state'=>'Sikkim',             'state_code'=>'SK', 'name'=>'Gangtok',          'city'=>'Gangtok',         'region'=>'Northeast', 'economy'=>6499,  'standard'=>12999, 'luxury'=>25999, 'days'=>6, 'transport'=>'flight', 'see'=>'Tsomgo Lake, Nathula Pass, Rumtek Monastery, Pelling'],
            ['state'=>'Arunachal Pradesh',  'state_code'=>'AR', 'name'=>'Itanagar',         'city'=>'Itanagar',        'region'=>'Northeast', 'economy'=>6999,  'standard'=>13999, 'luxury'=>27999, 'days'=>6, 'transport'=>'flight', 'see'=>'Tawang Monastery, Ziro Valley, Namdapha, Sela Pass'],
            ['state'=>'Manipur',            'state_code'=>'MN', 'name'=>'Imphal',           'city'=>'Imphal',          'region'=>'Northeast', 'economy'=>5999,  'standard'=>11499, 'luxury'=>22999, 'days'=>4, 'transport'=>'flight', 'see'=>'Loktak Lake, Keibul Lamjao Park, Kangla Fort'],
            ['state'=>'Mizoram',            'state_code'=>'MZ', 'name'=>'Aizawl',           'city'=>'Aizawl',          'region'=>'Northeast', 'economy'=>5999,  'standard'=>11499, 'luxury'=>22999, 'days'=>4, 'transport'=>'flight', 'see'=>'Vantawng Falls, Blue Mountain, Reiek Peak'],
            ['state'=>'Nagaland',           'state_code'=>'NL', 'name'=>'Kohima',           'city'=>'Kohima',          'region'=>'Northeast', 'economy'=>5999,  'standard'=>11499, 'luxury'=>22999, 'days'=>4, 'transport'=>'flight', 'see'=>'Kohima War Cemetery, Dzükou Valley, Hornbill Festival'],
            ['state'=>'Tripura',            'state_code'=>'TR', 'name'=>'Agartala',         'city'=>'Agartala',        'region'=>'Northeast', 'economy'=>5499,  'standard'=>10499, 'luxury'=>20999, 'days'=>4, 'transport'=>'flight', 'see'=>'Ujjayanta Palace, Neermahal, Sepahijala Wildlife Sanctuary'],

            // ── UNION TERRITORIES ──
            ['state'=>'Andaman & Nicobar',  'state_code'=>'AN', 'name'=>'Port Blair',       'city'=>'Port Blair',      'region'=>'Islands',   'economy'=>8999,  'standard'=>17999, 'luxury'=>34999, 'days'=>6, 'transport'=>'flight', 'see'=>'Cellular Jail, Radhanagar Beach, Havelock Island, Neil Island'],
            ['state'=>'Lakshadweep',        'state_code'=>'LD', 'name'=>'Kavaratti',        'city'=>'Kavaratti',       'region'=>'Islands',   'economy'=>9999,  'standard'=>19999, 'luxury'=>39999, 'days'=>5, 'transport'=>'flight', 'see'=>'Agatti Island, Bangaram Atoll, Coral Reefs, Water Sports'],
            ['state'=>'Chandigarh UT',      'state_code'=>'CH', 'name'=>'Chandigarh',       'city'=>'Chandigarh',      'region'=>'North',     'economy'=>3499,  'standard'=>6499,  'luxury'=>12999, 'days'=>2, 'transport'=>'train',  'see'=>'Rock Garden, Sukhna Lake, Rose Garden, Capitol Complex'],
            ['state'=>'Dadra & Nagar Haveli','state_code'=>'DN','name'=>'Silvassa',         'city'=>'Silvassa',        'region'=>'West',      'economy'=>3999,  'standard'=>7499,  'luxury'=>14999, 'days'=>2, 'transport'=>'train',  'see'=>'Vanganga Lake, Tribal Museum, Hirwa Van Gardens'],
            ['state'=>'Daman & Diu',        'state_code'=>'DD', 'name'=>'Daman',            'city'=>'Daman',           'region'=>'West',      'economy'=>3999,  'standard'=>7499,  'luxury'=>14999, 'days'=>2, 'transport'=>'train',  'see'=>'Daman Fort, Jampore Beach, St. Jerome Fort, Diu Fort'],
        ];

        foreach ($states as $s) {
            Destination::updateOrCreate(
                ['state_code' => $s['state_code'], 'is_state_capital' => true],
                [
                    'name'                   => $s['name'],
                    'country'                => 'India',
                    'state'                  => $s['state'],
                    'state_code'             => $s['state_code'],
                    'city'                   => $s['city'],
                    'region'                 => $s['region'],
                    'is_state_capital'       => true,
                    'is_active'              => true,
                    'is_featured'            => in_array($s['state_code'], ['RJ','KL','GA','JK','LA','TN','MH']),
                    'category'               => 'cultural',
                    'climate'                => 'varies',
                    'best_season'            => 'October – March',
                    'base_price_economy'     => $s['economy'],
                    'base_price_standard'    => $s['standard'],
                    'base_price_luxury'      => $s['luxury'],
                    'duration_days_suggested'=> $s['days'],
                    'transport_mode'         => $s['transport'],
                    'what_to_see'            => $s['see'],
                    'description'            => 'Explore the best of ' . $s['state'] . '. Visit ' . $s['see'] . ' and experience the local culture, cuisine and heritage.',
                ]
            );
        }

        $this->command->info('✅ Seeded ' . count($states) . ' Indian states & union territories with pricing.');
    }
}
