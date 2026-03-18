<?php
namespace App\Http\Controllers;

use App\Models\VirtualCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class VirtualCardController extends Controller
{
    private function customer() { return Auth::guard('customer')->user(); }

    public function index()
    {
        $customer = $this->customer();
        $accounts = $customer->accounts()->get();
        $cards    = VirtualCard::where('customer_id', $customer->id)
            ->whereIn('status', ['active', 'frozen'])
            ->orderByDesc('created_at')
            ->get();

        return view('cards.index', compact('cards', 'accounts'));
    }

    public function store(Request $request)
    {
        $customer = $this->customer();
        $request->validate(['account_id' => 'required|string']);

        $account = $customer->accounts()->findOrFail($request->account_id);

        // Limit: one active/frozen card per account
        $existing = VirtualCard::where('account_id', $account->id)
            ->whereIn('status', ['active', 'frozen'])
            ->count();

        if ($existing >= 3) {
            return back()->withErrors(['Maximum 3 virtual cards per account.']);
        }

        $last4  = str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
        $expiry = now()->addYears(3);
        $types  = ['visa', 'mastercard'];

        VirtualCard::create([
            'id'                  => (string) Str::uuid(),
            'tenant_id'           => $customer->tenant_id,
            'account_id'          => $account->id,
            'customer_id'         => $customer->id,
            'card_last4'          => $last4,
            'card_number_masked'  => '**** **** **** ' . $last4,
            'expiry_month'        => $expiry->format('m'),
            'expiry_year'         => $expiry->format('Y'),
            'card_name'           => strtoupper($account->account_name),
            'card_type'           => $types[array_rand($types)],
            'status'              => 'active',
        ]);

        return back()->with('success', 'Virtual card created successfully.');
    }

    public function freeze(string $cardId)
    {
        $this->customerCard($cardId)->update(['status' => 'frozen']);
        return back()->with('success', 'Card frozen. No new transactions will be processed.');
    }

    public function unfreeze(string $cardId)
    {
        $this->customerCard($cardId)->update(['status' => 'active']);
        return back()->with('success', 'Card unfrozen and active.');
    }

    public function setLimit(Request $request, string $cardId)
    {
        $request->validate(['limit' => 'required|numeric|min:0']);
        $this->customerCard($cardId)->update(['spending_limit' => $request->limit ?: null]);
        return back()->with('success', 'Spending limit updated.');
    }

    public function destroy(string $cardId)
    {
        $this->customerCard($cardId)->update(['status' => 'cancelled']);
        return back()->with('success', 'Card cancelled.');
    }

    public function setPin(Request $request, string $cardId)
    {
        $request->validate([
            'pin'              => 'required|digits:4|confirmed',
            'pin_confirmation' => 'required',
            'portal_pin'       => 'required',
        ]);

        $customer = $this->customer();

        if (!$customer->portal_pin || !Hash::check($request->portal_pin, $customer->portal_pin)) {
            return back()->withErrors(['portal_pin' => 'Incorrect portal PIN. Cannot authorise card PIN change.']);
        }

        $this->customerCard($cardId)->update([
            'card_pin'        => Hash::make($request->pin),
            'pin_set_at'      => now(),
            'pin_attempts'    => 0,
            'pin_locked_until'=> null,
        ]);

        return back()->with('pin_success', 'Card PIN set successfully.');
    }

    public function changePin(Request $request, string $cardId)
    {
        $request->validate([
            'current_pin'      => 'required|digits:4',
            'new_pin'          => 'required|digits:4|confirmed',
            'new_pin_confirmation' => 'required',
        ]);

        $card = $this->customerCard($cardId);

        // Check if pin is locked
        if ($card->pin_locked_until && now()->lt($card->pin_locked_until)) {
            return back()->withErrors(['current_pin' => 'Card PIN is locked until ' . \Carbon\Carbon::parse($card->pin_locked_until)->format('d M Y, H:i') . '.']);
        }

        // Verify current pin
        if (!$card->card_pin || !Hash::check($request->current_pin, $card->card_pin)) {
            $attempts = $card->pin_attempts + 1;
            if ($attempts >= 3) {
                $card->update([
                    'pin_attempts'    => $attempts,
                    'pin_locked_until'=> now()->addHours(24),
                ]);
                return back()->withErrors(['current_pin' => 'Incorrect PIN. Card PIN has been locked for 24 hours due to too many failed attempts.']);
            }
            $card->update(['pin_attempts' => $attempts]);
            return back()->withErrors(['current_pin' => 'Incorrect current PIN. ' . (3 - $attempts) . ' attempt(s) remaining.']);
        }

        $card->update([
            'card_pin'         => Hash::make($request->new_pin),
            'pin_set_at'       => now(),
            'pin_attempts'     => 0,
            'pin_locked_until' => null,
        ]);

        return back()->with('pin_success', 'Card PIN changed.');
    }

    private function customerCard(string $cardId): VirtualCard
    {
        $customer = $this->customer();
        return VirtualCard::where('customer_id', $customer->id)->findOrFail($cardId);
    }
}
