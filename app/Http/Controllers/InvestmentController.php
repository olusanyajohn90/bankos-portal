<?php
namespace App\Http\Controllers;

use App\Models\PortalInvestment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvestmentController extends Controller
{
    private function customer() { return Auth::guard('customer')->user(); }

    public function index()
    {
        $customer = $this->customer();
        $investments = PortalInvestment::where('customer_id', $customer->id)
            ->orderByDesc('created_at')->get();

        // Auto-mature any that have passed their maturity date
        foreach ($investments->where('status', 'active') as $inv) {
            if ($inv->maturity_date->isPast()) {
                DB::transaction(function () use ($inv, $customer) {
                    $account = $customer->accounts()->find($inv->account_id);
                    if ($account) {
                        $account->increment('available_balance', $inv->maturity_amount);
                        $account->increment('ledger_balance', $inv->maturity_amount);
                        Transaction::create([
                            'id' => (string) Str::uuid(), 'tenant_id' => $customer->tenant_id,
                            'account_id' => $account->id, 'reference' => 'INV-MAT-' . strtoupper(Str::random(8)),
                            'type' => 'deposit', 'amount' => $inv->maturity_amount, 'currency' => $account->currency ?? 'NGN',
                            'description' => 'Investment matured — ' . $inv->name, 'status' => 'success', 'performed_by' => null, // portal-initiated; customer UUID cannot go into bigint FK
                        ]);
                    }
                    $inv->update(['status' => 'matured', 'matured_at' => now()]);
                });
            }
        }

        $investments = PortalInvestment::where('customer_id', $customer->id)->orderByDesc('created_at')->get();
        $totalInvested = $investments->where('status', 'active')->sum('principal');
        $totalEarned   = $investments->where('status', 'matured')->sum('expected_interest');

        return view('investments.index', compact('investments', 'totalInvested', 'totalEarned'));
    }

    public function create()
    {
        $customer = $this->customer();
        $accounts = $customer->accounts()->where('status', 'active')->get();
        $durations = PortalInvestment::$durations;
        return view('investments.create', compact('accounts', 'durations'));
    }

    public function store(Request $request)
    {
        $customer = $this->customer();
        $request->validate([
            'account_id'    => 'required|string',
            'name'          => 'required|string|max:100',
            'principal'     => 'required|numeric|min:5000',
            'duration_days' => 'required|integer',
        ]);

        $account = $customer->accounts()->findOrFail($request->account_id);
        $duration = PortalInvestment::$durations[$request->duration_days] ?? null;
        if (!$duration) return back()->withErrors(['Invalid duration.']);

        $principal = (float) $request->principal;
        if ((float)$account->available_balance < $principal) {
            return back()->withErrors(['Insufficient account balance.']);
        }

        $rate = $duration['rate'];
        $interest = round($principal * ($rate / 100) * ($request->duration_days / 365), 2);
        $maturityAmount = $principal + $interest;
        $startDate = now()->toDateString();
        $maturityDate = now()->addDays($request->duration_days)->toDateString();

        DB::transaction(function () use ($customer, $account, $principal, $rate, $interest, $maturityAmount, $startDate, $maturityDate, $request) {
            $account->decrement('available_balance', $principal);
            $account->decrement('ledger_balance', $principal);

            Transaction::create([
                'id' => (string) Str::uuid(), 'tenant_id' => $customer->tenant_id,
                'account_id' => $account->id, 'reference' => 'INV-' . strtoupper(Str::random(8)),
                'type' => 'withdrawal', 'amount' => $principal, 'currency' => $account->currency ?? 'NGN',
                'description' => 'Investment created — ' . $request->name, 'status' => 'success', 'performed_by' => null, // portal-initiated; customer UUID cannot go into bigint FK
            ]);

            PortalInvestment::create([
                'id' => (string) Str::uuid(), 'customer_id' => $customer->id, 'tenant_id' => $customer->tenant_id,
                'account_id' => $account->id, 'reference' => 'INV-' . strtoupper(Str::random(10)),
                'name' => $request->name, 'principal' => $principal, 'interest_rate' => $rate,
                'duration_days' => $request->duration_days, 'expected_interest' => $interest,
                'maturity_amount' => $maturityAmount, 'start_date' => $startDate, 'maturity_date' => $maturityDate, 'status' => 'active',
            ]);
        });

        return redirect()->route('investments')->with('success', "Investment of NGN " . number_format($principal, 2) . " created at {$rate}% p.a. Matures on " . date('d M Y', strtotime($maturityDate)) . ".");
    }

    public function show(string $id)
    {
        $customer = $this->customer();
        $investment = PortalInvestment::where('customer_id', $customer->id)->findOrFail($id);
        return view('investments.show', compact('investment'));
    }

    public function liquidate(Request $request, string $id)
    {
        $customer = $this->customer();
        $inv = PortalInvestment::where('customer_id', $customer->id)->where('status', 'active')->findOrFail($id);

        // Early liquidation: return principal only, 10% penalty on accrued interest
        $accrued = $inv->accrued_interest;
        $penalty = round($accrued * 0.1, 2);
        $payout  = (float)$inv->principal + $accrued - $penalty;

        DB::transaction(function () use ($customer, $inv, $payout, $penalty) {
            $account = $customer->accounts()->find($inv->account_id);
            if ($account) {
                $account->increment('available_balance', $payout);
                $account->increment('ledger_balance', $payout);
                Transaction::create([
                    'id' => (string) Str::uuid(), 'tenant_id' => $customer->tenant_id,
                    'account_id' => $account->id, 'reference' => 'INV-LIQ-' . strtoupper(Str::random(8)),
                    'type' => 'deposit', 'amount' => $payout, 'currency' => $account->currency ?? 'NGN',
                    'description' => 'Early investment liquidation — ' . $inv->name . ($penalty > 0 ? " (NGN {$penalty} penalty)" : ''),
                    'status' => 'success', 'performed_by' => null, // portal-initiated; customer UUID cannot go into bigint FK
                ]);
            }
            $inv->update(['status' => 'broken', 'broken_at' => now(), 'penalty_amount' => $penalty]);
        });

        return redirect()->route('investments')->with('success', "Investment liquidated. NGN " . number_format($payout, 2) . " returned" . ($penalty > 0 ? " (NGN " . number_format($penalty, 2) . " early withdrawal penalty applied)." : "."));
    }
}
