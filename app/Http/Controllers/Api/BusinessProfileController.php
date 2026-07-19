<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\PrinterSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BusinessProfileController extends Controller
{
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
