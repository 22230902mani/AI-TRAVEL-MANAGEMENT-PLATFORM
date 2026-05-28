<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'destination_id', 'package_id', 'hotel_id', 'booking_id',
        'reviewable_type', 'rating', 'title', 'body', 'food_rating',
        'cleanliness_rating', 'safety_rating', 'value_rating', 'photos',
        'is_verified', 'is_flagged', 'helpful_votes', 'hash', 'prev_hash',
    ];

    protected $casts = [
        'photos'      => 'array',
        'is_verified' => 'boolean',
        'is_flagged'  => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        // Blockchain-inspired: hash this review chained to previous
        static::creating(function ($review) {
            $prevReview = static::latest()->first();
            $review->prev_hash = $prevReview?->hash ?? str_repeat('0', 64);
            $review->hash = hash('sha256',
                $review->user_id . $review->body . $review->rating . $review->prev_hash . now()->timestamp
            );
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeNotFlagged($query)
    {
        return $query->where('is_flagged', false);
    }
}
