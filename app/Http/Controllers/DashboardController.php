<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Loan;
use App\Models\SavingsPocket;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();
        $accounts = $customer->accounts()
            ->where('status', 'active')
            ->get(['id', 'account_number', 'account_name', 'available_balance', 'ledger_balance', 'currency', 'type']);

        $totalBalance = $accounts->sum('available_balance');

        // Active loans summary
        $activeLoans = Loan::where('customer_id', $customer->id)
            ->where('tenant_id', $customer->tenant_id)
            ->whereIn('status', ['active', 'overdue'])
            ->get(['id', 'loan_number', 'outstanding_balance', 'principal_amount', 'status', 'expected_maturity_date']);

        // Savings summary
        $savingsTotal = SavingsPocket::where('customer_id', $customer->id)
            ->where('status', 'active')
            ->sum('balance');
        $savingsCount = SavingsPocket::where('customer_id', $customer->id)
            ->where('status', 'active')
            ->count();

        // Recent transactions across all accounts (last 5)
        $accountIds = $accounts->pluck('id');
        $recentTxns = Transaction::whereIn('account_id', $accountIds)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'customer', 'accounts', 'totalBalance',
            'activeLoans', 'savingsTotal', 'savingsCount', 'recentTxns'
        ));
    }

    public function transactions(Request $request, string $accountId)
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();
        $account  = $customer->accounts()->where('id', $accountId)->firstOrFail();

        $transactions = $account->transactions()
            ->when($request->from, fn($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->to,   fn($q) => $q->whereDate('created_at', '<=', $request->to))
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->when($request->q,    fn($q) => $q->where(fn($s) =>
                $s->where('description', 'like', '%' . $request->q . '%')
                  ->orWhere('reference',  'like', '%' . $request->q . '%')
            ))
            ->paginate(20)
            ->withQueryString();

        return view('transactions', compact('account', 'transactions'));
    }
}
