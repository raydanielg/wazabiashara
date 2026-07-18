<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashFlow extends Model
{
    protected $fillable = ['business_id', 'branch_id', 'account_id', 'direction', 'category', 'description', 'amount', 'reference', 'flow_date'];

    protected $casts = ['flow_date' => 'date'];

    public function business() { return $this->belongsTo(Business::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function account() { return $this->belongsTo(Account::class); }
}
