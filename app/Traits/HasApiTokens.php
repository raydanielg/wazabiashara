<?php

namespace App\Traits;

use App\Models\PersonalAccessToken;
use Illuminate\Support\Str;

trait HasApiTokens
{
    /**
     * The access token the user is currently authenticating with (set by ApiTokenAuth middleware).
     */
    protected ?PersonalAccessToken $currentAccessToken = null;

    /**
     * All personal access tokens issued to this model.
     */
    public function tokens()
    {
        return $this->morphMany(PersonalAccessToken::class, 'tokenable');
    }

    /**
     * Create a new personal access token for this model.
     *
     * Generates a random 64-character plain-text token, stores only its
     * SHA-256 hash in the database, and returns an object exposing both
     * the token model (accessToken) and the one-time plain-text value
     * (plainTextToken) that must be shown to the caller now — it cannot
     * be recovered later since only the hash is persisted.
     */
    public function createToken(string $name, array $abilities = ['*'], $expiresAt = null): object
    {
        $plainTextToken = Str::random(64);

        $token = $this->tokens()->create([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken),
            'abilities' => $abilities ? json_encode($abilities) : null,
            'expires_at' => $expiresAt,
        ]);

        return (object) [
            'accessToken' => $token,
            'plainTextToken' => $plainTextToken,
        ];
    }

    /**
     * Set the access token used for the current request (called by ApiTokenAuth middleware).
     */
    public function withAccessToken(PersonalAccessToken $token): static
    {
        $this->currentAccessToken = $token;

        return $this;
    }

    /**
     * Get the access token currently authenticating this request, if any.
     */
    public function currentAccessToken(): ?PersonalAccessToken
    {
        return $this->currentAccessToken;
    }
}
