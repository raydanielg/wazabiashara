<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
}
