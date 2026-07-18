<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $fillable = ['business_id', 'branch_id', 'user_id', 'customer_id', 'quotation_no', 'subtotal', 'discount', 'vat', 'total', 'status', 'notes', 'valid_until', 'converted_at'];

    protected $casts = ['valid_until' => 'date', 'converted_at' => 'datetime'];

    public function business() { return $this->belongsTo(Business::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function items() { return $this->hasMany(QuotationItem::class); }
}
