<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function ajaxLogin(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return response()->json([
                'success' => false,
                'message' => 'Email au nenosiri si sahihi.',
            ], 422);
        }

        $request->session()->regenerate();
        $user = Auth::user();

        if (!$user->business_id && !$user->isSuperAdmin()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Karibu tena, ' . $user->name . '!',
                'redirect' => '/business/register',
                'role'     => $user->role,
            ]);
        }

        $redirect = match($user->role) {
            'super_admin'  => '/home',
            'business_admin' => '/home',
            'manager'      => '/home',
            'cashier'      => '/pos',
            default        => '/home',
        };

        return response()->json([
            'success'  => true,
            'message'  => 'Karibu tena, ' . $user->name . '!',
            'redirect' => $redirect,
            'role'     => $user->role,
        ]);
    }
}

