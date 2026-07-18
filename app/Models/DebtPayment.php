<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DebtPayment extends Model
{
    protected $fillable = ['customer_debt_id', 'amount', 'method', 'reference', 'user_id'];

    public function debt()
    {
        return $this->belongsTo(CustomerDebt::class, 'customer_debt_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
