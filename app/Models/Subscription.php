<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = ['business_id', 'plan', 'amount', 'status', 'starts_at', 'ends_at', 'payment_method', 'payment_ref'];

    protected $casts = ['starts_at' => 'date', 'ends_at' => 'date'];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
