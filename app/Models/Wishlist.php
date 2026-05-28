<?php namespace App\Models;
use MongoDB\Laravel\Eloquent\Model;

class Wishlist extends Model
{
    protected $fillable = ['user_id','destination_id','package_id','wishlistable_type'];
    public function user()        { return $this->belongsTo(User::class); }
    public function destination() { return $this->belongsTo(Destination::class); }
    public function package()     { return $this->belongsTo(Package::class); }
}
