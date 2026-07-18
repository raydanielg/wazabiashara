<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    protected $fillable = ['business_id', 'branch_id', 'user_id', 'opening_float', 'closing_cash', 'expected_cash', 'variance', 'status', 'opened_at', 'closed_at', 'note'];

    protected $casts = ['opened_at' => 'datetime', 'closed_at' => 'datetime'];

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

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
