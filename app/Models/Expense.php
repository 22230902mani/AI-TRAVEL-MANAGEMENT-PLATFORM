<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'itinerary_id', 'category', 'amount', 'currency',
        'description', 'receipt_image', 'expense_date', 'payment_method',
        'is_shared', 'split_with',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'split_with'   => 'array',
        'is_shared'    => 'boolean',
        'amount'       => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function itinerary()
    {
        return $this->belongsTo(Itinerary::class);
    }

    public static function getCategoryIcon(string $category): string
    {
        return match ($category) {
            'accommodation' => '🏨',
            'food'          => '🍽️',
            'transport'     => '🚗',
            'activity'      => '🎯',
            'shopping'      => '🛍️',
            'health'        => '💊',
            default         => '💳',
        };
    }
}
