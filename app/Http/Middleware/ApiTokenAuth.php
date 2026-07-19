<?php

namespace App\Http\Middleware;

use App\Models\PersonalAccessToken;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiTokenAuth
{
    /**
     * Authenticate API requests using a custom Bearer token
     * (personal_access_tokens table), since Sanctum/Passport are not installed.
     */
    public function handle(Request $request, Closure $next)
    {
        $plainTextToken = $request->bearerToken();

        if (!$plainTextToken) {
            return response()->json([
                'success' => false,
                'message' => 'Hakuna token ya uthibitisho iliyotolewa (Unauthenticated).',
            ], 401);
        }

        $hashedToken = hash('sha256', $plainTextToken);

        $accessToken = PersonalAccessToken::where('token', $hashedToken)
            ->where('tokenable_type', User::class)
            ->first();

        if (!$accessToken) {
            return response()->json([
                'success' => false,
                'message' => 'Token si sahihi (Invalid token).',
            ], 401);
        }

        if ($accessToken->isExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'Token imeisha muda wake (Token expired).',
            ], 401);
        }

        $user = User::find($accessToken->tokenable_id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Mtumiaji hapatikani (User not found).',
            ], 401);
        }

        if (($user->status ?? 'active') !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Akaunti yako imezimwa (Account inactive).',
            ], 401);
        }

        $user->withAccessToken($accessToken);

        Auth::setUser($user);
        $request->setUserResolver(fn () => $user);

        $accessToken->forceFill(['last_used_at' => now()])->save();

        return $next($request);
    }
}
