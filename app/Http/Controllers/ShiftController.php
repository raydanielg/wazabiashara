<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShiftController extends Controller
{
    public function index()
    {
        $businessId = auth()->user()->business_id;
        $shifts = Shift::where('business_id', $businessId)
            ->with(['user', 'branch'])
            ->orderByDesc('id')
            ->paginate(20);

        $activeShift = Shift::where('user_id', auth()->id())->where('status', 'open')->first();

        $totalShifts = Shift::where('business_id', $businessId)->count();
        $openShifts = Shift::where('business_id', $businessId)->where('status', 'open')->count();
        $todayShifts = Shift::where('business_id', $businessId)->whereDate('opened_at', today())->count();
        $totalVariance = Shift::where('business_id', $businessId)->where('status', 'closed')->sum('variance');

        return view('shifts.index', compact('shifts', 'activeShift', 'totalShifts', 'openShifts', 'todayShifts', 'totalVariance'));
    }

    public function open(Request $request)
    {
        $request->validate(['opening_float' => 'required|numeric|min:0']);

        $existing = Shift::where('user_id', auth()->id())->where('status', 'open')->first();
        if ($existing) {
            return redirect()->back()->with('error', 'Una zamu iliyo wazi tayari. Funga kwanza.');
        }

        Shift::create([
            'business_id' => auth()->user()->business_id,
            'branch_id' => session('active_branch_id') ?? auth()->user()->branch_id,
            'user_id' => auth()->id(),
            'opening_float' => $request->opening_float,
            'status' => 'open',
            'opened_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Zamu imefunguliwa!');
    }

    public function close(Request $request, Shift $shift)
    {
        if ($shift->user_id !== auth()->id() && !auth()->user()->isBusinessAdmin()) abort(403);

        $request->validate(['closing_cash' => 'required|numeric|min:0']);

        $cashSales = Sale::where('shift_id', $shift->id)->where('payment_method', 'cash')->where('status', 'completed')->sum('total');
        $expectedCash = $shift->opening_float + $cashSales;
        $variance = $request->closing_cash - $expectedCash;

        $shift->update([
            'closing_cash' => $request->closing_cash,
            'expected_cash' => $expectedCash,
            'variance' => $variance,
            'status' => 'closed',
            'closed_at' => now(),
            'note' => $request->note,
        ]);

        return redirect()->back()->with('success', "Zamu imefungwa. Tofauti: TZS " . number_format($variance, 0));
    }
}
