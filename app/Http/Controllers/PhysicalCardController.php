<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PhysicalCardController extends Controller
{
    private function customer()
    {
        return Auth::guard('customer')->user();
    }

    public function index()
    {
        $customer = $this->customer();

        $accounts = $customer->accounts()->get();

        $cards = DB::table('portal_debit_cards')
            ->where('customer_id', $customer->id)
            ->whereIn('status', ['active', 'blocked', 'expired'])
            ->get()
            ->keyBy('account_id');

        $pendingRequests = DB::table('portal_debit_card_requests')
            ->where('customer_id', $customer->id)
            ->whereIn('status', ['pending', 'processing'])
            ->pluck('account_id')
            ->flip();

        $recentRequests = DB::table('portal_debit_card_requests')
            ->where('customer_id', $customer->id)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return view('physical-cards.index', compact('accounts', 'cards', 'pendingRequests', 'recentRequests'));
    }

    public function block(Request $request, string $cardId)
    {
        $request->validate([
            'reason' => 'required|string|max:300',
        ]);

        $card = $this->customerCard($cardId);

        DB::table('portal_debit_cards')
            ->where('id', $card->id)
            ->update([
                'is_blocked'     => true,
                'blocked_at'     => now(),
                'blocked_reason' => $request->reason,
                'status'         => 'blocked',
                'updated_at'     => now(),
            ]);

        return back()->with('success', 'Your debit card has been blocked. Contact the bank to re-enable.');
    }

    public function unblock(string $cardId)
    {
        $card = $this->customerCard($cardId);

        DB::table('portal_debit_cards')
            ->where('id', $card->id)
            ->update([
                'is_blocked'     => false,
                'blocked_at'     => null,
                'blocked_reason' => null,
                'status'         => 'active',
                'updated_at'     => now(),
            ]);

        return back()->with('success', 'Your debit card has been unblocked and is now active.');
    }

    public function requestCard(string $accountId)
    {
        $customer = $this->customer();

        $account = $customer->accounts()->findOrFail($accountId);

        $existingRequests = DB::table('portal_debit_card_requests')
            ->where('customer_id', $customer->id)
            ->where('account_id', $accountId)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('physical-cards.request', compact('account', 'existingRequests'));
    }

    public function storeRequest(Request $request)
    {
        $customer = $this->customer();

        $request->validate([
            'account_id'        => 'required|string',
            'request_type'      => 'required|in:new,replacement,lost_stolen',
            'reason'            => 'nullable|string|max:500',
            'collection_method' => 'required|in:branch_pickup,home_delivery',
            'branch_name'       => 'required_if:collection_method,branch_pickup|nullable|string|max:200',
            'delivery_address'  => 'required_if:collection_method,home_delivery|nullable|string|max:1000',
        ]);

        // Verify account belongs to customer
        $account = $customer->accounts()->findOrFail($request->account_id);

        // Check no active pending/processing request for same account
        $active = DB::table('portal_debit_card_requests')
            ->where('customer_id', $customer->id)
            ->where('account_id', $account->id)
            ->whereIn('status', ['pending', 'processing'])
            ->exists();

        if ($active) {
            return back()->with('error', 'You already have a pending card request for this account. Please wait for it to be processed.');
        }

        DB::table('portal_debit_card_requests')->insert([
            'id'                => (string) Str::uuid(),
            'customer_id'       => $customer->id,
            'tenant_id'         => $customer->tenant_id,
            'account_id'        => $account->id,
            'request_type'      => $request->request_type,
            'reason'            => $request->reason,
            'delivery_address'  => $request->delivery_address,
            'collection_method' => $request->collection_method,
            'branch_name'       => $request->branch_name,
            'status'            => 'pending',
            'reference'         => 'DCR-' . strtoupper(Str::random(8)),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);

        return back()->with('success', 'Card request submitted successfully. You will be notified when your card is ready.');
    }

    public function cancelRequest(string $requestId)
    {
        $customer = $this->customer();

        $affected = DB::table('portal_debit_card_requests')
            ->where('id', $requestId)
            ->where('customer_id', $customer->id)
            ->whereIn('status', ['pending'])
            ->update([
                'status'     => 'cancelled',
                'updated_at' => now(),
            ]);

        if (!$affected) {
            return back()->with('error', 'Request not found or cannot be cancelled.');
        }

        return back()->with('success', 'Card request cancelled.');
    }

    private function customerCard(string $cardId)
    {
        $customer = $this->customer();
        $card = DB::table('portal_debit_cards')
            ->where('id', $cardId)
            ->where('customer_id', $customer->id)
            ->first();

        if (!$card) {
            abort(404);
        }

        return $card;
    }
}
