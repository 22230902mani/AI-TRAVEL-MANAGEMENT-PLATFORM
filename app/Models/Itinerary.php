<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Support\Str;

class Itinerary extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'destination_id', 'title', 'description', 'start_date', 'end_date',
        'duration_days', 'budget', 'spent', 'status', 'travel_style', 'days',
        'preferences', 'group_members', 'is_public', 'is_collaborative', 'share_token',
        'is_paid',
    ];

    protected $casts = [
        'start_date'    => 'date',
        'end_date'      => 'date',
        'days'          => 'array',
        'preferences'   => 'array',
        'group_members' => 'array',
        'is_public'     => 'boolean',
        'is_collaborative' => 'boolean',
        'is_paid'       => 'boolean',
        'budget'        => 'float',
        'spent'         => 'float',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($itinerary) {
            if (! $itinerary->share_token) {
                $itinerary->share_token = Str::random(32);
            }
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

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function getBudgetRemainingAttribute(): float
    {
        return max(0, ($this->budget ?? 0) - $this->spent);
    }

    public function getBudgetUsedPercentAttribute(): float
    {
        if (! $this->budget || $this->budget == 0) return 0;
        return min(100, round(($this->spent / $this->budget) * 100, 1));
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
