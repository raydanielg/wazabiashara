<?php

namespace App\Http\Controllers;

use App\Models\PrinterSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /** Default values shown when a business has no saved app settings yet. */
    private function defaultAppSettings(): array
    {
        return [
            'appearance' => 'light',
            'font_size' => 'normal',
            'language' => 'en',
            'currency' => 'TSh',
            'currency_position' => 'start',
            'date_format' => 'd/m/Y',
            'time_format' => '24h',
            'number_format' => '1,234.56',
            'privacy_mode' => false,
            'app_lock' => false,
        ];
    }

    private function defaultTransactionSettings(): array
    {
        return [
            'cash_sale_default' => true,
            'due_date_reminder' => true,
            'other_income_transactions' => false,
            'transaction_prefixes' => false,
            'additional_charges' => false,
            'round_off' => false,
            'save_images_gallery' => true,
            'image_cropping' => true,
            'reminder_language' => 'en',
        ];
    }

    public function app()
    {
        $business = auth()->user()->business;
        $settings = array_merge($this->defaultAppSettings(), $business->settings['app'] ?? []);

        return view('settings.app', compact('business', 'settings'));
    }

    public function updateApp(Request $request)
    {
        $business = auth()->user()->business;

        $request->validate([
            'appearance' => 'nullable|in:light,dark',
            'font_size' => 'nullable|in:small,normal,large',
            'language' => 'nullable|in:sw,en',
            'currency' => 'nullable|string|max:10',
            'currency_position' => 'nullable|in:start,end',
            'date_format' => 'nullable|string|max:30',
            'time_format' => 'nullable|in:12h,24h',
            'number_format' => 'nullable|string|max:30',
            'privacy_mode' => 'nullable|boolean',
            'app_lock' => 'nullable|boolean',
        ]);

        $current = $business->settings ?? [];
        $current['app'] = array_merge($this->defaultAppSettings(), $current['app'] ?? [], [
            'appearance' => $request->appearance ?? 'light',
            'font_size' => $request->font_size ?? 'normal',
            'language' => $request->language ?? 'en',
            'currency' => $request->currency ?? 'TSh',
            'currency_position' => $request->currency_position ?? 'start',
            'date_format' => $request->date_format ?? 'd/m/Y',
            'time_format' => $request->time_format ?? '24h',
            'number_format' => $request->number_format ?? '1,234.56',
            'privacy_mode' => $request->boolean('privacy_mode'),
            'app_lock' => $request->boolean('app_lock'),
        ]);

        $business->update(['settings' => $current]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Mipangilio ya programu imesasishwa!']);
        }

        return redirect()->route('settings.app')->with('success', 'Mipangilio ya programu imesasishwa!');
    }

    public function invoice()
    {
        $business = auth()->user()->business;
        $printerSetting = $business->printerSetting ?: new PrinterSetting(['business_id' => $business->id]);

        return view('settings.invoice', compact('printerSetting'));
    }

    public function updateInvoice(Request $request)
    {
        $business = auth()->user()->business;

        $request->validate([
            'print_type' => 'nullable|in:regular,thermal',
            'receipt_size' => 'nullable|string|max:20',
            'terms_conditions' => 'nullable|string',
            'signature_image' => 'nullable|image|max:2048',
            'show_phone' => 'nullable|boolean',
            'show_address' => 'nullable|boolean',
            'show_email' => 'nullable|boolean',
            'show_signature' => 'nullable|boolean',
            'show_party_balance' => 'nullable|boolean',
        ]);

        $setting = $business->printerSetting ?: PrinterSetting::create(['business_id' => $business->id]);

        $data = [
            'print_type' => $request->print_type ?? 'thermal',
            'receipt_size' => $request->receipt_size ?? '80mm',
            'terms_conditions' => $request->terms_conditions,
            'show_phone' => $request->boolean('show_phone'),
            'show_address' => $request->boolean('show_address'),
            'show_email' => $request->boolean('show_email'),
            'show_signature' => $request->boolean('show_signature'),
            'show_party_balance' => $request->boolean('show_party_balance'),
        ];

        if ($request->hasFile('signature_image')) {
            $data['signature_image'] = $request->file('signature_image')->store('signatures', 'public');
        }

        $setting->update($data);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Mipangilio ya risiti imesasishwa!']);
        }

        return redirect()->route('settings.invoice')->with('success', 'Mipangilio ya risiti imesasishwa!');
    }

    public function transaction()
    {
        $business = auth()->user()->business;
        $settings = array_merge($this->defaultTransactionSettings(), $business->settings['transaction'] ?? []);

        return view('settings.transaction', compact('settings'));
    }

    public function updateTransaction(Request $request)
    {
        $business = auth()->user()->business;

        $request->validate([
            'cash_sale_default' => 'nullable|boolean',
            'due_date_reminder' => 'nullable|boolean',
            'other_income_transactions' => 'nullable|boolean',
            'transaction_prefixes' => 'nullable|boolean',
            'additional_charges' => 'nullable|boolean',
            'round_off' => 'nullable|boolean',
            'save_images_gallery' => 'nullable|boolean',
            'image_cropping' => 'nullable|boolean',
            'reminder_language' => 'nullable|in:sw,en',
        ]);

        $current = $business->settings ?? [];
        $current['transaction'] = [
            'cash_sale_default' => $request->boolean('cash_sale_default'),
            'due_date_reminder' => $request->boolean('due_date_reminder'),
            'other_income_transactions' => $request->boolean('other_income_transactions'),
            'transaction_prefixes' => $request->boolean('transaction_prefixes'),
            'additional_charges' => $request->boolean('additional_charges'),
            'round_off' => $request->boolean('round_off'),
            'save_images_gallery' => $request->boolean('save_images_gallery'),
            'image_cropping' => $request->boolean('image_cropping'),
            'reminder_language' => $request->reminder_language ?? 'en',
        ];

        $business->update(['settings' => $current]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Mipangilio ya miamala imesasishwa!']);
        }

        return redirect()->route('settings.transaction')->with('success', 'Mipangilio ya miamala imesasishwa!');
    }
}
