<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = ['user_id', 'business_id', 'action', 'table', 'before', 'after', 'ip'];

    protected $casts = ['before' => 'array', 'after' => 'array'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
