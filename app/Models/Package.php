<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'destination_id', 'title', 'description', 'image', 'duration_days',
        'price_per_person', 'original_price', 'package_type', 'max_group_size',
        'difficulty_level', 'inclusions', 'exclusions', 'highlights', 'itinerary',
        'is_active', 'is_featured', 'availability_count', 'discount_percent',
        'cancellation_policy', 'offer_badge', 'offer_text', 'offer_expires_at',
        'ai_estimations',
    ];

    protected $casts = [
        'inclusions'       => 'array',
        'exclusions'       => 'array',
        'highlights'       => 'array',
        'itinerary'        => 'array',
        'is_active'        => 'boolean',
        'is_featured'      => 'boolean',
        'price_per_person' => 'float',
        'original_price'   => 'float',
        'discount_percent' => 'float',
        'offer_expires_at' => 'datetime',
        'ai_estimations'   => 'array',
    ];

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function priceAlerts()
    {
        return $this->hasMany(PriceAlert::class);
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image && str_starts_with($this->image, 'http')) {
            return $this->image;
        }
        return $this->image
            ? asset('storage/' . $this->image)
            : asset('images/package-placeholder.jpg');
    }

    public function getDiscountedPriceAttribute(): float
    {
        if ($this->discount_percent > 0) {
            return round($this->price_per_person * (1 - $this->discount_percent / 100), 2);
        }
        return $this->price_per_person;
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
