<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GreetingCard extends Model
{
    protected $fillable = ['business_id', 'customer_id', 'type', 'title', 'message', 'image', 'share_token'];

    public function business() { return $this->belongsTo(Business::class); }
    public function customer() { return $this->belongsTo(Customer::class); }

    protected static function booted()
    {
        static::creating(function ($card) {
            $card->share_token = \Illuminate\Support\Str::random(32);
        });
    }
}
