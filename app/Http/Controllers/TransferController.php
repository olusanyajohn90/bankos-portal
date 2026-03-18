<?php
namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use App\Notifications\LowBalanceAlert;
use App\Notifications\TransactionAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class TransferController extends Controller
{
    public function create()
    {
        /** @var \App\Models\Customer $customer */
        $customer = Auth::guard('customer')->user();
        $accounts = $customer->accounts()
            ->where('status', 'active')
            ->get(['id', 'account_number', 'account_name', 'available_balance', 'currency']);

        return view('transfer.index', compact('accounts'));
    }

    /**
     * AJAX: resolve account number → account name (same tenant only).
     */
    public function lookup(Request $request)
    {
        /** @var \App\Models\Customer $customer */
        $customer = Auth::guard('customer')->user();
        $account  = Account::where('account_number', $request->account_number)
            ->where('tenant_id', $customer->tenant_id)
            ->where('status', 'active')
            ->first(['account_name', 'account_number']);

        if (!$account) {
            return response()->json(['success' => false, 'message' => 'Account not found.'], 404);
        }

        return response()->json(['success' => true, 'account_name' => $account->account_name]);
    }

    public function store(Request $request)
    {
        /** @var \App\Models\Customer $customer */
        $customer = Auth::guard('customer')->user();

        $validated = $request->validate([
            'from_account_id'   => 'required|string',
            'to_account_number' => 'required|string',
            'amount'            => 'required|numeric|min:1',
            'description'       => 'nullable|string|max:100',
            'pin'               => 'required|digits:4',
        ]);

        // Verify transaction PIN before moving any funds
        if (!$customer->portal_pin || !Hash::check($validated['pin'], $customer->portal_pin)) {
            return back()->withErrors(['pin' => 'Incorrect transaction PIN. Please try again.'])->withInput();
        }

        $fromAccount = $customer->accounts()
            ->where('status', 'active')
            ->findOrFail($validated['from_account_id']);

        $toAccount = Account::where('account_number', $validated['to_account_number'])
            ->where('tenant_id', $customer->tenant_id)
            ->where('status', 'active')
            ->first();

        if (!$toAccount) {
            return back()->withErrors(['to_account_number' => 'Account not found in this bank.'])->withInput();
        }

        if ($fromAccount->id === $toAccount->id) {
            return back()->withErrors(['to_account_number' => 'Cannot transfer to the same account.'])->withInput();
        }

        $amount = (float) $validated['amount'];

        if ((float) $fromAccount->available_balance < $amount) {
            return back()->withErrors(['amount' => 'Insufficient available balance.'])->withInput();
        }

        $narration = $validated['description'] ?: 'Funds transfer';
        $reference = 'TRF' . strtoupper(Str::random(10));

        DB::transaction(function () use ($fromAccount, $toAccount, $amount, $narration, $reference, $customer) {
            $fromAccount->decrement('available_balance', $amount);
            $fromAccount->decrement('ledger_balance', $amount);

            Transaction::create([
                'id'           => (string) Str::uuid(),
                'tenant_id'    => $customer->tenant_id,
                'account_id'   => $fromAccount->id,
                'reference'    => $reference . '-DR',
                'type'         => 'transfer',
                'amount'       => $amount,
                'currency'     => $fromAccount->currency ?? 'NGN',
                'description'  => $narration . ' — to ' . $toAccount->account_number . ' (' . $toAccount->account_name . ')',
                'status'       => 'success',
                'performed_by' => null, // portal-initiated; customer UUID cannot go into bigint FK
            ]);

            $toAccount->increment('available_balance', $amount);
            $toAccount->increment('ledger_balance', $amount);

            Transaction::create([
                'id'           => (string) Str::uuid(),
                'tenant_id'    => $customer->tenant_id,
                'account_id'   => $toAccount->id,
                'reference'    => $reference . '-CR',
                'type'         => 'transfer',
                'amount'       => $amount,
                'currency'     => $toAccount->currency ?? 'NGN',
                'description'  => $narration . ' — from ' . $fromAccount->account_number . ' (' . $fromAccount->account_name . ')',
                'status'       => 'success',
                'performed_by' => null, // portal-initiated; customer UUID cannot go into bigint FK
            ]);
        });

        // Reload balances after transaction so notifications show current balance
        $fromAccount->refresh();
        $toAccount->refresh();

        // Send transaction alert to sender (debit)
        if ($customer->email) {
            try {
                $debitTxn = Transaction::where('reference', $reference . '-DR')->first();
                if ($debitTxn) {
                    Notification::route('mail', $customer->email)
                        ->notify(new TransactionAlert($debitTxn, $fromAccount, $customer->tenant));
                }
            } catch (\Throwable $e) {
                // Notification failure must never break the transfer
                \Illuminate\Support\Facades\Log::warning('TransactionAlert (debit) failed: ' . $e->getMessage());
            }

            // Low balance check — use customer preference threshold if available, else 1000
            try {
                $pref      = DB::table('notification_preferences')->where('customer_id', $customer->id)->first();
                $threshold = $pref ? (float) ($pref->low_balance_threshold ?? 1000) : 1000.0;
                $lowAlerts = $pref ? (bool) $pref->low_balance_alert : true;

                if ($lowAlerts && (float) $fromAccount->available_balance < $threshold) {
                    Notification::route('mail', $customer->email)
                        ->notify(new LowBalanceAlert($fromAccount, $customer->tenant, $threshold));
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('LowBalanceAlert failed: ' . $e->getMessage());
            }
        }

        $currency = $fromAccount->currency ?? 'NGN';
        return redirect()->route('dashboard')
            ->with('success', "Transfer of {$currency} " . number_format($amount, 2) . " to {$toAccount->account_name} was successful. Ref: {$reference}");
    }
}
