<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = ['business_id', 'name', 'type', 'bank_name', 'account_number', 'phone_number', 'opening_balance', 'current_balance', 'is_active'];

    public function business() { return $this->belongsTo(Business::class); }
    public function cashFlows() { return $this->hasMany(CashFlow::class); }
}
