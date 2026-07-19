<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Business extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'type', 'region', 'phone', 'email', 'logo', 'plan', 'status', 'vat_rate', 'currency', 'language', 'trial_ends_at', 'owner_name', 'address', 'tax_number', 'registration_number', 'website', 'social_media', 'dark_mode', 'settings'];

    protected $casts = ['trial_ends_at' => 'datetime', 'social_media' => 'array', 'dark_mode' => 'boolean', 'settings' => 'array'];

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

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function cashFlows()
    {
        return $this->hasMany(CashFlow::class);
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }

    public function greetingCards()
    {
        return $this->hasMany(GreetingCard::class);
    }

    public function businessCards()
    {
        return $this->hasMany(BusinessCard::class);
    }

    public function printerSetting()
    {
        return $this->hasOne(PrinterSetting::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }
}
