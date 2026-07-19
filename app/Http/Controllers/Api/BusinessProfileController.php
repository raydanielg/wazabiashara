<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Branch;
use App\Models\Business;
use App\Models\BusinessType;
use App\Models\PrinterSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BusinessProfileController extends Controller
{
    /**
     * Active business types, for the "Business Setup" step's type picker.
     * Public-ish (behind auth.token like everything else) — just a lookup.
     */
    public function types(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => BusinessType::active()->get(),
        ]);
    }

    /**
     * Create the Business + default Branch for a freshly-registered user and
     * attach them as its owner. Mirrors BusinessController::store on the web
     * side — the mobile app has no separate "register" step, so this runs
     * right after account registration/first login when business_id is null.
     */
    public function register(Request $request)
    {
        $user = $request->user();

        if ($user->business_id) {
            return response()->json([
                'success' => false,
                'message' => 'Akaunti yako tayari ina biashara iliyosajiliwa.',
            ], 422);
        }

        $validSlugs = BusinessType::active()->pluck('slug')->toArray();

        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'business_type' => $validSlugs ? 'required|string|in:' . implode(',', $validSlugs) : 'nullable|string',
            'region' => 'required|string|max:255',
            'business_phone' => 'required|string|max:20',
            'business_email' => 'nullable|email|max:255',
            'branch_name' => 'required|string|max:255',
            'branch_location' => 'required|string|max:255',
        ]);

        $business = Business::create([
            'name' => $validated['business_name'],
            'type' => $validated['business_type'] ?? null,
            'region' => $validated['region'],
            'phone' => $validated['business_phone'],
            'email' => $validated['business_email'] ?? null,
            'plan' => 'starter',
            'status' => 'active',
            'trial_ends_at' => now()->addDays(14),
        ]);

        $branch = Branch::create([
            'business_id' => $business->id,
            'name' => $validated['branch_name'],
            'location' => $validated['branch_location'],
            'phone' => $validated['business_phone'],
            'status' => 'active',
        ]);

        $user->update([
            'business_id' => $business->id,
            'branch_id' => $branch->id,
            'role' => 'business_admin',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Biashara yako imesajiliwa! Karibu kwenye Wazabiashara.',
            'user' => $user->fresh()->load('business', 'branch'),
        ], 201);
    }

    public function show(Request $request)
    {
        $business = $request->user()->business;

        if (!$business) {
            return response()->json([
                'success' => false,
                'message' => 'Mtumiaji hana biashara iliyosajiliwa.',
            ], 404);
        }

        $accounts = Account::where('business_id', $business->id)->get();
        $printerSetting = $business->printerSetting ?: new PrinterSetting(['business_id' => $business->id]);

        return response()->json([
            'success' => true,
            'business' => $business,
            'accounts' => $accounts,
            'printerSetting' => $printerSetting,
        ]);
    }

    public function update(Request $request)
    {
        $business = $request->user()->business;

        if (!$business) {
            return response()->json([
                'success' => false,
                'message' => 'Mtumiaji hana biashara iliyosajiliwa.',
            ], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'owner_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'tax_number' => 'nullable|string|max:100',
            'registration_number' => 'nullable|string|max:100',
            'website' => 'nullable|string|max:255',
            'vat_rate' => 'nullable|numeric|min:0|max:100',
            'currency' => 'nullable|string|max:10',
            'logo' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'owner_name', 'phone', 'email', 'address', 'tax_number', 'registration_number', 'website', 'vat_rate', 'currency']);

        if ($request->hasFile('logo')) {
            if ($business->logo) {
                Storage::delete('public/' . $business->logo);
            }
            $data['logo'] = $request->file('logo')->store('business-logos', 'public');
        }

        $business->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Wasifu wa biashara umesasishwa!',
            'business' => $business->fresh(),
        ]);
    }
}
