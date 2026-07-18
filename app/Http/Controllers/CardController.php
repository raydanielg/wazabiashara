<?php

namespace App\Http\Controllers;

use App\Models\GreetingCard;
use App\Models\BusinessCard;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CardController extends Controller
{
    public function greetingIndex()
    {
        $user = auth()->user();
        $businessId = $user->business_id;

        $cards = GreetingCard::where('business_id', $businessId)
            ->with('customer')
            ->orderByDesc('id')
            ->paginate(20);

        $customers = Customer::where('business_id', $businessId)->where('status', 'active')->orderBy('name')->get();
        $totalCards = GreetingCard::where('business_id', $businessId)->count();
        $birthdayCards = GreetingCard::where('business_id', $businessId)->where('type', 'birthday')->count();
        $holidayCards = GreetingCard::where('business_id', $businessId)->where('type', 'holiday')->count();
        $appreciationCards = GreetingCard::where('business_id', $businessId)->where('type', 'appreciation')->count();

        return view('cards.greeting', compact('cards', 'customers', 'totalCards', 'birthdayCards', 'holidayCards', 'appreciationCards'));
    }

    public function greetingStore(Request $request)
    {
        $request->validate([
            'type' => 'required|in:birthday,holiday,appreciation',
            'title' => 'required|string|max:255',
            'message' => 'nullable|string|max:1000',
            'customer_id' => 'nullable|exists:customers,id',
            'image' => 'nullable|image|max:2048',
        ]);

        $card = GreetingCard::create([
            'business_id' => auth()->user()->business_id,
            'customer_id' => $request->customer_id,
            'type' => $request->type,
            'title' => $request->title,
            'message' => $request->message,
        ]);

        if ($request->hasFile('image')) {
            $card->update(['image' => $request->file('image')->store('greeting-cards', 'public')]);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Kadi imetengenezwa!', 'share_url' => route('cards.greeting.share', $card->share_token)]);
        }
        return redirect()->route('cards.greeting')->with('success', 'Kadi imetengenezwa!');
    }

    public function greetingShare($token)
    {
        $card = GreetingCard::where('share_token', $token)->with(['business', 'customer'])->firstOrFail();
        return view('cards.greeting-share', compact('card'));
    }

    public function greetingDestroy(GreetingCard $card)
    {
        if ($card->business_id !== auth()->user()->business_id) abort(403);
        if ($card->image) Storage::delete('public/' . $card->image);
        $card->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Kadi imefutwa.']);
        }
        return redirect()->route('cards.greeting')->with('success', 'Kadi imefutwa.');
    }

    public function businessIndex()
    {
        $user = auth()->user();
        $businessId = $user->business_id;

        $cards = BusinessCard::where('business_id', $businessId)->orderByDesc('id')->get();
        return view('cards.business', compact('cards'));
    }

    public function businessStore(Request $request)
    {
        $request->validate([
            'card_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'address' => 'nullable|string|max:500',
            'website' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'social_facebook' => 'nullable|string',
            'social_instagram' => 'nullable|string',
            'social_twitter' => 'nullable|string',
            'social_linkedin' => 'nullable|string',
        ]);

        $social = [];
        if ($request->social_facebook) $social['facebook'] = $request->social_facebook;
        if ($request->social_instagram) $social['instagram'] = $request->social_instagram;
        if ($request->social_twitter) $social['twitter'] = $request->social_twitter;
        if ($request->social_linkedin) $social['linkedin'] = $request->social_linkedin;

        $card = BusinessCard::create([
            'business_id' => auth()->user()->business_id,
            'card_name' => $request->card_name,
            'owner_name' => $request->owner_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'website' => $request->website,
            'social_media' => $social,
        ]);

        if ($request->hasFile('logo')) {
            $card->update(['logo' => $request->file('logo')->store('business-cards', 'public')]);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Kadi ya biashara imetengenezwa!', 'share_url' => route('cards.business.share', $card->share_token)]);
        }
        return redirect()->route('cards.business')->with('success', 'Kadi ya biashara imetengenezwa!');
    }

    public function businessShare($token)
    {
        $card = BusinessCard::where('share_token', $token)->with('business')->firstOrFail();
        return view('cards.business-share', compact('card'));
    }

    public function businessDestroy(BusinessCard $card)
    {
        if ($card->business_id !== auth()->user()->business_id) abort(403);
        if ($card->logo) Storage::delete('public/' . $card->logo);
        $card->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Kadi imefutwa.']);
        }
        return redirect()->route('cards.business')->with('success', 'Kadi imefutwa.');
    }
}
