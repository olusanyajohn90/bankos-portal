<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Customer;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    // ──────────────────────────────────────────────
    // Step 1 — Personal Info
    // ──────────────────────────────────────────────

    public function start(): View
    {
        session()->forget('onboarding');
        return view('onboarding.step1');
    }

    public function storeStep1(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'email'     => ['required', 'email', 'max:120'],
            'phone'     => ['required', 'string', 'max:20'],
        ]);

        // Check email not already registered
        if (Customer::where('email', $data['email'])->exists()) {
            return back()->withInput()->withErrors([
                'email' => 'This email address is already registered. Please sign in.',
            ]);
        }

        session(['onboarding' => array_merge(session('onboarding', []), $data)]);

        return redirect()->route('onboarding.step2');
    }

    // ──────────────────────────────────────────────
    // Step 2 — Password
    // ──────────────────────────────────────────────

    public function step2(): View|RedirectResponse
    {
        $this->redirectIfNoSession(['full_name', 'email', 'phone']);
        return view('onboarding.step2');
    }

    public function storeStep2(Request $request): RedirectResponse
    {
        $this->redirectIfNoSession(['full_name', 'email', 'phone']);

        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        session(['onboarding' => array_merge(session('onboarding', []), [
            'password_hash' => Hash::make($data['password']),
        ])]);

        return redirect()->route('onboarding.step3');
    }

    // ──────────────────────────────────────────────
    // Step 3 — Account Type
    // ──────────────────────────────────────────────

    public function step3(): View|RedirectResponse
    {
        $this->redirectIfNoSession(['full_name', 'email', 'phone', 'password_hash']);
        return view('onboarding.step3');
    }

    public function storeStep3(Request $request): RedirectResponse
    {
        $this->redirectIfNoSession(['full_name', 'email', 'phone', 'password_hash']);

        $data = $request->validate([
            'account_type' => ['required', 'string', 'in:savings,current,wallet'],
        ]);

        session(['onboarding' => array_merge(session('onboarding', []), $data)]);

        return redirect()->route('onboarding.step4');
    }

    // ──────────────────────────────────────────────
    // Step 4 — BVN Verification (mock)
    // ──────────────────────────────────────────────

    public function step4(): View|RedirectResponse
    {
        $this->redirectIfNoSession(['full_name', 'email', 'phone', 'password_hash', 'account_type']);
        return view('onboarding.step4');
    }

    public function storeStep4(Request $request): RedirectResponse
    {
        $this->redirectIfNoSession(['full_name', 'email', 'phone', 'password_hash', 'account_type']);

        $data = $request->validate([
            'bvn' => ['required', 'digits:11'],
        ]);

        // Mock BVN verification — always passes
        session(['onboarding' => array_merge(session('onboarding', []), $data)]);

        return redirect()->route('onboarding.step5');
    }

    // ──────────────────────────────────────────────
    // Step 5 — ID Upload (optional)
    // ──────────────────────────────────────────────

    public function step5(): View|RedirectResponse
    {
        $this->redirectIfNoSession(['full_name', 'email', 'phone', 'password_hash', 'account_type', 'bvn']);
        return view('onboarding.step5');
    }

    public function storeStep5(Request $request): RedirectResponse
    {
        $this->redirectIfNoSession(['full_name', 'email', 'phone', 'password_hash', 'account_type', 'bvn']);

        $request->validate([
            'id_document' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $onboarding = session('onboarding', []);

        if ($request->hasFile('id_document') && $request->file('id_document')->isValid()) {
            $ext  = $request->file('id_document')->getClientOriginalExtension();
            $uuid = (string) Str::uuid();
            $path = $request->file('id_document')->storeAs('kyc', $uuid . '.' . $ext, 'public');
            $onboarding['id_document_path'] = $path;
        }

        session(['onboarding' => $onboarding]);

        return redirect()->route('onboarding.review');
    }

    // ──────────────────────────────────────────────
    // Review
    // ──────────────────────────────────────────────

    public function review(): View|RedirectResponse
    {
        $this->redirectIfNoSession(['full_name', 'email', 'phone', 'password_hash', 'account_type', 'bvn']);
        return view('onboarding.review', ['onboarding' => session('onboarding', [])]);
    }

    // ──────────────────────────────────────────────
    // Submit
    // ──────────────────────────────────────────────

    public function submit(Request $request): RedirectResponse
    {
        $this->redirectIfNoSession(['full_name', 'email', 'phone', 'password_hash', 'account_type', 'bvn']);

        $ob = session('onboarding', []);

        // 1. Determine tenant
        $tenant = Tenant::where('status', 'active')->first();
        if (!$tenant) {
            return back()->withErrors(['general' => 'Service temporarily unavailable. Please try again later.']);
        }

        // 2. Split full_name into first/last
        $nameParts = explode(' ', trim($ob['full_name']), 2);
        $firstName = $nameParts[0];
        $lastName  = $nameParts[1] ?? $nameParts[0];

        // 3. Create customer
        $customerId = (string) Str::uuid();
        $customer   = Customer::create([
            'id'              => $customerId,
            'tenant_id'       => $tenant->id,
            'first_name'      => $firstName,
            'last_name'       => $lastName,
            'email'           => $ob['email'],
            'phone'           => $ob['phone'],
            'portal_password' => $ob['password_hash'],
            'customer_number' => 'CUS' . str_pad((string) mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT),
            'kyc_tier'        => 'level_1',
            'kyc_status'      => 'manual_review',
            'portal_active'   => 1,
            'portal_pin'      => Hash::make('0000'),
            'status'          => 'active',
            'bvn'             => $ob['bvn'],
        ]);

        // 4. Create account
        $accountType = $ob['account_type'];
        $currency    = 'NGN';

        Account::create([
            'id'                => (string) Str::uuid(),
            'tenant_id'         => $tenant->id,
            'customer_id'       => $customer->id,
            'account_number'    => '20' . str_pad((string) mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT),
            'account_name'      => strtoupper($ob['full_name']),
            'type'              => $accountType,
            'currency'          => $currency,
            'available_balance' => 0,
            'ledger_balance'    => 0,
            'status'            => 'active',
        ]);

        // 5. Clear session
        session()->forget('onboarding');

        return redirect()->route('login')
            ->with('success', 'Your account has been created successfully. You can now sign in.');
    }

    // ──────────────────────────────────────────────
    // Private helpers
    // ──────────────────────────────────────────────

    private function redirectIfNoSession(array $requiredKeys): void
    {
        $ob = session('onboarding', []);
        foreach ($requiredKeys as $key) {
            if (empty($ob[$key])) {
                redirect()->route('onboarding.start')->send();
                exit;
            }
        }
    }
}
