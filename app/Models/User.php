<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Added for custom role management in MongoDB
    protected $attributes = [
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'facebook_id',
        'avatar',
        'is_active',
        'last_login_at',
        'role',
        'guide_status',
        'guide_specialty',
        'guide_phone',
        'guide_experience',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'google_id',
        'facebook_id',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    public function getRoleNames(): array
    {
        $roles = $this->getRawOriginal('roles');
        if (is_array($roles)) return $roles;
        if (is_string($roles)) {
            $decoded = json_decode($roles, true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }

    // ── Relationships ─────────────────────────────────────────────────

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function itineraries()
    {
        return $this->hasMany(Itinerary::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function travelPosts()
    {
        return $this->hasMany(TravelPost::class);
    }

    public function loyaltyPoints()
    {
        return $this->hasMany(LoyaltyPoint::class);
    }

    public function notifications()
    {
        return $this->hasMany(TravelNotification::class);
    }

    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function priceAlerts()
    {
        return $this->hasMany(PriceAlert::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────

    public function hasRole($roles): bool
    {
        $userRoles = $this->getRoleNames();
        
        if (is_string($roles)) {
            return in_array($roles, $userRoles);
        }
        
        if (is_array($roles)) {
            return count(array_intersect($roles, $userRoles)) > 0;
        }
        
        return false;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(['super_admin', 'admin']);
    }

    public function isGuide(): bool
    {
        return ($this->role ?? 'user') === 'guide';
    }

    public function isApprovedGuide(): bool
    {
        return $this->isGuide() && ($this->guide_status ?? '') === 'approved';
    }

    public function isTraveler(): bool
    {
        return $this->hasRole('traveler');
    }

    public function getLoyaltyLevelNameAttribute(): string
    {
        $levels = [1 => 'Bronze', 2 => 'Silver', 3 => 'Gold', 4 => 'Diamond'];
        return $levels[$this->profile?->loyalty_level ?? 1] ?? 'Bronze';
    }

    public function getTotalPointsAttribute(): int
    {
        return $this->profile?->total_points ?? 0;
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->profile?->avatar) {
            return asset('storage/' . $this->profile->avatar);
        }
        if ($this->avatar) {
            return $this->avatar;
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=0d6efd&color=fff&size=128';
    }
}
