<?php namespace App\Models;
use MongoDB\Laravel\Eloquent\Model;

class TravelNotification extends Model
{
    protected $fillable = ['user_id','type','title','message','data','is_read'];
    protected $casts = ['data'=>'array','is_read'=>'boolean'];
    public function user() { return $this->belongsTo(User::class); }
}
