<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'avatar', 'phone', 'nationality', 'date_of_birth', 'gender',
        'bio', 'travel_interests', 'accessibility_needs', 'preferred_language',
        'preferred_currency', 'loyalty_level', 'total_points', 'total_trips',
        'behavioral_vector', 'passport_number', 'passport_expiry', 'emergency_contacts',
    ];

    protected $casts = [
        'travel_interests'   => 'array',
        'accessibility_needs'=> 'array',
        'behavioral_vector'  => 'array',
        'emergency_contacts' => 'array',
        'date_of_birth'      => 'date',
        'passport_expiry'    => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getLoyaltyLevelNameAttribute(): string
    {
        return match ($this->loyalty_level) {
            2 => 'Silver',
            3 => 'Gold',
            4 => 'Diamond',
            default => 'Bronze',
        };
    }

    public function getLoyaltyBadgeColorAttribute(): string
    {
        return match ($this->loyalty_level) {
            2 => '#C0C0C0',
            3 => '#FFD700',
            4 => '#00BFFF',
            default => '#CD7F32',
        };
    }
}
