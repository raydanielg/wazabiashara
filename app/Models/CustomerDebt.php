<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerDebt extends Model
{
    protected $fillable = ['customer_id', 'sale_id', 'business_id', 'amount', 'balance', 'due_date', 'status', 'note'];

    protected $casts = ['due_date' => 'date'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function payments()
    {
        return $this->hasMany(DebtPayment::class);
    }
}
