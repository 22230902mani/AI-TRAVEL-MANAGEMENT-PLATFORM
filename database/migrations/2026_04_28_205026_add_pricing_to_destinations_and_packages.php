<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add India state/capital pricing fields to destinations
        Schema::table('destinations', function (Blueprint $table) {
            $table->string('state')->nullable();
            $table->string('state_code', 10)->nullable();
            $table->boolean('is_state_capital')->default(false);
            // Pre-set prices set by admin
            $table->decimal('base_price_economy',  10, 2)->default(0);
            $table->decimal('base_price_standard', 10, 2)->default(0);
            $table->decimal('base_price_luxury',   10, 2)->default(0);
            $table->integer('duration_days_suggested')->default(3);
            $table->string('transport_mode')->default('flight'); // flight, train, bus
            $table->text('what_to_see')->nullable();
            $table->string('region')->nullable(); // North, South, East, West, Central, Northeast
        });
    }

    public function down(): void
    {
        Schema::table('destinations', function (Blueprint $table) {
            $table->dropColumn([
                'state', 'state_code', 'is_state_capital',
                'base_price_economy', 'base_price_standard', 'base_price_luxury',
                'duration_days_suggested', 'transport_mode', 'what_to_see', 'region',
            ]);
        });
    }
};
