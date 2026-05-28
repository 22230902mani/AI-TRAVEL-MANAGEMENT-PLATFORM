<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Destinations ──────────────────────────────────────────────
        Schema::create('destinations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('country');
            $table->string('city');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->string('banner_image')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('climate')->nullable();
            $table->string('best_season')->nullable();
            $table->integer('avg_rating')->default(0);
            $table->integer('review_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->string('category')->default('general'); // adventure, heritage, culinary, ecotourism
            $table->json('tags')->nullable();
            $table->json('safety_tips')->nullable();
            $table->json('visa_info')->nullable();
            $table->timestamps();
        });

        // ── Packages ──────────────────────────────────────────────────
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destination_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->string('image')->nullable();
            $table->integer('duration_days');
            $table->decimal('price_per_person', 10, 2);
            $table->decimal('original_price', 10, 2)->nullable();
            $table->string('package_type')->default('standard'); // budget, standard, luxury
            $table->integer('max_group_size')->default(20);
            $table->string('difficulty_level')->default('easy'); // easy, moderate, challenging
            $table->json('inclusions')->nullable();
            $table->json('exclusions')->nullable();
            $table->json('highlights')->nullable();
            $table->json('itinerary')->nullable(); // day-by-day plan
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('availability_count')->default(0);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->string('cancellation_policy')->default('flexible');
            $table->timestamps();
        });

        // ── Hotels ────────────────────────────────────────────────────
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destination_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('address');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->integer('star_rating')->default(3);
            $table->decimal('price_per_night', 10, 2);
            $table->string('image')->nullable();
            $table->json('amenities')->nullable();
            $table->json('room_types')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->timestamps();
        });

        // ── Itineraries ───────────────────────────────────────────────
        Schema::create('itineraries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('destination_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('duration_days');
            $table->decimal('budget', 10, 2)->nullable();
            $table->decimal('spent', 10, 2)->default(0);
            $table->string('status')->default('planning'); // planning, active, completed, cancelled
            $table->string('travel_style')->nullable(); // adventure, relaxation, culinary, cultural
            $table->json('days')->nullable(); // AI-generated day-by-day plan
            $table->json('preferences')->nullable();
            $table->json('group_members')->nullable();
            $table->boolean('is_public')->default(false);
            $table->boolean('is_collaborative')->default(false);
            $table->string('share_token')->nullable()->unique();
            $table->timestamps();
        });

        // ── Bookings ──────────────────────────────────────────────────
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_reference')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('package_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('hotel_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('itinerary_id')->nullable()->constrained()->nullOnDelete();
            $table->string('booking_type')->default('package'); // package, hotel, flight, experience
            $table->date('check_in');
            $table->date('check_out')->nullable();
            $table->integer('adults')->default(1);
            $table->integer('children')->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->string('payment_status')->default('pending'); // pending, paid, partial, refunded
            $table->string('booking_status')->default('pending'); // pending, confirmed, cancelled, completed
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable();
            $table->text('special_requests')->nullable();
            $table->json('traveler_details')->nullable();
            $table->string('qr_code')->nullable();
            $table->json('event_log')->nullable(); // event-sourced state changes
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
        });

        // ── Reviews ───────────────────────────────────────────────────
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('destination_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('package_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('hotel_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reviewable_type'); // destination, package, hotel
            $table->integer('rating'); // 1-5
            $table->string('title')->nullable();
            $table->text('body');
            $table->integer('food_rating')->nullable();
            $table->integer('cleanliness_rating')->nullable();
            $table->integer('safety_rating')->nullable();
            $table->integer('value_rating')->nullable();
            $table->json('photos')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_flagged')->default(false);
            $table->integer('helpful_votes')->default(0);
            $table->string('hash')->nullable(); // blockchain-inspired tamper-evident hash
            $table->string('prev_hash')->nullable();
            $table->timestamps();
        });

        // ── Expenses ──────────────────────────────────────────────────
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('itinerary_id')->nullable()->constrained()->nullOnDelete();
            $table->string('category'); // accommodation, food, transport, activity, shopping, other
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('USD');
            $table->string('description')->nullable();
            $table->string('receipt_image')->nullable();
            $table->date('expense_date');
            $table->string('payment_method')->nullable();
            $table->boolean('is_shared')->default(false);
            $table->json('split_with')->nullable();
            $table->timestamps();
        });

        // ── Chat Messages ─────────────────────────────────────────────
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('itinerary_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id')->nullable();
            $table->string('role')->default('user'); // user, assistant
            $table->text('message');
            $table->string('language')->default('en');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // ── Wishlists ─────────────────────────────────────────────────
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('destination_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('package_id')->nullable()->constrained()->nullOnDelete();
            $table->string('wishlistable_type');
            $table->timestamps();
        });

        // ── Travel Feed / Posts ───────────────────────────────────────
        Schema::create('travel_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('destination_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title')->nullable();
            $table->text('body');
            $table->json('photos')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->integer('likes_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->boolean('is_public')->default(true);
            $table->timestamps();
        });

        // ── Post Likes ────────────────────────────────────────────────
        Schema::create('post_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('travel_post_id')->constrained()->cascadeOnDelete();
            $table->unique(['user_id', 'travel_post_id']);
            $table->timestamps();
        });

        // ── Post Comments ─────────────────────────────────────────────
        Schema::create('post_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('travel_post_id')->constrained()->cascadeOnDelete();
            $table->text('body');
            $table->timestamps();
        });

        // ── Notifications ─────────────────────────────────────────────
        Schema::create('travel_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // booking_confirmed, price_alert, itinerary_update, etc.
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        // ── Contact / Support Tickets ─────────────────────────────────
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('subject');
            $table->text('message');
            $table->string('status')->default('open'); // open, in_progress, resolved, closed
            $table->string('priority')->default('medium'); // low, medium, high, urgent
            $table->text('admin_response')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });

        // ── Loyalty / Points ──────────────────────────────────────────
        Schema::create('loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('points');
            $table->string('type'); // earn, redeem, expire
            $table->string('description');
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        // ── Price Alerts ──────────────────────────────────────────────
        Schema::create('price_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('package_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('destination_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('target_price', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamp('triggered_at')->nullable();
            $table->timestamps();
        });

        // ── Promotions ────────────────────────────────────────────────
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('discount_type')->default('percent'); // percent, fixed
            $table->decimal('discount_value', 10, 2);
            $table->decimal('min_booking_amount', 10, 2)->default(0);
            $table->integer('usage_limit')->nullable();
            $table->integer('used_count')->default(0);
            $table->date('valid_from');
            $table->date('valid_until');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ── User Profiles (extended) ──────────────────────────────────
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->unique();
            $table->string('avatar')->nullable();
            $table->string('phone')->nullable();
            $table->string('nationality')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->text('bio')->nullable();
            $table->json('travel_interests')->nullable(); // adventure, heritage, culinary, etc.
            $table->json('accessibility_needs')->nullable();
            $table->string('preferred_language')->default('en');
            $table->string('preferred_currency')->default('USD');
            $table->integer('loyalty_level')->default(1); // 1=Bronze, 2=Silver, 3=Gold, 4=Diamond
            $table->integer('total_points')->default(0);
            $table->integer('total_trips')->default(0);
            $table->json('behavioral_vector')->nullable(); // AI personalization vector
            $table->string('passport_number')->nullable();
            $table->date('passport_expiry')->nullable();
            $table->json('emergency_contacts')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('price_alerts');
        Schema::dropIfExists('loyalty_points');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('travel_notifications');
        Schema::dropIfExists('post_comments');
        Schema::dropIfExists('post_likes');
        Schema::dropIfExists('travel_posts');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('itineraries');
        Schema::dropIfExists('hotels');
        Schema::dropIfExists('packages');
        Schema::dropIfExists('destinations');
    }
};
