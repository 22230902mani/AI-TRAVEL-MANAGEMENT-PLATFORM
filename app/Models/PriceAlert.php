<?php namespace App\Models;
use MongoDB\Laravel\Eloquent\Model;

class PriceAlert extends Model
{
    protected $fillable = [
        'user_id','package_id','destination_id','target_price','is_active','triggered_at',
    ];
    protected $casts = ['is_active'=>'boolean','triggered_at'=>'datetime','target_price'=>'float'];
    public function user()        { return $this->belongsTo(User::class); }
    public function package()     { return $this->belongsTo(Package::class); }
    public function destination() { return $this->belongsTo(Destination::class); }
}
