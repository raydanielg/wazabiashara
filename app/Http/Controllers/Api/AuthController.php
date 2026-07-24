<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class AuthController extends Controller
{
    /**
     * Register a new user account (mirrors RegisterController::ajaxRegister validation rules).
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^255[67]\d{8}$/', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Taarifa si sahihi.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'role' => 'user',
            'password' => Hash::make($request->password),
        ]);

        // Send welcome email
        Mail::send('emails.welcome', ['user' => $user], function ($message) use ($user) {
            $message->to($user->email, $user->name);
            $message->subject('Karibu Wazabiashara!');
        });

        $tokenResult = $user->createToken('mobile-app');

        return response()->json([
            'success' => true,
            'message' => 'Akaunti imeundwa! Karibu, ' . $user->name . '!',
            'user' => $user,
            'token' => $tokenResult->plainTextToken,
        ], 201);
    }

    /**
     * Authenticate a user and issue a new personal access token.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email au nenosiri si sahihi.',
            ], 401);
        }

        if (($user->status ?? 'active') !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Akaunti yako imezimwa.',
            ], 401);
        }

        $tokenResult = $user->createToken('mobile-app');

        return response()->json([
            'success' => true,
            'message' => 'Karibu tena, ' . $user->name . '!',
            'user' => $user->load('business', 'branch'),
            'token' => $tokenResult->plainTextToken,
        ]);
    }

    /**
     * Revoke the token used for the current request.
     */
    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken();

        if ($token) {
            $token->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Umetoka kikamilifu.',
        ]);
    }

    /**
     * Return the currently authenticated user and their business.
     */
    public function me(Request $request)
    {
        $user = $request->user()->load('business', 'branch');

        return response()->json([
            'success' => true,
            'user' => $user,
        ]);
    }

    /**
     * Update the signed-in user's own account details (name/email/phone).
     * Distinct from BusinessProfileController — that edits the business,
     * this edits the person signed in.
     */
    public function updateMe(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'phone' => ['sometimes', 'required', 'string', 'regex:/^255[67]\d{8}$/', 'unique:users,phone,' . $user->id],
            'email' => ['sometimes', 'nullable', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Taarifa si sahihi.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Taarifa zako zimesasishwa.',
            'user' => $user->fresh()->load('business', 'branch'),
        ]);
    }

    /**
     * Change the signed-in user's password (requires the current password).
     */
    public function changePassword(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Taarifa si sahihi.',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Nenosiri la sasa si sahihi.',
            ], 422);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return response()->json([
            'success' => true,
            'message' => 'Nenosiri limebadilishwa kikamilifu.',
        ]);
    }

    /**
     * Send a password reset link to the user's email.
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Tafadhali ingiza barua pepe sahihi.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        // Always return success to avoid email enumeration
        if (!$user) {
            return response()->json([
                'success' => true,
                'message' => 'Tumekutumia kiungo cha kubadilisha nenosiri kwenye barua pepe yako.',
            ]);
        }

        // Generate reset token
        $token = Str::random(64);

        // Store token in cache for 60 minutes
        cache()->put('password_reset_' . $token, [
            'user_id' => $user->id,
            'email' => $user->email,
            'created_at' => Carbon::now(),
        ], now()->addMinutes(60));

        // Send reset email
        Mail::send('emails.password-reset', ['token' => $token, 'user' => $user], function ($message) use ($user) {
            $message->to($user->email, $user->name);
            $message->subject('Wazabiashara - Badilisha Nenosiri');
        });

        return response()->json([
            'success' => true,
            'message' => 'Tumekutumia kiungo cha kubadilisha nenosiri kwenye barua pepe yako.',
        ]);
    }

    /**
     * Reset password using token.
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Taarifa si sahihi.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $resetData = cache()->get('password_reset_' . $request->token);

        if (!$resetData) {
            return response()->json([
                'success' => false,
                'message' => 'Kiungo cha kubadilisha nenosiri halihalali au kimekwisha muda wake.',
            ], 400);
        }

        $user = User::find($resetData['user_id']);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Mtumiaji hakupatikana.',
            ], 404);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Delete used token
        cache()->forget('password_reset_' . $request->token);

        // Revoke all existing tokens
        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Nenosiri limebadilishwa kikamilifu. Unaweza kuingia sasa.',
        ]);
    }
}
