<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PortalOverdraftController extends Controller
{
    private function customer()
    {
        return Auth::guard('customer')->user();
    }

    public function index()
    {
        $customer = $this->customer();

        $accounts = $customer->accounts()->get();

        $requests = DB::table('portal_overdraft_requests')
            ->where('customer_id', $customer->id)
            ->orderByDesc('created_at')
            ->get();

        return view('overdraft.index', compact('accounts', 'requests'));
    }

    public function create()
    {
        $customer = $this->customer();

        $accounts = $customer->accounts()->where('status', 'active')->get();

        return view('overdraft.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $customer = $this->customer();

        $request->validate([
            'account_id'      => 'required|string',
            'requested_limit' => 'required|numeric|min:10000',
            'purpose'         => 'required|string|max:500',
            'monthly_income'  => 'nullable|numeric|min:0',
            'employer'        => 'nullable|string|max:200',
        ]);

        // Verify account belongs to customer
        $account = $customer->accounts()->where('status', 'active')->findOrFail($request->account_id);

        // Check no pending/under_review request for same account
        $active = DB::table('portal_overdraft_requests')
            ->where('customer_id', $customer->id)
            ->where('account_id', $account->id)
            ->whereIn('status', ['pending', 'under_review'])
            ->exists();

        if ($active) {
            return back()->with('error', 'You already have a pending overdraft request for this account. Please wait for a decision before applying again.');
        }

        DB::table('portal_overdraft_requests')->insert([
            'id'              => (string) Str::uuid(),
            'customer_id'     => $customer->id,
            'tenant_id'       => $customer->tenant_id,
            'account_id'      => $account->id,
            'requested_limit' => $request->requested_limit,
            'purpose'         => $request->purpose,
            'monthly_income'  => $request->monthly_income ?: null,
            'employer'        => $request->employer ?: null,
            'status'          => 'pending',
            'approved_limit'  => null,
            'review_note'     => null,
            'reference'       => 'OVD-' . strtoupper(Str::random(8)),
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        return back()->with('success', 'Overdraft application submitted. Our credit team will review your request within 2–3 business days.');
    }

    public function cancel(string $requestId)
    {
        $customer = $this->customer();

        $affected = DB::table('portal_overdraft_requests')
            ->where('id', $requestId)
            ->where('customer_id', $customer->id)
            ->whereIn('status', ['pending', 'under_review'])
            ->update([
                'status'     => 'cancelled',
                'updated_at' => now(),
            ]);

        if (!$affected) {
            return back()->with('error', 'Request not found or cannot be cancelled at this stage.');
        }

        return back()->with('success', 'Overdraft application cancelled.');
    }
}
