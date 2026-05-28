<?php namespace App\Models;
use MongoDB\Laravel\Eloquent\Model;

class SupportTicket extends Model
{
    protected $fillable = [
        'user_id','name','email','subject','message','status','priority',
        'admin_response','assigned_to','resolved_at',
    ];
    protected $casts = ['resolved_at'=>'datetime'];
    public function user()       { return $this->belongsTo(User::class); }
    public function assignedTo() { return $this->belongsTo(User::class,'assigned_to'); }
}
