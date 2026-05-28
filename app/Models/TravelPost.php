<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class TravelPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'destination_id', 'title', 'body', 'photos',
        'latitude', 'longitude', 'likes_count', 'comments_count', 'is_public',
    ];

    protected $casts = [
        'photos'    => 'array',
        'is_public' => 'boolean',
        'latitude'  => 'float',
        'longitude' => 'float',
    ];

    public function user()        { return $this->belongsTo(User::class); }
    public function destination() { return $this->belongsTo(Destination::class); }
    public function likes()       { return $this->hasMany(PostLike::class); }
    public function comments()    { return $this->hasMany(PostComment::class); }

    public function isLikedBy(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }
}
