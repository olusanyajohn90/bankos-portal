<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorController extends Controller
{
    public function show(Request $request)
    {
        if (!$request->session()->has('auth.2fa_customer_id')) {
            return redirect()->route('login');
        }
        return view('auth.two-factor');
    }

    public function verify(Request $request)
    {
        $customerId = $request->session()->get('auth.2fa_customer_id');
        if (!$customerId) {
            return redirect()->route('login');
        }

        $request->validate(['code' => 'required|digits:6']);

        $customer = Customer::find($customerId);
        if (!$customer || !$customer->two_factor_enabled) {
            $request->session()->forget('auth.2fa_customer_id');
            return redirect()->route('login');
        }

        // RFC 6238 TOTP — pure PHP, no external library required
        if (!$customer->two_factor_secret || !$this->verifyTotp($customer->two_factor_secret, $request->code)) {
            return back()->withErrors(['code' => 'Invalid or expired code. Please try again.']);
        }

        $request->session()->forget('auth.2fa_customer_id');
        Auth::guard('customer')->loginUsingId($customerId);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    /**
     * RFC 6238 TOTP verification — pure PHP, no external library needed.
     * Checks current window ±1 (30-second steps) to tolerate clock drift.
     */
    private function verifyTotp(string $secret, string $code): bool
    {
        // Decode base32 secret
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret   = strtoupper(preg_replace('/\s+/', '', $secret));
        $binary   = '';
        $buffer   = 0;
        $bits     = 0;

        for ($i = 0; $i < strlen($secret); $i++) {
            $val = strpos($alphabet, $secret[$i]);
            if ($val === false) continue;
            $buffer = ($buffer << 5) | $val;
            $bits  += 5;
            if ($bits >= 8) {
                $binary .= chr(($buffer >> ($bits - 8)) & 0xFF);
                $bits   -= 8;
            }
        }

        // Check current window and ±1 step for clock skew tolerance
        $step = (int) floor(time() / 30);
        for ($offset = -1; $offset <= 1; $offset++) {
            $t    = $step + $offset;
            $msg  = pack('N*', 0) . pack('N*', $t);
            $hash = hash_hmac('sha1', $msg, $binary, true);
            $idx  = ord($hash[19]) & 0x0F;
            $otp  = (
                ((ord($hash[$idx])     & 0x7F) << 24) |
                ((ord($hash[$idx + 1]) & 0xFF) << 16) |
                ((ord($hash[$idx + 2]) & 0xFF) <<  8) |
                 (ord($hash[$idx + 3]) & 0xFF)
            ) % 1_000_000;

            if ($code === str_pad((string) $otp, 6, '0', STR_PAD_LEFT)) {
                return true;
            }
        }

        return false;
    }
}
