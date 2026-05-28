<?php namespace App\Models;
use MongoDB\Laravel\Eloquent\Model;

class PostComment extends Model
{
    protected $fillable = ['user_id', 'travel_post_id', 'body'];
    public function user() { return $this->belongsTo(User::class); }
    public function post() { return $this->belongsTo(TravelPost::class, 'travel_post_id'); }
}
