<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class DeviceVerificationController extends Controller
{
    public function show(Request $request)
    {
        if (!$request->session()->has('auth.device_otp_customer_id')) {
            return redirect()->route('login');
        }

        return view('auth.device-verify');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        $customerId = $request->session()->get('auth.device_otp_customer_id');

        if (!$customerId) {
            return redirect()->route('login')->withErrors(['code' => 'Session expired. Please log in again.']);
        }

        $otpRecord = DB::table('portal_login_otps')
            ->where('customer_id', $customerId)
            ->where('otp', $request->code)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->orderByDesc('created_at')
            ->first();

        if (!$otpRecord) {
            return back()->withErrors(['code' => 'Invalid or expired verification code. Please try again or request a new code.']);
        }

        // Mark OTP as used
        DB::table('portal_login_otps')
            ->where('id', $otpRecord->id)
            ->update(['used_at' => now()]);

        // Register device as known
        $fingerprint = hash('sha256',
            substr($request->userAgent() ?? '', 0, 200) . '.' .
            substr($request->ip(), 0, strrpos($request->ip(), '.') ?: strlen($request->ip()))
        );

        $deviceName = $this->parseDevice($request->userAgent());

        DB::table('portal_known_devices')->upsert([
            [
                'customer_id'        => $customerId,
                'device_fingerprint' => $fingerprint,
                'device_name'        => $deviceName,
                'ip_address'         => $request->ip(),
                'first_seen_at'      => now(),
                'last_seen_at'       => now(),
                'trusted'            => true,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]
        ], ['customer_id', 'device_fingerprint'], ['last_seen_at', 'ip_address', 'updated_at']);

        // Log in the customer
        $request->session()->forget('auth.device_otp_customer_id');
        $request->session()->regenerate();

        Auth::guard('customer')->loginUsingId($customerId);

        return redirect()->intended(route('dashboard'));
    }

    public function resend(Request $request)
    {
        $customerId = $request->session()->get('auth.device_otp_customer_id');

        if (!$customerId) {
            return redirect()->route('login');
        }

        $customer = DB::table('customers')->where('id', $customerId)->first();

        if (!$customer) {
            return redirect()->route('login');
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('portal_login_otps')->insert([
            'customer_id' => $customerId,
            'otp'         => $otp,
            'expires_at'  => now()->addMinutes(15),
            'ip_address'  => $request->ip(),
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        try {
            Mail::html(
                'Your bankOS verification code is: <strong>' . $otp . '</strong>. Valid for 15 minutes.',
                fn($m) => $m->to($customer->email)->subject('bankOS Login Verification Code')
            );
        } catch (\Exception $e) {
            // Silently fail — OTP still saved in DB for manual retrieval if needed
        }

        return back()->with('resent', true);
    }

    private function parseDevice(?string $ua): string
    {
        if (!$ua) return 'Unknown device';

        if (str_contains($ua, 'Edg'))          $browser = 'Edge';
        elseif (str_contains($ua, 'Chrome'))   $browser = 'Chrome';
        elseif (str_contains($ua, 'Firefox'))  $browser = 'Firefox';
        elseif (str_contains($ua, 'Safari'))   $browser = 'Safari';
        else                                    $browser = 'Browser';

        if (str_contains($ua, 'iPhone'))        $os = 'iPhone';
        elseif (str_contains($ua, 'Android'))  $os = 'Android';
        elseif (str_contains($ua, 'Windows'))  $os = 'Windows';
        elseif (str_contains($ua, 'Mac'))      $os = 'macOS';
        elseif (str_contains($ua, 'Linux'))    $os = 'Linux';
        else                                    $os = 'Unknown OS';

        return "{$browser} on {$os}";
    }
}
