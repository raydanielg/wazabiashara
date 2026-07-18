<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Branch;
use App\Models\BusinessType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BusinessController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create()
    {
        if (auth()->user()->business_id) {
            return redirect()->route('home');
        }

        $businessTypes = BusinessType::active()->get();

        return view('business.register', compact('businessTypes'));
    }

    public function store(Request $request)
    {
        $validSlugs = BusinessType::active()->pluck('slug')->toArray();

        $validated = $request->validate([
            'business_name'     => ['required', 'string', 'max:255'],
            'business_type'     => ['required', 'string', 'in:' . implode(',', $validSlugs)],
            'region'            => ['required', 'string', 'max:255'],
            'business_phone'    => ['required', 'string', 'max:20'],
            'business_email'    => ['nullable', 'email', 'max:255'],
            'branch_name'       => ['required', 'string', 'max:255'],
            'branch_location'   => ['required', 'string', 'max:255'],
        ]);

        $business = Business::create([
            'name'   => $validated['business_name'],
            'type'   => $validated['business_type'],
            'region' => $validated['region'],
            'phone'  => $validated['business_phone'],
            'email'  => $validated['business_email'] ?? null,
            'plan'   => 'starter',
            'status' => 'active',
            'trial_ends_at' => now()->addDays(14),
        ]);

        $branch = Branch::create([
            'business_id' => $business->id,
            'name'        => $validated['branch_name'],
            'location'    => $validated['branch_location'],
            'phone'       => $validated['business_phone'],
            'status'      => 'active',
        ]);

        $user = auth()->user();
        $user->update([
            'business_id' => $business->id,
            'branch_id'   => $branch->id,
            'role'        => 'business_admin',
        ]);

        session(['active_branch_id' => $branch->id]);

        return response()->json([
            'success'  => true,
            'message'  => 'Biashara yako imesajiliwa! Karibu kwenye Wazabiashara.',
            'redirect' => '/home',
        ]);
    }
}
