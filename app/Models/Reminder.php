<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    protected $fillable = ['business_id', 'user_id', 'type', 'title', 'message', 'channel', 'remindable_type', 'remindable_id', 'remind_at', 'status'];

    protected $casts = ['remind_at' => 'datetime'];

    public function business() { return $this->belongsTo(Business::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function remindable() { return $this->morphTo(); }
}
