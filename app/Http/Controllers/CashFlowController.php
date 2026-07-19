<?php

namespace App\Http\Controllers;

use App\Models\CashFlow;
use App\Models\Account;
use App\Models\Sale;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Payment;
use App\Models\Purchase;
use Illuminate\Http\Request;

class CashFlowController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $businessId = $user->business_id;

        $accounts = Account::where('business_id', $businessId)->where('is_active', true)->get();
        $cashAccount = $accounts->where('type', 'cash')->first();
        $bankAccounts = $accounts->where('type', 'bank');
        $mobileAccounts = $accounts->where('type', 'mobile_money');

        $cashBalance = $cashAccount?->current_balance ?? 0;
        $bankBalance = $bankAccounts->sum('current_balance');
        $mobileBalance = $mobileAccounts->sum('current_balance');

        $today = today();
        $startOfMonth = now()->startOfMonth();

        $moneyIn = CashFlow::where('business_id', $businessId)->where('direction', 'in')->whereMonth('flow_date', now()->month)->sum('amount');
        $moneyOut = CashFlow::where('business_id', $businessId)->where('direction', 'out')->whereMonth('flow_date', now()->month)->sum('amount');
        $currentBalance = ($cashBalance + $bankBalance + $mobileBalance);

        $flows = CashFlow::where('business_id', $businessId)
            ->with(['branch', 'account'])
            ->orderByDesc('flow_date')
            ->orderByDesc('id')
            ->paginate(20);

        $last30Days = collect();
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $in = CashFlow::where('business_id', $businessId)->where('direction', 'in')->whereDate('flow_date', $date)->sum('amount');
            $out = CashFlow::where('business_id', $businessId)->where('direction', 'out')->whereDate('flow_date', $date)->sum('amount');
            $last30Days->push(['date' => $date->format('d/m'), 'in' => (float)$in, 'out' => (float)$out]);
        }

        return view('cash-flow.index', compact('accounts', 'cashBalance', 'bankBalance', 'mobileBalance', 'moneyIn', 'moneyOut', 'currentBalance', 'flows', 'last30Days'));
    }

    public function storeAccount(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:cash,bank,mobile_money',
            'bank_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'opening_balance' => 'nullable|numeric|min:0',
        ]);

        $account = Account::create([
            'business_id' => auth()->user()->business_id,
            'name' => $request->name,
            'type' => $request->type,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'phone_number' => $request->phone_number,
            'opening_balance' => $request->opening_balance ?? 0,
            'current_balance' => $request->opening_balance ?? 0,
            'is_active' => true,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Account added successfully!', 'account' => $account]);
        }
        return redirect()->route('cash-flow.index')->with('success', 'Account added successfully!');
    }

    public function destroyAccount(Account $account)
    {
        if ($account->business_id !== auth()->user()->business_id) abort(403);
        $account->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Account deleted successfully.']);
        }
        return redirect()->route('cash-flow.index')->with('success', 'Account deleted successfully.');
    }
}
