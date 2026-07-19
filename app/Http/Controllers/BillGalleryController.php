<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BillGalleryController extends Controller
{
    public function index()
    {
        $bills = Bill::where('business_id', auth()->user()->business_id)
            ->orderByDesc('id')
            ->paginate(24);

        return view('bill-gallery.index', compact('bills'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120',
            'title' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'bill_date' => 'nullable|date',
        ]);

        $bill = Bill::create([
            'business_id' => auth()->user()->business_id,
            'user_id' => auth()->id(),
            'image_path' => $request->file('image')->store('bills', 'public'),
            'title' => $request->title,
            'notes' => $request->notes,
            'bill_date' => $request->bill_date,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Risiti imepakiwa!', 'bill' => $bill]);
        }

        return redirect()->route('bill-gallery.index')->with('success', 'Risiti imepakiwa!');
    }

    public function destroy(Bill $bill)
    {
        if ($bill->business_id !== auth()->user()->business_id) abort(403);

        if ($bill->image_path) {
            Storage::disk('public')->delete($bill->image_path);
        }
        $bill->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Risiti imefutwa.']);
        }

        return redirect()->route('bill-gallery.index')->with('success', 'Risiti imefutwa.');
    }
}
