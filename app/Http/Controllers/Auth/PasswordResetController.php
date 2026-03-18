<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function showForgot()
    {
        return view('auth.forgot-password');
    }

    public function sendReset(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $customer = Customer::where('email', $request->email)
            ->where('portal_active', true)
            ->first();

        if ($customer) {
            $token = Str::random(64);

            DB::table('password_reset_tokens')->upsert(
                ['email' => $customer->email, 'token' => Hash::make($token), 'created_at' => now()],
                ['email'],
                ['token', 'created_at']
            );

            $resetUrl = url('/reset-password?token=' . $token . '&email=' . urlencode($customer->email));

            try {
                Mail::html(
                    '<p>Hello ' . e($customer->first_name) . ',</p>'
                    . '<p>Click the link below to reset your bankOS portal password. This link expires in 60 minutes.</p>'
                    . '<p><a href="' . $resetUrl . '">' . $resetUrl . '</a></p>'
                    . '<p>If you did not request this, ignore this email.</p>',
                    function ($m) use ($customer) {
                        $m->to($customer->email)->subject('Reset your bankOS portal password');
                    }
                );
            } catch (\Exception $e) {
                // Mail not configured — expose link in session during local dev only
                if (app()->isLocal()) {
                    return back()->with('dev_reset_url', $resetUrl);
                }
            }
        }

        // Always show the same message to prevent email enumeration
        return back()->with('status', 'If that email is registered, a password reset link has been sent.');
    }

    public function showReset(Request $request)
    {
        return view('auth.reset-password', [
            'token' => $request->token,
            'email' => $request->email,
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email'                 => 'required|email',
            'token'                 => 'required|string',
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->withErrors(['token' => 'This reset link is invalid or has already been used.']);
        }

        if (now()->diffInMinutes($record->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['token' => 'This reset link has expired. Please request a new one.']);
        }

        $customer = Customer::where('email', $request->email)->first();
        if (!$customer) {
            return back()->withErrors(['email' => 'No account found with that email.']);
        }

        $customer->update(['portal_password' => Hash::make($request->password)]);
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')
            ->with('success', 'Password reset successfully. You may now sign in with your new password.');
    }
}
