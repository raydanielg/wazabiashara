<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalAccessToken extends Model
{
    protected $fillable = [
        'tokenable_type',
        'tokenable_id',
        'name',
        'token',
        'abilities',
        'last_used_at',
        'expires_at',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected $hidden = ['token'];

    public function tokenable()
    {
        return $this->morphTo();
    }

    /**
     * Determine if the token has a given ability.
     */
    public function can($ability): bool
    {
        $abilities = $this->abilities ? json_decode($this->abilities, true) : ['*'];

        if (!is_array($abilities)) {
            return true;
        }

        return in_array('*', $abilities) || in_array($ability, $abilities);
    }

    /**
     * Determine if the token has expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }
}
