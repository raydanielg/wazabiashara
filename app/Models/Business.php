<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Business extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'type', 'region', 'phone', 'email', 'logo', 'plan', 'status', 'vat_rate', 'currency', 'language', 'trial_ends_at'];

    protected $casts = ['trial_ends_at' => 'datetime'];

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function stockTransfers()
    {
        return $this->hasMany(StockTransfer::class);
    }
}
