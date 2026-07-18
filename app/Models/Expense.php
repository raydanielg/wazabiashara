<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = ['business_id', 'branch_id', 'category', 'description', 'amount', 'receipt_image', 'user_id', 'expense_date'];

    protected $casts = ['expense_date' => 'date'];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
