<?php namespace App\Models;
use MongoDB\Laravel\Eloquent\Model;

class LoyaltyPoint extends Model
{
    protected $fillable = ['user_id','points','type','description','booking_id'];
    public function user()    { return $this->belongsTo(User::class); }
    public function booking() { return $this->belongsTo(Booking::class); }
}
