<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\Itinerary;
use App\Models\Review;
use App\Policies\BookingPolicy;
use App\Policies\ItineraryPolicy;
use App\Policies\ReviewPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        Booking::class   => BookingPolicy::class,
        Itinerary::class => ItineraryPolicy::class,
        Review::class    => ReviewPolicy::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }

        // Super admin can do everything
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('super_admin')) return true;
        });

        // Force HTTPS in production / Render reverse-proxy environment
        if ($this->app->environment('production') || env('FORCE_HTTPS', false)) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
