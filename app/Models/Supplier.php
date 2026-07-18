<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['business_id', 'name', 'phone', 'email', 'address', 'balance', 'status'];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
