<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\CashFlow;
use Illuminate\Http\Request;

class CashFlowController extends Controller
{
    /**
     * List accounts and cash-flow summary/entries for the authenticated user's business.
     */
    public function index(Request $request)
    {
        $businessId = $request->user()->business_id;

        $accounts = Account::where('business_id', $businessId)->where('is_active', true)->get();
        $cashAccount = $accounts->where('type', 'cash')->first();
        $bankAccounts = $accounts->where('type', 'bank');
        $mobileAccounts = $accounts->where('type', 'mobile_money');

        $cashBalance = $cashAccount?->current_balance ?? 0;
        $bankBalance = $bankAccounts->sum('current_balance');
        $mobileBalance = $mobileAccounts->sum('current_balance');

        $moneyIn = CashFlow::where('business_id', $businessId)->where('direction', 'in')->whereMonth('flow_date', now()->month)->sum('amount');
        $moneyOut = CashFlow::where('business_id', $businessId)->where('direction', 'out')->whereMonth('flow_date', now()->month)->sum('amount');
        $currentBalance = $cashBalance + $bankBalance + $mobileBalance;

        $flows = CashFlow::where('business_id', $businessId)
            ->with(['branch', 'account'])
            ->orderByDesc('flow_date')
            ->orderByDesc('id')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'accounts' => $accounts,
            'summary' => [
                'cashBalance' => $cashBalance,
                'bankBalance' => $bankBalance,
                'mobileBalance' => $mobileBalance,
                'moneyIn' => $moneyIn,
                'moneyOut' => $moneyOut,
                'currentBalance' => $currentBalance,
            ],
            'flows' => $flows,
        ]);
    }

    /**
     * Create a new account (cash / bank / mobile_money).
     */
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
            'business_id' => $request->user()->business_id,
            'name' => $request->name,
            'type' => $request->type,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'phone_number' => $request->phone_number,
            'opening_balance' => $request->opening_balance ?? 0,
            'current_balance' => $request->opening_balance ?? 0,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Account added successfully!',
            'account' => $account,
        ], 201);
    }

    public function destroyAccount(Request $request, Account $account)
    {
        if ($account->business_id !== $request->user()->business_id) abort(403);
        $account->delete();

        return response()->json([
            'success' => true,
            'message' => 'Account deleted successfully.',
        ]);
    }
}
