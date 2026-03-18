<?php
namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SplitBillController extends Controller
{
    private function customer() { return Auth::guard('customer')->user(); }

    public function index()
    {
        $customer = $this->customer();
        $bills = DB::table('portal_split_bills')
            ->where('customer_id', $customer->id)
            ->where('tenant_id', $customer->tenant_id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('split-bills.index', compact('bills'));
    }

    public function create()
    {
        $customer = $this->customer();
        $accounts = $customer->accounts()->where('status', 'active')->get();
        return view('split-bills.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $customer = $this->customer();

        $validated = $request->validate([
            'account_id'        => 'required|string',
            'title'             => 'required|string|max:200',
            'description'       => 'nullable|string|max:500',
            'total_amount'      => 'required|numeric|min:100',
            'participant_count' => 'required|integer|min:2|max:50',
            'expires_in_days'   => 'nullable|integer|min:1|max:30',
            'participants'      => 'required|array|min:1',
            'participants.*.name'  => 'required|string|max:150',
            'participants.*.phone' => 'nullable|string|max:30',
            'participants.*.email' => 'nullable|email|max:150',
        ]);

        $account = $customer->accounts()->findOrFail($validated['account_id']);
        $total   = (float) $validated['total_amount'];
        $count   = (int) $validated['participant_count'];
        $perPerson = round($total / $count, 2);

        $billId    = (string) Str::uuid();
        $reference = 'SPL' . strtoupper(Str::random(10));
        $expiresAt = isset($validated['expires_in_days'])
            ? now()->addDays($validated['expires_in_days'])
            : now()->addDays(7);

        DB::transaction(function () use (
            $customer, $account, $validated, $billId, $reference,
            $total, $count, $perPerson, $expiresAt
        ) {
            DB::table('portal_split_bills')->insert([
                'id'                => $billId,
                'customer_id'       => $customer->id,
                'tenant_id'         => $customer->tenant_id,
                'account_id'        => $account->id,
                'reference'         => $reference,
                'title'             => $validated['title'],
                'description'       => $validated['description'] ?? null,
                'total_amount'      => $total,
                'per_person_amount' => $perPerson,
                'participant_count' => $count,
                'collected_amount'  => 0,
                'paid_count'        => 0,
                'status'            => 'open',
                'expires_at'        => $expiresAt,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            foreach ($validated['participants'] as $p) {
                DB::table('portal_split_bill_participants')->insert([
                    'split_bill_id' => $billId,
                    'name'          => trim($p['name']),
                    'phone'         => trim($p['phone'] ?? ''),
                    'email'         => trim($p['email'] ?? ''),
                    'amount'        => $perPerson,
                    'status'        => 'pending',
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }
        });

        return redirect()->route('split-bills.show', $billId)
            ->with('success', 'Split bill created. Reference: ' . $reference);
    }

    public function show(string $id)
    {
        $customer = $this->customer();
        $bill = DB::table('portal_split_bills')
            ->where('id', $id)
            ->where('customer_id', $customer->id)
            ->first();

        if (!$bill) abort(404);

        $participants = DB::table('portal_split_bill_participants')
            ->where('split_bill_id', $id)
            ->orderBy('id')
            ->get();

        $account = $customer->accounts()->find($bill->account_id);

        return view('split-bills.show', compact('bill', 'participants', 'account'));
    }

    public function markPaid(Request $request, string $billId, int $participantId)
    {
        $customer = $this->customer();

        $bill = DB::table('portal_split_bills')
            ->where('id', $billId)
            ->where('customer_id', $customer->id)
            ->where('status', 'open')
            ->first();

        if (!$bill) abort(404);

        $participant = DB::table('portal_split_bill_participants')
            ->where('id', $participantId)
            ->where('split_bill_id', $billId)
            ->where('status', 'pending')
            ->first();

        if (!$participant) {
            return back()->withErrors(['That participant entry was not found or already marked.']);
        }

        $ref = 'SPLP' . strtoupper(Str::random(8));

        DB::transaction(function () use ($bill, $participant, $ref, $billId, $customer) {
            DB::table('portal_split_bill_participants')
                ->where('id', $participant->id)
                ->update(['status' => 'paid', 'paid_at' => now(), 'reference' => $ref, 'updated_at' => now()]);

            DB::table('portal_split_bills')
                ->where('id', $billId)
                ->increment('collected_amount', $participant->amount);
            DB::table('portal_split_bills')
                ->where('id', $billId)
                ->increment('paid_count', 1, ['updated_at' => now()]);

            // Credit the receiving account
            $account = $customer->accounts()->find($bill->account_id);
            if ($account) {
                $account->increment('available_balance', $participant->amount);
                $account->increment('ledger_balance', $participant->amount);

                Transaction::create([
                    'id'          => (string) \Illuminate\Support\Str::uuid(),
                    'tenant_id'   => $customer->tenant_id,
                    'account_id'  => $account->id,
                    'reference'   => $ref,
                    'type'        => 'deposit',
                    'amount'      => $participant->amount,
                    'currency'    => $account->currency ?? 'NGN',
                    'description' => 'Split bill payment — ' . $participant->name . ' (' . $bill->title . ')',
                    'status'      => 'success',
                    'performed_by'=> $customer->id,
                ]);
            }

            // Auto-close if all paid
            $remaining = DB::table('portal_split_bill_participants')
                ->where('split_bill_id', $billId)
                ->where('status', 'pending')
                ->count();

            if ($remaining === 0) {
                DB::table('portal_split_bills')
                    ->where('id', $billId)
                    ->update(['status' => 'completed', 'updated_at' => now()]);
            }
        });

        return back()->with('success', $participant->name . '\'s share of NGN ' . number_format($participant->amount, 2) . ' marked as paid.');
    }

    public function cancel(string $id)
    {
        $customer = $this->customer();
        $updated = DB::table('portal_split_bills')
            ->where('id', $id)
            ->where('customer_id', $customer->id)
            ->whereIn('status', ['open'])
            ->update(['status' => 'cancelled', 'updated_at' => now()]);

        if (!$updated) return back()->withErrors(['Could not cancel this split bill.']);
        return redirect()->route('split-bills')->with('success', 'Split bill cancelled.');
    }
}
