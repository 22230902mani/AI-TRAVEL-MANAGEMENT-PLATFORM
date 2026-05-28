<?php namespace App\Models;
use MongoDB\Laravel\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = [
        'code','title','description','discount_type','discount_value',
        'min_booking_amount','usage_limit','used_count','valid_from','valid_until','is_active',
    ];
    protected $casts = [
        'valid_from'=>'date','valid_until'=>'date',
        'is_active'=>'boolean','discount_value'=>'float',
        'min_booking_amount'=>'float',
    ];

    public function isValid(): bool
    {
        return $this->is_active
            && now()->between($this->valid_from, $this->valid_until)
            && (is_null($this->usage_limit) || $this->used_count < $this->usage_limit);
    }

    public function calculateDiscount(float $amount): float
    {
        if ($amount < $this->min_booking_amount) return 0;
        return $this->discount_type === 'percent'
            ? round($amount * $this->discount_value / 100, 2)
            : min($this->discount_value, $amount);
    }
}
