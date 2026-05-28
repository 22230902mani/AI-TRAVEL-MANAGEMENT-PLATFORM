<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Destination;

class UpdatePricingSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Starting Destination database cleanup and pricing updates...');

        // 1. Delete corrupted destinations where the name is empty or a 24-character MongoDB ObjectId
        $corrupted = Destination::all()->filter(function ($d) {
            return empty($d->name) 
                || preg_match('/^[0-9a-fA-F]{24}$/', $d->name) 
                || (isset($d->state) && preg_match('/^[0-9a-fA-F]{24}$/', $d->state));
        });

        if ($corrupted->count() > 0) {
            $this->command->warn('Found ' . $corrupted->count() . ' corrupted destination records. Deleting...');
            foreach ($corrupted as $c) {
                $this->command->warn('Deleting corrupted ID: ' . $c->id . ' (Name: ' . $c->name . ')');
                $c->delete();
            }
        }

        // 2. Run the IndiaStatesSeeder to restore / populate domestic prices correctly
        $this->command->info('Seeding/updating India domestic destinations...');
        $this->call(IndiaStatesSeeder::class);

        // 3. Run the updated GlobalDestinationsSeeder to update global prices to high-quality INR tiers
        $this->command->info('Seeding/updating global international destinations...');
        $this->call(GlobalDestinationsSeeder::class);

        // 4. Scan all records and fix any remaining zero or null standard/luxury prices
        $this->command->info('Verifying and scaling pricing tiers across all destinations...');
        $all = Destination::all();
        $updatedCount = 0;

        foreach ($all as $dest) {
            $changed = false;

            // Ensure transport mode is valid (defaults to flight/train/bus)
            if (empty($dest->transport_mode)) {
                $dest->transport_mode = ($dest->country === 'India') ? 'train' : 'flight';
                $changed = true;
            }

            // Ensure duration days is set and at least 1
            if (empty($dest->duration_days_suggested) || $dest->duration_days_suggested < 1) {
                $dest->duration_days_suggested = 4;
                $changed = true;
            }

            // Ensure base price economy is at least 3000 for realistic INR unless it is domestic train
            if (empty($dest->base_price_economy) || $dest->base_price_economy <= 0) {
                // Set default economy based on domestic vs international
                $dest->base_price_economy = ($dest->country === 'India') ? 3999 : 25000;
                $changed = true;
            }

            // Standard should be roughly 1.8x of Economy, ending in 99 or rounded to nearest 500
            if (empty($dest->base_price_standard) || $dest->base_price_standard <= 0 || $dest->base_price_standard <= $dest->base_price_economy) {
                $calc = $dest->base_price_economy * 1.8;
                // Round to nearest 500 and subtract 1 to get a clean price (e.g. 8999 or 7499)
                $dest->base_price_standard = round($calc / 500) * 500 - 1;
                $changed = true;
            }

            // Luxury should be roughly 3.6x of Economy, ending in 99 or rounded to nearest 500
            if (empty($dest->base_price_luxury) || $dest->base_price_luxury <= 0 || $dest->base_price_luxury <= $dest->base_price_standard) {
                $calc = $dest->base_price_economy * 3.6;
                $dest->base_price_luxury = round($calc / 1000) * 1000 - 1;
                $changed = true;
            }

            if ($changed) {
                $dest->save();
                $updatedCount++;
            }
        }

        $this->command->info('✅ Cleaned and successfully calibrated ' . $updatedCount . ' destinations with premium pricing tiers!');
    }
}
