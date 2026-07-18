<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessCard extends Model
{
    protected $fillable = ['business_id', 'card_name', 'owner_name', 'phone', 'email', 'address', 'website', 'logo', 'social_media', 'share_token'];

    protected $casts = ['social_media' => 'array'];

    public function business() { return $this->belongsTo(Business::class); }

    protected static function booted()
    {
        static::creating(function ($card) {
            $card->share_token = \Illuminate\Support\Str::random(32);
        });
    }
}
