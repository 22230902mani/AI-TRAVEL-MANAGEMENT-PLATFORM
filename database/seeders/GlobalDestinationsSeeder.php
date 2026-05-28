<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Destination;

class GlobalDestinationsSeeder extends Seeder
{
    public function run()
    {
        $destinations = [
            // North America
            [
                'name' => 'New York City', 'city' => 'New York', 'country' => 'United States', 
                'category' => 'urban', 'best_season' => 'Sep-Nov', 'avg_rating' => 4.8, 'review_count' => 12500, 
                'image_url' => 'https://images.unsplash.com/photo-1496442226666-8d4d0e62e6e9?w=800', 
                'description' => 'The Big Apple. Experience Times Square, Central Park, and Broadway.',
                'base_price_economy' => 45000, 'base_price_standard' => 85000, 'base_price_luxury' => 180000,
                'duration_days_suggested' => 7, 'transport_mode' => 'flight'
            ],
            [
                'name' => 'Grand Canyon', 'city' => 'Arizona', 'country' => 'United States', 
                'category' => 'nature', 'best_season' => 'Mar-May', 'avg_rating' => 4.9, 'review_count' => 8400, 
                'image_url' => 'https://images.unsplash.com/photo-1474044159687-1ee9f3a51722?w=800', 
                'description' => 'A breathtaking natural wonder of red rock majesty.',
                'base_price_economy' => 35000, 'base_price_standard' => 65000, 'base_price_luxury' => 140000,
                'duration_days_suggested' => 5, 'transport_mode' => 'flight'
            ],
            [
                'name' => 'Banff National Park', 'city' => 'Banff', 'country' => 'Canada', 
                'category' => 'mountains', 'best_season' => 'Jun-Aug', 'avg_rating' => 4.9, 'review_count' => 6200, 
                'image_url' => 'https://images.unsplash.com/photo-1573511740924-1147a400e998?w=800', 
                'description' => 'Turquoise glacial lakes and towering peaks in the Canadian Rockies.',
                'base_price_economy' => 40000, 'base_price_standard' => 75000, 'base_price_luxury' => 160000,
                'duration_days_suggested' => 6, 'transport_mode' => 'flight'
            ],
            [
                'name' => 'Cancun', 'city' => 'Cancun', 'country' => 'Mexico', 
                'category' => 'beaches', 'best_season' => 'Dec-Apr', 'avg_rating' => 4.6, 'review_count' => 9100, 
                'image_url' => 'https://images.unsplash.com/photo-1510097467424-192d713fd8b2?w=800', 
                'description' => 'White sand beaches and ancient Mayan ruins on the Caribbean sea.',
                'base_price_economy' => 30000, 'base_price_standard' => 55000, 'base_price_luxury' => 120000,
                'duration_days_suggested' => 5, 'transport_mode' => 'flight'
            ],

            // South America
            [
                'name' => 'Rio de Janeiro', 'city' => 'Rio', 'country' => 'Brazil', 
                'category' => 'beaches', 'best_season' => 'Dec-Mar', 'avg_rating' => 4.7, 'review_count' => 7800, 
                'image_url' => 'https://images.unsplash.com/photo-1483729558449-99ef09a8c325?w=800', 
                'description' => 'Home to Christ the Redeemer, Copacabana, and vibrant Carnival culture.',
                'base_price_economy' => 35000, 'base_price_standard' => 65000, 'base_price_luxury' => 140000,
                'duration_days_suggested' => 6, 'transport_mode' => 'flight'
            ],
            [
                'name' => 'Machu Picchu', 'city' => 'Cusco', 'country' => 'Peru', 
                'category' => 'historical', 'best_season' => 'Apr-Oct', 'avg_rating' => 4.9, 'review_count' => 11000, 
                'image_url' => 'https://images.unsplash.com/photo-1587595431973-160d0d94add1?w=800', 
                'description' => 'The majestic lost city of the Incas high in the Andes mountains.',
                'base_price_economy' => 38000, 'base_price_standard' => 70000, 'base_price_luxury' => 150000,
                'duration_days_suggested' => 6, 'transport_mode' => 'flight'
            ],

            // Europe
            [
                'name' => 'London', 'city' => 'London', 'country' => 'United Kingdom', 
                'category' => 'urban', 'best_season' => 'May-Sep', 'avg_rating' => 4.7, 'review_count' => 15000, 
                'image_url' => 'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?w=800', 
                'description' => 'Historic landmarks, royal palaces, and modern multicultural vibrancy.',
                'base_price_economy' => 42000, 'base_price_standard' => 80000, 'base_price_luxury' => 175000,
                'duration_days_suggested' => 6, 'transport_mode' => 'flight'
            ],
            [
                'name' => 'Rome', 'city' => 'Rome', 'country' => 'Italy', 
                'category' => 'historical', 'best_season' => 'Apr-Jun', 'avg_rating' => 4.8, 'review_count' => 14200, 
                'image_url' => 'https://images.unsplash.com/photo-1552832230-c0197dd311b5?w=800', 
                'description' => 'The Eternal City. Explore the Colosseum, Vatican, and authentic pasta.',
                'base_price_economy' => 36000, 'base_price_standard' => 68000, 'base_price_luxury' => 145000,
                'duration_days_suggested' => 5, 'transport_mode' => 'flight'
            ],
            [
                'name' => 'Barcelona', 'city' => 'Barcelona', 'country' => 'Spain', 
                'category' => 'cultural', 'best_season' => 'May-Jun', 'avg_rating' => 4.7, 'review_count' => 11500, 
                'image_url' => 'https://images.unsplash.com/photo-1583422409516-2895a77efded?w=800', 
                'description' => 'Gaudí architecture, vibrant beaches, and world-class tapas.',
                'base_price_economy' => 34000, 'base_price_standard' => 65000, 'base_price_luxury' => 140000,
                'duration_days_suggested' => 5, 'transport_mode' => 'flight'
            ],
            [
                'name' => 'Swiss Alps', 'city' => 'Zermatt', 'country' => 'Switzerland', 
                'category' => 'mountains', 'best_season' => 'Dec-Mar', 'avg_rating' => 4.9, 'review_count' => 5400, 
                'image_url' => 'https://images.unsplash.com/photo-1530122037265-a5f1f91d3b99?w=800', 
                'description' => 'World-class skiing beneath the shadow of the Matterhorn.',
                'base_price_economy' => 55000, 'base_price_standard' => 105000, 'base_price_luxury' => 220000,
                'duration_days_suggested' => 6, 'transport_mode' => 'flight'
            ],
            [
                'name' => 'Berlin', 'city' => 'Berlin', 'country' => 'Germany', 
                'category' => 'cultural', 'best_season' => 'May-Sep', 'avg_rating' => 4.6, 'review_count' => 8900, 
                'image_url' => 'https://images.unsplash.com/photo-1560969184-10fe8719e047?w=800', 
                'description' => 'Rich modern history, incredible art scene, and legendary nightlife.',
                'base_price_economy' => 32000, 'base_price_standard' => 60000, 'base_price_luxury' => 130000,
                'duration_days_suggested' => 5, 'transport_mode' => 'flight'
            ],
            [
                'name' => 'Athens', 'city' => 'Athens', 'country' => 'Greece', 
                'category' => 'historical', 'best_season' => 'Apr-Jun', 'avg_rating' => 4.7, 'review_count' => 7600, 
                'image_url' => 'https://images.unsplash.com/photo-1603565816030-6b389eeb23cb?w=800', 
                'description' => 'The cradle of Western civilization, home to the magnificent Acropolis.',
                'base_price_economy' => 30000, 'base_price_standard' => 58000, 'base_price_luxury' => 125000,
                'duration_days_suggested' => 5, 'transport_mode' => 'flight'
            ],
            [
                'name' => 'Moscow', 'city' => 'Moscow', 'country' => 'Russia', 
                'category' => 'cultural', 'best_season' => 'May-Aug', 'avg_rating' => 4.5, 'review_count' => 4500, 
                'image_url' => 'https://images.unsplash.com/photo-1520106212299-d99c443e4568?w=800', 
                'description' => 'Red Square, the Kremlin, and iconic colorful domes.',
                'base_price_economy' => 32000, 'base_price_standard' => 60000, 'base_price_luxury' => 130000,
                'duration_days_suggested' => 5, 'transport_mode' => 'flight'
            ],

            // Asia
            [
                'name' => 'Taj Mahal', 'city' => 'Agra', 'country' => 'India', 
                'category' => 'historical', 'best_season' => 'Oct-Mar', 'avg_rating' => 4.9, 'review_count' => 20000, 
                'image_url' => 'https://images.unsplash.com/photo-1548013146-72479768bada?w=800', 
                'description' => 'The ultimate monument to love. A white marble architectural masterpiece.',
                'base_price_economy' => 4500, 'base_price_standard' => 8500, 'base_price_luxury' => 18000,
                'duration_days_suggested' => 2, 'transport_mode' => 'train'
            ],
            [
                'name' => 'Great Wall of China', 'city' => 'Beijing', 'country' => 'China', 
                'category' => 'historical', 'best_season' => 'Sep-Nov', 'avg_rating' => 4.8, 'review_count' => 18000, 
                'image_url' => 'https://images.unsplash.com/photo-1508804185872-d7bad80084aa?w=800', 
                'description' => 'An ancient world wonder stretching thousands of miles across mountains.',
                'base_price_economy' => 25000, 'base_price_standard' => 48000, 'base_price_luxury' => 95000,
                'duration_days_suggested' => 5, 'transport_mode' => 'flight'
            ],
            [
                'name' => 'Seoul', 'city' => 'Seoul', 'country' => 'South Korea', 
                'category' => 'urban', 'best_season' => 'Mar-May', 'avg_rating' => 4.7, 'review_count' => 8200, 
                'image_url' => 'https://images.unsplash.com/photo-1538485399081-7191377e8241?w=800', 
                'description' => 'High-tech metropolis meets traditional palaces and street food.',
                'base_price_economy' => 35000, 'base_price_standard' => 65000, 'base_price_luxury' => 140000,
                'duration_days_suggested' => 5, 'transport_mode' => 'flight'
            ],
            [
                'name' => 'Bangkok', 'city' => 'Bangkok', 'country' => 'Thailand', 
                'category' => 'urban', 'best_season' => 'Nov-Feb', 'avg_rating' => 4.6, 'review_count' => 16500, 
                'image_url' => 'https://images.unsplash.com/photo-1583301058362-e64e9a3857e4?w=800', 
                'description' => 'Ornate shrines, vibrant street life, and bustling floating markets.',
                'base_price_economy' => 18000, 'base_price_standard' => 32000, 'base_price_luxury' => 68000,
                'duration_days_suggested' => 4, 'transport_mode' => 'flight'
            ],
            [
                'name' => 'Sigiriya', 'city' => 'Dambulla', 'country' => 'Sri Lanka', 
                'category' => 'heritage', 'best_season' => 'Dec-Mar', 'avg_rating' => 4.8, 'review_count' => 4100, 
                'image_url' => 'https://images.unsplash.com/photo-1586220742614-da9067b66782?w=800', 
                'description' => 'An ancient palace and fortress complex carved into a massive rock.',
                'base_price_economy' => 15000, 'base_price_standard' => 28000, 'base_price_luxury' => 55000,
                'duration_days_suggested' => 4, 'transport_mode' => 'flight'
            ],
            [
                'name' => 'Singapore', 'city' => 'Singapore', 'country' => 'Singapore', 
                'category' => 'luxury', 'best_season' => 'Feb-Apr', 'avg_rating' => 4.8, 'review_count' => 10200, 
                'image_url' => 'https://images.unsplash.com/photo-1525625293386-3f8f99389edd?w=800', 
                'description' => 'Futuristic gardens, Marina Bay Sands, and incredible cleanliness.',
                'base_price_economy' => 28000, 'base_price_standard' => 52000, 'base_price_luxury' => 110000,
                'duration_days_suggested' => 4, 'transport_mode' => 'flight'
            ],
            [
                'name' => 'Kathmandu', 'city' => 'Kathmandu', 'country' => 'Nepal', 
                'category' => 'adventure', 'best_season' => 'Oct-Dec', 'avg_rating' => 4.7, 'review_count' => 5800, 
                'image_url' => 'https://images.unsplash.com/photo-1581416972047-ce774eb0567f?w=800', 
                'description' => 'Gateway to the Himalayas, filled with ancient stupas and temples.',
                'base_price_economy' => 12000, 'base_price_standard' => 22000, 'base_price_luxury' => 45000,
                'duration_days_suggested' => 4, 'transport_mode' => 'flight'
            ],

            // Middle East
            [
                'name' => 'Dubai', 'city' => 'Dubai', 'country' => 'UAE', 
                'category' => 'luxury', 'best_season' => 'Nov-Mar', 'avg_rating' => 4.7, 'review_count' => 13400, 
                'image_url' => 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=800', 
                'description' => 'The Burj Khalifa, luxury shopping, and ultramodern architecture.',
                'base_price_economy' => 32000, 'base_price_standard' => 60000, 'base_price_luxury' => 130000,
                'duration_days_suggested' => 5, 'transport_mode' => 'flight'
            ],
            [
                'name' => 'Al Ula', 'city' => 'Al Ula', 'country' => 'Saudi Arabia', 
                'category' => 'heritage', 'best_season' => 'Oct-Apr', 'avg_rating' => 4.8, 'review_count' => 2100, 
                'image_url' => 'https://images.unsplash.com/photo-1601614041006-25f38dc2a926?w=800', 
                'description' => 'Ancient Nabataean tombs carved into desert sandstone mountains.',
                'base_price_economy' => 35000, 'base_price_standard' => 65000, 'base_price_luxury' => 140000,
                'duration_days_suggested' => 5, 'transport_mode' => 'flight'
            ],
            [
                'name' => 'Cappadocia', 'city' => 'Göreme', 'country' => 'Turkey', 
                'category' => 'adventure', 'best_season' => 'Apr-May', 'avg_rating' => 4.9, 'review_count' => 8700, 
                'image_url' => 'https://images.unsplash.com/photo-1538332576228-eb5b4c4de6f5?w=800', 
                'description' => 'Fairy chimneys and sunrise hot air balloon rides.',
                'base_price_economy' => 28000, 'base_price_standard' => 52000, 'base_price_luxury' => 110000,
                'duration_days_suggested' => 5, 'transport_mode' => 'flight'
            ],

            // Africa
            [
                'name' => 'Cape Town', 'city' => 'Cape Town', 'country' => 'South Africa', 
                'category' => 'nature', 'best_season' => 'Dec-Mar', 'avg_rating' => 4.8, 'review_count' => 7400, 
                'image_url' => 'https://images.unsplash.com/photo-1580060839134-75a5edca2e99?w=800', 
                'description' => 'Table Mountain, pristine beaches, and world-renowned vineyards.',
                'base_price_economy' => 38000, 'base_price_standard' => 72000, 'base_price_luxury' => 155000,
                'duration_days_suggested' => 6, 'transport_mode' => 'flight'
            ],
            [
                'name' => 'Pyramids of Giza', 'city' => 'Cairo', 'country' => 'Egypt', 
                'category' => 'historical', 'best_season' => 'Oct-Apr', 'avg_rating' => 4.7, 'review_count' => 11200, 
                'image_url' => 'https://images.unsplash.com/photo-1539650116574-8efeb43e2b50?w=800', 
                'description' => 'The last surviving wonder of the ancient world.',
                'base_price_economy' => 22000, 'base_price_standard' => 42000, 'base_price_luxury' => 88000,
                'duration_days_suggested' => 5, 'transport_mode' => 'flight'
            ],

            // Oceania
            [
                'name' => 'Sydney', 'city' => 'Sydney', 'country' => 'Australia', 
                'category' => 'urban', 'best_season' => 'Dec-Feb', 'avg_rating' => 4.8, 'review_count' => 9600, 
                'image_url' => 'https://images.unsplash.com/photo-1506973035872-a4ec16b8e8d9?w=800', 
                'description' => 'The iconic Opera House, Harbour Bridge, and Bondi Beach.',
                'base_price_economy' => 45000, 'base_price_standard' => 85000, 'base_price_luxury' => 180000,
                'duration_days_suggested' => 6, 'transport_mode' => 'flight'
            ]
        ];

        foreach ($destinations as $dest) {
            Destination::updateOrCreate(
                ['name' => $dest['name']],
                array_merge($dest, [
                    'status' => 'active',
                    'is_active' => true,
                ])
            );
        }
    }
}
