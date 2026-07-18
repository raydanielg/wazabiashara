<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Account;
use App\Models\PrinterSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BusinessProfileController extends Controller
{
    public function show()
    {
        $business = auth()->user()->business;
        $accounts = Account::where('business_id', $business->id)->get();
        $printerSetting = $business->printerSetting ?: new PrinterSetting(['business_id' => $business->id]);

        return view('business.profile', compact('business', 'accounts', 'printerSetting'));
    }

    public function update(Request $request)
    {
        $business = auth()->user()->business;

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
            if ($business->logo) Storage::delete('public/' . $business->logo);
            $data['logo'] = $request->file('logo')->store('business-logos', 'public');
        }

        $business->update($data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Wasifu wa biashara umesasishwa!']);
        }
        return redirect()->route('business.profile')->with('success', 'Wasifu wa biashara umesasishwa!');
    }

    public function updatePrinter(Request $request)
    {
        $business = auth()->user()->business;

        $request->validate([
            'printer_type' => 'required|in:thermal,bluetooth,network,a4',
            'receipt_size' => 'required|string',
            'logo_position' => 'required|string',
            'footer_message' => 'nullable|string|max:500',
            'show_qr' => 'boolean',
            'show_signature' => 'boolean',
            'show_stamp' => 'boolean',
        ]);

        $setting = $business->printerSetting ?: PrinterSetting::create(['business_id' => $business->id]);
        $setting->update($request->only(['printer_type', 'receipt_size', 'logo_position', 'footer_message', 'show_qr', 'show_signature', 'show_stamp']));

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Mipangilio ya printer imesasishwa!']);
        }
        return redirect()->route('business.profile')->with('success', 'Mipangilio ya printer imesasishwa!');
    }
}
