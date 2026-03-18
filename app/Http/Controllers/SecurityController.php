<?php
namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\LoginHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SecurityController extends Controller
{
    private function customer() { return Auth::guard('customer')->user(); }

    public function index()
    {
        $customer = $this->customer();
        $loginHistory = LoginHistory::where('customer_id', $customer->id)
            ->orderByDesc('created_at')->limit(20)->get();
        $accounts = Account::where('customer_id', $customer->id)
            ->where('status', 'active')->get();
        return view('security.index', compact('customer', 'loginHistory', 'accounts'));
    }

    public function logoutAll(Request $request)
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Logged out from all sessions.');
    }

    public function updatePin(Request $request)
    {
        $customer = $this->customer();
        $request->validate([
            'pin'              => 'required|string|size:4|regex:/^\d{4}$/|confirmed',
            'pin_confirmation' => 'required',
        ]);

        $wasSet = (bool) $customer->portal_pin;
        $customer->update(['portal_pin' => Hash::make($request->pin)]);
        return back()->with('pin_success', 'Transaction PIN ' . ($wasSet ? 'updated' : 'set') . ' successfully.');
    }

    public function enable2fa(Request $request)
    {
        $customer = $this->customer();
        // Generate a TOTP secret (base32 encoded, 16 chars)
        $secret = strtoupper(Str::random(16));
        $customer->update(['two_factor_secret' => $secret, 'two_factor_method' => $request->method ?? 'totp']);

        return response()->json([
            'secret'  => $secret,
            'otpauth' => 'otpauth://totp/' . urlencode($customer->email ?? $customer->phone) . '?secret=' . $secret . '&issuer=bankOS',
        ]);
    }

    public function confirm2fa(Request $request)
    {
        $customer = $this->customer();

        if (!preg_match('/^\d{6}$/', (string) $request->code)) {
            return response()->json(['success' => false, 'message' => 'Please enter a valid 6-digit code.']);
        }

        // Save secret from setup step if provided
        if ($request->filled('secret')) {
            $customer->update(['two_factor_secret' => $request->secret]);
        }

        $customer->update(['two_factor_enabled' => true, 'two_factor_confirmed_at' => now()]);
        return response()->json(['success' => true]);
    }

    public function disable2fa(Request $request)
    {
        $customer = $this->customer();
        $request->validate(['password' => 'required|string']);

        if (!$customer->portal_password || !password_verify($request->password, $customer->portal_password)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        $customer->update(['two_factor_enabled' => false, 'two_factor_secret' => null, 'two_factor_confirmed_at' => null]);
        return back()->with('success', '2FA disabled.');
    }
}
