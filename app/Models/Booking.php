<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_reference', 'user_id', 'package_id', 'hotel_id', 'itinerary_id',
        'guide_id', 'booking_type', 'check_in', 'check_out', 'adults', 'children',
        'total_amount', 'paid_amount', 'payment_status', 'booking_status',
        'payment_method', 'transaction_id', 'special_requests', 'traveler_details',
        'qr_code', 'event_log', 'confirmed_at', 'cancelled_at', 'cancellation_reason',
        'razorpay_order_id', 'discount_applied', 'package_details_shared', 'complimentary_addons'
    ];

    protected $casts = [
        'check_in'         => 'date',
        'check_out'        => 'date',
        'traveler_details' => 'array',
        'event_log'        => 'array',
        'complimentary_addons' => 'array',
        'total_amount'     => 'float',
        'paid_amount'      => 'float',
        'confirmed_at'     => 'datetime',
        'cancelled_at'     => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($booking) {
            if (! $booking->booking_reference) {
                $booking->booking_reference = 'TM-' . strtoupper(Str::random(8));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function itinerary()
    {
        return $this->belongsTo(Itinerary::class);
    }

    public function guide()
    {
        return $this->belongsTo(User::class, 'guide_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function loyaltyPoints()
    {
        return $this->hasMany(LoyaltyPoint::class);
    }

    // Event-sourcing: append state change to event log
    public function appendEvent(string $event, array $data = []): void
    {
        $log   = $this->event_log ?? [];
        $log[] = [
            'event'     => $event,
            'data'      => $data,
            'timestamp' => now()->toISOString(),
            'user_id'   => auth()->id(),
        ];
        $this->update(['event_log' => $log]);
    }

    public function getAmountDueAttribute(): float
    {
        return max(0, $this->total_amount - $this->paid_amount);
    }

    public function getCheckInAttribute($value)
    {
        if (!$value && $this->itinerary_id) {
            return $this->itinerary?->start_date;
        }
        return $value;
    }

    public function getCheckOutAttribute($value)
    {
        if (!$value && $this->itinerary_id) {
            return $this->itinerary?->end_date;
        }
        return $value;
    }

    public function getAdultsAttribute($value)
    {
        if ((!$value || $value == 0) && $this->itinerary_id) {
            $groupType = $this->itinerary?->preferences['group_type'] ?? '';
            if ($groupType === 'solo') return 1;
            if ($groupType === 'couple') return 2;
            if ($groupType === 'family') return 3;
            if ($groupType === 'friends') return 4;
            return 1;
        }
        return $value;
    }

    public function getPassengerSummaryAttribute(): string
    {
        if ($this->itinerary_id) {
            $groupType = $this->itinerary?->preferences['group_type'] ?? 'solo';
            return ucfirst($groupType) . ' Traveler';
        }
        $summary = $this->adults . ' Adults';
        if ($this->children) {
            $summary .= ', ' . $this->children . ' Kids';
        }
        return $summary;
    }

    public function isConfirmed(): bool
    {
        return $this->booking_status === 'confirmed';
    }

    public function isCancelled(): bool
    {
        return $this->booking_status === 'cancelled';
    }

    public function scopeConfirmed($query)
    {
        return $query->where('booking_status', 'confirmed');
    }
}
