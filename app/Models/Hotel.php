<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'destination_id', 'name', 'description', 'address', 'latitude', 'longitude',
        'star_rating', 'price_per_night', 'image', 'amenities', 'room_types',
        'is_active', 'contact_email', 'contact_phone',
    ];

    protected $casts = [
        'amenities'      => 'array',
        'room_types'     => 'array',
        'is_active'      => 'boolean',
        'price_per_night'=> 'float',
        'latitude'       => 'float',
        'longitude'      => 'float',
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

    public function getImageUrlAttribute(): string
    {
        if ($this->image && str_starts_with($this->image, 'http')) {
            return $this->image;
        }
        return $this->image
            ? asset('storage/' . $this->image)
            : asset('images/hotel-placeholder.jpg');
    }
}
