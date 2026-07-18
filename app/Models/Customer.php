<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['business_id', 'name', 'phone', 'email', 'address', 'credit_limit', 'current_debt', 'status'];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function debts()
    {
        return $this->hasMany(CustomerDebt::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
