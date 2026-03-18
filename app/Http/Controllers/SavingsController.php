<?php
namespace App\Http\Controllers;

use App\Models\SavingsPocket;
use App\Models\SavingsPocketTransaction;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SavingsController extends Controller
{
    private function customer() { return Auth::guard('customer')->user(); }

    public function index()
    {
        $customer = $this->customer();
        $accounts = $customer->accounts()->get();
        $pockets  = SavingsPocket::where('customer_id', $customer->id)
            ->whereIn('status', ['active', 'locked'])
            ->orderByDesc('created_at')
            ->get();

        $totalSaved = $pockets->sum('balance');

        return view('savings.index', compact('pockets', 'accounts', 'totalSaved'));
    }

    public function create()
    {
        $customer = $this->customer();
        $accounts = $customer->accounts()->get();
        return view('savings.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $customer = $this->customer();
        $request->validate([
            'account_id'    => 'required|string',
            'name'          => 'required|string|max:100',
            'emoji'         => 'nullable|string|max:10',
            'target_amount' => 'nullable|numeric|min:1',
            'target_date'   => 'nullable|date|after:today',
            'type'          => 'required|in:manual,round_up,scheduled',
        ]);

        $account = $customer->accounts()->findOrFail($request->account_id);

        SavingsPocket::create([
            'id'          => (string) Str::uuid(),
            'tenant_id'   => $customer->tenant_id,
            'account_id'  => $account->id,
            'customer_id' => $customer->id,
            'name'        => $request->name,
            'emoji'       => $request->emoji ?? '💰',
            'target_amount' => $request->target_amount,
            'target_date'   => $request->target_date,
            'type'          => $request->type,
        ]);

        return redirect()->route('savings')->with('success', 'Savings pocket "' . $request->name . '" created!');
    }

    public function show(string $pocketId)
    {
        $customer = $this->customer();
        $pocket   = SavingsPocket::where('customer_id', $customer->id)->findOrFail($pocketId);
        $history  = $pocket->transactions()->orderByDesc('created_at')->limit(30)->get();
        return view('savings.show', compact('pocket', 'history'));
    }

    public function deposit(Request $request, string $pocketId)
    {
        $customer = $this->customer();
        $pocket   = SavingsPocket::where('customer_id', $customer->id)
            ->where('status', 'active')
            ->findOrFail($pocketId);

        $request->validate(['amount' => 'required|numeric|min:1']);

        $account = $customer->accounts()->findOrFail($pocket->account_id);
        $amount  = (float) $request->amount;

        if ((float) $account->available_balance < $amount) {
            return back()->withErrors(['Insufficient balance in linked account.']);
        }

        DB::transaction(function () use ($customer, $account, $pocket, $amount) {
            $account->decrement('available_balance', $amount);
            $account->decrement('ledger_balance', $amount);
            $pocket->increment('balance', $amount);
            $newBalance = $pocket->fresh()->balance;

            SavingsPocketTransaction::create([
                'id'             => (string) Str::uuid(),
                'pocket_id'      => $pocket->id,
                'account_id'     => $account->id,
                'customer_id'    => $customer->id,
                'type'           => 'deposit',
                'amount'         => $amount,
                'balance_after'  => $newBalance,
            ]);

            Transaction::create([
                'id'           => (string) Str::uuid(),
                'tenant_id'    => $customer->tenant_id,
                'account_id'   => $account->id,
                'reference'    => 'SPD-' . strtoupper(Str::random(8)),
                'type'         => 'savings_deposit',
                'amount'       => $amount,
                'currency'     => $account->currency ?? 'NGN',
                'description'  => 'Savings pocket deposit — ' . $pocket->name,
                'status'       => 'success',
                'performed_by' => null, // portal-initiated; customer UUID cannot go into bigint FK
            ]);
        });

        return back()->with('success', 'NGN ' . number_format($amount, 2) . ' deposited into "' . $pocket->name . '".');
    }

    public function withdraw(Request $request, string $pocketId)
    {
        $customer = $this->customer();
        $pocket   = SavingsPocket::where('customer_id', $customer->id)
            ->where('status', 'active')
            ->findOrFail($pocketId);

        $request->validate(['amount' => 'required|numeric|min:1']);

        $amount = (float) $request->amount;

        if ((float) $pocket->balance < $amount) {
            return back()->withErrors(['Insufficient balance in pocket.']);
        }

        DB::transaction(function () use ($customer, $pocket, $amount) {
            $account = $customer->accounts()->findOrFail($pocket->account_id);
            $pocket->decrement('balance', $amount);
            $account->increment('available_balance', $amount);
            $account->increment('ledger_balance', $amount);
            $newBalance = $pocket->fresh()->balance;

            SavingsPocketTransaction::create([
                'id'             => (string) Str::uuid(),
                'pocket_id'      => $pocket->id,
                'account_id'     => $account->id,
                'customer_id'    => $customer->id,
                'type'           => 'withdrawal',
                'amount'         => $amount,
                'balance_after'  => $newBalance,
            ]);

            Transaction::create([
                'id'           => (string) Str::uuid(),
                'tenant_id'    => $customer->tenant_id,
                'account_id'   => $account->id,
                'reference'    => 'SPW-' . strtoupper(Str::random(8)),
                'type'         => 'savings_withdrawal',
                'amount'       => $amount,
                'currency'     => $account->currency ?? 'NGN',
                'description'  => 'Savings pocket withdrawal — ' . $pocket->name,
                'status'       => 'success',
                'performed_by' => null, // portal-initiated; customer UUID cannot go into bigint FK
            ]);
        });

        return back()->with('success', 'NGN ' . number_format($amount, 2) . ' withdrawn from "' . $pocket->name . '".');
    }

    public function destroy(string $pocketId)
    {
        $customer = $this->customer();
        $pocket   = SavingsPocket::where('customer_id', $customer->id)->findOrFail($pocketId);

        if ((float) $pocket->balance > 0) {
            // Return balance to account
            $account = $customer->accounts()->findOrFail($pocket->account_id);
            $account->increment('available_balance', $pocket->balance);
            $account->increment('ledger_balance', $pocket->balance);
        }

        $pocket->update(['status' => 'broken']);

        return redirect()->route('savings')->with('success', 'Pocket closed. Balance returned to your account.');
    }
}
