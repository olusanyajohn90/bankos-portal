<?php
namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LoanController extends Controller
{
    private function customer() { return Auth::guard('customer')->user(); }

    public function index()
    {
        $customer = $this->customer();
        $loans    = Loan::where('customer_id', $customer->id)
            ->where('tenant_id', $customer->tenant_id)
            ->orderByDesc('created_at')
            ->get();

        return view('loans.index', compact('loans'));
    }

    public function show(string $loanId)
    {
        $customer = $this->customer();
        $loan     = Loan::where('customer_id', $customer->id)
            ->where('tenant_id', $customer->tenant_id)
            ->findOrFail($loanId);

        $accounts = $customer->accounts()->get();

        // Repayment history
        $repayments = Transaction::where('account_id', $loan->account_id)
            ->where('type', 'repayment')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return view('loans.show', compact('loan', 'accounts', 'repayments'));
    }

    public function repay(Request $request, string $loanId)
    {
        $customer = $this->customer();
        $loan     = Loan::where('customer_id', $customer->id)
            ->where('tenant_id', $customer->tenant_id)
            ->whereIn('status', ['active', 'overdue'])
            ->findOrFail($loanId);

        $request->validate([
            'account_id' => 'required|string',
            'amount'     => 'required|numeric|min:1',
        ]);

        $account = $customer->accounts()->findOrFail($request->account_id);
        $amount  = min((float) $request->amount, (float) $loan->outstanding_balance);

        if ((float) $account->available_balance < $amount) {
            return back()->withErrors(['Insufficient account balance.']);
        }

        DB::transaction(function () use ($customer, $account, $loan, $amount) {
            $account->decrement('available_balance', $amount);
            $account->decrement('ledger_balance', $amount);
            $loan->decrement('outstanding_balance', $amount);

            $newBalance = (float) $loan->fresh()->outstanding_balance;
            if ($newBalance <= 0) {
                $loan->update(['status' => 'closed', 'outstanding_balance' => 0]);
            }

            $reference = 'RPY-' . strtoupper(Str::random(10));
            Transaction::create([
                'id'           => (string) Str::uuid(),
                'tenant_id'    => $customer->tenant_id,
                'account_id'   => $account->id,
                'reference'    => $reference,
                'type'         => 'repayment',
                'amount'       => $amount,
                'currency'     => $account->currency ?? 'NGN',
                'description'  => 'Loan repayment — Loan #' . $loan->loan_number,
                'status'       => 'success',
                'performed_by' => null, // portal-initiated; customer UUID cannot go into bigint FK
            ]);
        });

        $msg = 'Repayment of NGN ' . number_format($amount, 2) . ' processed.';
        if ((float) $loan->fresh()->outstanding_balance <= 0) {
            $msg .= ' Loan fully repaid!';
        }

        return back()->with('success', $msg);
    }

    public function topupForm(string $loanId)
    {
        $customer = $this->customer();
        $loan     = Loan::where('customer_id', $customer->id)
            ->where('tenant_id', $customer->tenant_id)
            ->where('status', 'active')
            ->findOrFail($loanId);

        $pastRequests = DB::table('portal_loan_topup_requests')
            ->where('loan_id', $loan->id)
            ->orderByDesc('created_at')
            ->get();

        return view('loans.topup', compact('loan', 'pastRequests'));
    }

    public function topupStore(Request $request, string $loanId)
    {
        $customer = $this->customer();
        $loan     = Loan::where('customer_id', $customer->id)
            ->where('tenant_id', $customer->tenant_id)
            ->where('status', 'active')
            ->findOrFail($loanId);

        $request->validate([
            'amount'  => 'required|numeric|min:1000',
            'purpose' => 'nullable|string|max:500',
        ]);

        // Check no pending top-up request exists for this loan
        $pending = DB::table('portal_loan_topup_requests')
            ->where('loan_id', $loan->id)
            ->where('status', 'pending')
            ->exists();

        if ($pending) {
            return back()->withErrors(['amount' => 'You already have a pending top-up request for this loan. Please wait for it to be reviewed.']);
        }

        DB::table('portal_loan_topup_requests')->insert([
            'id'               => (string) Str::uuid(),
            'customer_id'      => $customer->id,
            'tenant_id'        => $customer->tenant_id,
            'loan_id'          => $loan->id,
            'requested_amount' => $request->amount,
            'purpose'          => $request->purpose,
            'status'           => 'pending',
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        return back()->with('success', 'Top-up request submitted. We will review and respond within 2 business days.');
    }
}
