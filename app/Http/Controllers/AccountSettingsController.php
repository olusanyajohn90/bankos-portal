<?php
namespace App\Http\Controllers;

use App\Models\CustomerAccountSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AccountSettingsController extends Controller
{
    private function customer() { return Auth::guard('customer')->user(); }

    public function update(Request $request, string $accountId)
    {
        $customer = $this->customer();
        $account  = $customer->accounts()->findOrFail($accountId);

        $request->validate(['nickname' => 'nullable|string|max:60', 'color_hex' => 'nullable|string|size:7']);

        $setting = CustomerAccountSetting::forAccount($customer->id, $account->id);
        if (!$setting->exists) $setting->id = (string) Str::uuid();
        $setting->fill(['nickname' => $request->nickname, 'color_hex' => $request->color_hex ?? '#2563eb']);
        $setting->save();

        return back()->with('success', 'Account display updated.');
    }

    public function freeze(Request $request, string $accountId)
    {
        $customer = $this->customer();
        $account  = $customer->accounts()->findOrFail($accountId);

        $setting = CustomerAccountSetting::forAccount($customer->id, $account->id);
        if (!$setting->exists) $setting->id = (string) Str::uuid();
        $setting->fill(['is_frozen' => true, 'frozen_at' => now(), 'frozen_reason' => $request->reason ?? 'Customer initiated freeze']);
        $setting->save();

        return back()->with('success', 'Account frozen. All outgoing transactions are blocked until you unfreeze.');
    }

    public function unfreeze(string $accountId)
    {
        $customer = $this->customer();
        $account  = $customer->accounts()->findOrFail($accountId);

        CustomerAccountSetting::where('customer_id', $customer->id)
            ->where('account_id', $account->id)
            ->update(['is_frozen' => false, 'frozen_at' => null, 'frozen_reason' => null]);

        return back()->with('success', 'Account unfrozen. Transactions are now enabled.');
    }
}
