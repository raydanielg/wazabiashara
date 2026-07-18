<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['business_id', 'branch_id', 'user_id', 'type', 'category', 'description', 'amount', 'payment_method', 'reference', 'payable_type', 'payable_id', 'payment_date'];

    protected $casts = ['payment_date' => 'date'];

    public function business() { return $this->belongsTo(Business::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function payable() { return $this->morphTo(); }
}
