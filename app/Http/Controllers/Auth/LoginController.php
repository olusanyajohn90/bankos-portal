<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('customer')->attempt([
            'email'         => $credentials['email'],
            'password'      => $credentials['password'],
            'portal_active' => true,
        ], $request->boolean('remember'))) {
            $request->session()->regenerate();
            /** @var \App\Models\Customer $customer */
            $customer = Auth::guard('customer')->user();

            // Record successful login
            LoginHistory::create([
                'customer_id' => $customer->id,
                'tenant_id'   => $customer->tenant_id,
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
                'device'      => $this->parseDevice($request->userAgent()),
                'status'      => 'success',
            ]);

            $customer->update(['last_login_at' => now()]);

            // Enforce 2FA if enabled
            if ($customer->two_factor_enabled) {
                $request->session()->put('auth.2fa_customer_id', $customer->id);
                Auth::guard('customer')->logout();
                return redirect()->route('login.2fa');
            }

            // Check if this is a known device
            $fingerprint = hash('sha256', substr($request->userAgent() ?? '', 0, 200) . '.' . substr($request->ip(), 0, strrpos($request->ip(), '.') ?: strlen($request->ip())));
            $known = DB::table('portal_known_devices')
                ->where('customer_id', $customer->id)
                ->where('device_fingerprint', $fingerprint)
                ->first();

            if (!$known) {
                // New device — generate OTP and redirect to verification
                $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                DB::table('portal_login_otps')->insert([
                    'customer_id' => $customer->id,
                    'otp'         => $otp,
                    'expires_at'  => now()->addMinutes(15),
                    'ip_address'  => $request->ip(),
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
                $request->session()->put('auth.device_otp_customer_id', $customer->id);
                Auth::guard('customer')->logout();

                // Try email OTP
                try {
                    Mail::html('Your bankOS verification code is: <strong>' . $otp . '</strong>. Valid for 15 minutes.',
                        fn($m) => $m->to($customer->email)->subject('bankOS Login Verification Code'));
                } catch (\Exception $e) {}

                return redirect()->route('login.device-verify');
            }

            // Update last seen
            DB::table('portal_known_devices')->where('id', $known->id)
                ->update(['last_seen_at' => now(), 'ip_address' => $request->ip()]);

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['email' => 'Invalid credentials or account not activated for portal access.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function parseDevice(?string $ua): string
    {
        if (!$ua) return 'Unknown device';
        if (str_contains($ua, 'Edg'))     $browser = 'Edge';
        elseif (str_contains($ua, 'Chrome'))  $browser = 'Chrome';
        elseif (str_contains($ua, 'Firefox')) $browser = 'Firefox';
        elseif (str_contains($ua, 'Safari'))  $browser = 'Safari';
        else $browser = 'Browser';

        if (str_contains($ua, 'iPhone'))  $os = 'iPhone';
        elseif (str_contains($ua, 'Android')) $os = 'Android';
        elseif (str_contains($ua, 'Windows')) $os = 'Windows';
        elseif (str_contains($ua, 'Mac'))     $os = 'macOS';
        elseif (str_contains($ua, 'Linux'))   $os = 'Linux';
        else $os = 'Unknown OS';

        return "{$browser} on {$os}";
    }
}
