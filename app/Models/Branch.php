<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['business_id', 'name', 'location', 'phone', 'status'];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function stock()
    {
        return $this->hasMany(BranchStock::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
