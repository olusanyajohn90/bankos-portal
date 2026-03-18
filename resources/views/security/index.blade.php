@extends('layouts.portal')
@section('title', 'Security Centre')

@section('content')
<div style="margin-bottom:28px">
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Settings</p>
    <h1 style="font-size:20px;font-weight:800;color:#111827;margin:0">Security Centre</h1>
</div>

@if(session('success'))
<div style="margin-bottom:16px;padding:12px 16px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;color:#15803d;font-size:13px;font-weight:500;display:flex;align-items:center;gap:8px">
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div style="margin-bottom:16px;padding:12px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;color:#991b1b;font-size:13px">{{ session('error') }}</div>
@endif

{{-- Security score --}}
@php
$customer = auth('customer')->user();
$secScore = 40;
$secScore += $customer->two_factor_enabled ? 40 : 0;
$secScore += $customer->portal_pin ? 20 : 0;
$secLabel = $secScore >= 80 ? 'Strong' : ($secScore >= 50 ? 'Moderate' : 'Weak');
$barColor = $secScore >= 80 ? '#86efac' : ($secScore >= 50 ? '#fde68a' : '#fca5a5');
@endphp
<div style="background:linear-gradient(135deg,#1e3a8a 0%,#2563eb 100%);border-radius:14px;padding:22px 24px;color:white;margin-bottom:20px;position:relative;overflow:hidden">
    <div style="position:absolute;right:-30px;top:-30px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,0.06)"></div>
    <div style="position:absolute;right:60px;bottom:-40px;width:100px;height:100px;border-radius:50%;background:rgba(255,255,255,0.04)"></div>
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;position:relative">
        <div>
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.8)" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                <p style="font-size:13px;font-weight:700;margin:0">Security Score</p>
            </div>
            <p style="font-size:11px;color:rgba(255,255,255,0.6);margin:0">Enable all features for maximum protection</p>
        </div>
        <div style="text-align:right">
            <p style="font-size:32px;font-weight:900;line-height:1;margin:0">{{ $secScore }}</p>
            <p style="font-size:11px;font-weight:700;color:{{ $barColor }};margin:2px 0 0 0">{{ $secLabel }}</p>
        </div>
    </div>
    <div style="height:6px;background:rgba(255,255,255,0.15);border-radius:6px;overflow:hidden;position:relative;margin-bottom:16px">
        <div style="height:100%;width:{{ $secScore }}%;background:{{ $barColor }};border-radius:6px"></div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;position:relative">
        @php
        $checks = [
            ['label'=>'Password Set','done'=>true],
            ['label'=>'PIN Set','done'=>(bool)$customer->portal_pin],
            ['label'=>'2FA Active','done'=>(bool)$customer->two_factor_enabled],
        ];
        @endphp
        @foreach($checks as $chk)
        <div style="background:rgba(255,255,255,0.1);border-radius:9px;padding:9px 12px;display:flex;align-items:center;gap:7px">
            @if($chk['done'])
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#86efac" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.35)" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>
            @endif
            <span style="font-size:10px;font-weight:600;color:rgba(255,255,255,0.85)">{{ $chk['label'] }}</span>
        </div>
        @endforeach
    </div>
</div>

{{-- PIN + Password in 2 columns --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;align-items:start">

    {{-- Transaction PIN --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:22px">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px">
            <div style="width:38px;height:38px;border-radius:11px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </div>
            <div>
                <p style="font-size:13px;font-weight:700;color:#111827;margin:0">Transaction PIN</p>
                <p style="font-size:11px;color:#9ca3af;margin:0">4-digit PIN for transfers</p>
            </div>
        </div>
        @if(session('pin_success'))
        <div style="margin-bottom:12px;padding:8px 12px;background:#f0fdf4;border-radius:8px;color:#15803d;font-size:12px;font-weight:500;display:flex;align-items:center;gap:6px">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            {{ session('pin_success') }}
        </div>
        @endif
        <form method="POST" action="{{ route('security.pin') }}">
            @csrf
            <div style="margin-bottom:10px">
                <label style="display:block;font-size:11px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">New PIN</label>
                <input type="password" name="pin" maxlength="4" minlength="4" pattern="\d{4}" placeholder="• • • •"
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:22px;letter-spacing:10px;text-align:center;box-sizing:border-box;outline:none" required>
            </div>
            <div style="margin-bottom:16px">
                <label style="display:block;font-size:11px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Confirm PIN</label>
                <input type="password" name="pin_confirmation" maxlength="4" minlength="4" pattern="\d{4}" placeholder="• • • •"
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:22px;letter-spacing:10px;text-align:center;box-sizing:border-box;outline:none" required>
            </div>
            <button type="submit" style="width:100%;background:#2563eb;color:white;font-size:13px;font-weight:700;padding:11px;border-radius:10px;border:none;cursor:pointer">
                {{ $customer->portal_pin ? 'Update PIN' : 'Set PIN' }}
            </button>
        </form>
    </div>

    {{-- Change Password --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:22px">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px">
            <div style="width:38px;height:38px;border-radius:11px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg>
            </div>
            <div>
                <p style="font-size:13px;font-weight:700;color:#111827;margin:0">Login Password</p>
                <p style="font-size:11px;color:#9ca3af;margin:0">Portal access password</p>
            </div>
        </div>
        <form method="POST" action="{{ route('profile.password') }}">
            @csrf
            <div style="margin-bottom:10px">
                <label style="display:block;font-size:11px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Current Password</label>
                <input type="password" name="current_password" placeholder="Current password"
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none" required>
            </div>
            <div style="margin-bottom:10px">
                <label style="display:block;font-size:11px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">New Password</label>
                <input type="password" name="password" placeholder="Min. 8 characters"
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none" required>
            </div>
            <div style="margin-bottom:16px">
                <label style="display:block;font-size:11px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Confirm Password</label>
                <input type="password" name="password_confirmation" placeholder="Confirm new password"
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none" required>
            </div>
            <button type="submit" style="width:100%;background:#15803d;color:white;font-size:13px;font-weight:700;padding:11px;border-radius:10px;border:none;cursor:pointer">
                Change Password
            </button>
        </form>
    </div>
</div>

{{-- 2FA --}}
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:22px;margin-bottom:16px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
        <div style="display:flex;align-items:center;gap:12px">
            <div style="width:42px;height:42px;border-radius:12px;background:{{ $customer->two_factor_enabled ? '#f0fdf4' : '#fef9c3' }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="{{ $customer->two_factor_enabled ? '#16a34a' : '#ca8a04' }}" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <div>
                <p style="font-size:14px;font-weight:700;color:#111827;margin:0">Two-Factor Authentication</p>
                <p style="font-size:12px;color:#9ca3af;margin:2px 0 0 0">{{ $customer->two_factor_enabled ? 'Active — extra layer of protection on your account' : 'Disabled — add extra protection to your account' }}</p>
            </div>
        </div>
        <span style="font-size:10px;font-weight:800;padding:5px 12px;border-radius:20px;letter-spacing:.04em;background:{{ $customer->two_factor_enabled ? '#f0fdf4' : '#fef9c3' }};color:{{ $customer->two_factor_enabled ? '#15803d' : '#92400e' }};border:1px solid {{ $customer->two_factor_enabled ? '#bbf7d0' : '#fde68a' }}">
            {{ $customer->two_factor_enabled ? 'ENABLED' : 'DISABLED' }}
        </span>
    </div>

    @if(!$customer->two_factor_enabled)
    <div id="setup-2fa-area">
        <p style="font-size:12px;color:#6b7280;line-height:1.6;margin-bottom:14px">
            Protect your account with an authenticator app (Google Authenticator, Authy, etc.). Each login requires a 6-digit code from your app.
        </p>
        <button onclick="start2faSetup()" id="btn-start-2fa"
                style="background:#2563eb;color:white;font-size:13px;font-weight:700;padding:11px 22px;border-radius:10px;border:none;cursor:pointer">
            Set Up 2FA
        </button>

        <div id="2fa-setup-steps" style="display:none;margin-top:18px">
            <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:18px;margin-bottom:14px">
                <p style="font-size:12px;font-weight:700;color:#374151;margin-bottom:12px">Step 1 — Scan this QR code in your authenticator app</p>
                <div style="display:flex;justify-content:center;margin-bottom:12px">
                    <canvas id="qr-canvas" width="160" height="160" style="border-radius:8px"></canvas>
                </div>
                <p style="font-size:11px;color:#9ca3af;text-align:center;margin-bottom:4px">Or enter this secret manually:</p>
                <p id="2fa-secret-display" style="font-size:14px;font-weight:700;color:#111827;text-align:center;letter-spacing:4px;font-family:monospace;background:white;padding:8px;border-radius:8px;border:1px solid #e5e7eb"></p>
            </div>
            <div style="margin-bottom:12px">
                <label style="display:block;font-size:11px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Step 2 — Enter the 6-digit code from your app</label>
                <input type="text" id="2fa-code-input" maxlength="6" placeholder="0 0 0 0 0 0"
                       style="width:100%;padding:12px;border:1px solid #d1d5db;border-radius:9px;font-size:24px;letter-spacing:12px;text-align:center;box-sizing:border-box;outline:none;font-family:monospace">
            </div>
            <div id="2fa-confirm-error" style="display:none;color:#dc2626;font-size:12px;margin-bottom:10px;padding:8px 12px;background:#fef2f2;border-radius:8px"></div>
            <button onclick="confirm2fa()"
                    style="width:100%;background:#15803d;color:white;font-size:13px;font-weight:700;padding:11px;border-radius:10px;border:none;cursor:pointer">
                Verify &amp; Activate 2FA
            </button>
        </div>
    </div>
    @else
    <form method="POST" action="{{ route('security.2fa.disable') }}" onsubmit="return confirm('Disabling 2FA reduces your account security. Continue?')">
        @csrf
        <p style="font-size:12px;color:#6b7280;margin-bottom:12px">Your account is protected with 2FA. Disable only if you need to set up a new device.</p>
        <div style="margin-bottom:14px">
            <label style="display:block;font-size:11px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Enter your password to confirm</label>
            <input type="password" name="password" placeholder="Your login password"
                   style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none" required>
        </div>
        <button type="submit" style="background:#dc2626;color:white;font-size:13px;font-weight:700;padding:10px 22px;border-radius:10px;border:none;cursor:pointer">
            Disable 2FA
        </button>
    </form>
    @endif
</div>

{{-- Account Freeze --}}
@if($accounts->isNotEmpty())
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:22px;margin-bottom:16px">
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Account Controls</p>
    <p style="font-size:12px;color:#9ca3af;margin-bottom:18px">Temporarily freeze an account to block all transactions</p>
    <div style="display:flex;flex-direction:column;gap:2px">
        @foreach($accounts as $acct)
        @php $settings = \App\Models\CustomerAccountSetting::forAccount($acct->id, auth('customer')->id()); @endphp
        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid #f3f4f6;border-radius:10px;background:{{ $settings->is_frozen ? '#fef9f9' : 'white' }}">
            <div style="display:flex;align-items:center;gap:12px">
                <div style="width:36px;height:36px;border-radius:10px;background:{{ $settings->is_frozen ? '#fef2f2' : '#f3f4f6' }};display:flex;align-items:center;justify-content:center">
                    @if($settings->is_frozen)
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 9.9-1"/></svg>
                    @endif
                </div>
                <div>
                    <p style="font-size:13px;font-weight:600;color:#111827;margin:0">{{ $settings->nickname ?? $acct->account_name }}</p>
                    <p style="font-size:11px;color:#9ca3af;margin:0">{{ $acct->account_number }} · {{ $acct->account_type }}</p>
                    @if($settings->is_frozen)
                    <p style="font-size:10px;font-weight:700;color:#dc2626;margin:2px 0 0 0;letter-spacing:.02em">FROZEN — all debits blocked</p>
                    @endif
                </div>
            </div>
            @if($settings->is_frozen)
            <form method="POST" action="{{ route('accounts.unfreeze', $acct->id) }}">
                @csrf
                <button type="submit" style="font-size:12px;font-weight:700;background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;padding:8px 16px;border-radius:9px;cursor:pointer">
                    Unfreeze
                </button>
            </form>
            @else
            <form method="POST" action="{{ route('accounts.freeze', $acct->id) }}" onsubmit="return confirm('Freeze this account? All debits will be blocked.')">
                @csrf
                <button type="submit" style="font-size:12px;font-weight:700;background:#fef2f2;color:#dc2626;border:1px solid #fecaca;padding:8px 16px;border-radius:9px;cursor:pointer">
                    Freeze
                </button>
            </form>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Sign out all --}}
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:20px;margin-bottom:20px">
    <div style="display:flex;align-items:center;justify-content:space-between">
        <div style="display:flex;align-items:center;gap:12px">
            <div style="width:38px;height:38px;border-radius:11px;background:#fef2f2;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            </div>
            <div>
                <p style="font-size:13px;font-weight:700;color:#111827;margin:0">Sign Out All Devices</p>
                <p style="font-size:11px;color:#9ca3af;margin:2px 0 0 0">Immediately end all active sessions</p>
            </div>
        </div>
        <form method="POST" action="{{ route('security.logout-all') }}" onsubmit="return confirm('This will log you out of all devices including this one. Continue?')">
            @csrf
            <button type="submit" style="font-size:12px;font-weight:700;color:#dc2626;background:#fef2f2;border:1px solid #fecaca;padding:9px 18px;border-radius:9px;cursor:pointer">
                Sign Out All
            </button>
        </form>
    </div>
</div>

{{-- Login history --}}
<p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px">Recent Login Activity</p>
@if($loginHistory->isEmpty())
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;padding:40px;text-align:center">
    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5" style="display:block;margin:0 auto 10px"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
    <p style="font-size:13px;color:#9ca3af;margin:0">No login history recorded yet.</p>
</div>
@else
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.06)">
    @foreach($loginHistory as $log)
    <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid #f9fafb">
        <div style="display:flex;align-items:center;gap:12px">
            <div style="width:8px;height:8px;border-radius:50%;background:{{ $log->status === 'success' ? '#16a34a' : '#dc2626' }};flex-shrink:0;box-shadow:0 0 0 3px {{ $log->status === 'success' ? '#dcfce7' : '#fee2e2' }}"></div>
            <div>
                <p style="font-size:12px;font-weight:700;color:#111827;margin:0">{{ $log->device ?? 'Unknown device' }}</p>
                <p style="font-size:11px;color:#9ca3af;margin:1px 0 0 0">{{ $log->ip_address ?? 'Unknown IP' }}{{ $log->location ? ' · '.$log->location : '' }}</p>
            </div>
        </div>
        <div style="text-align:right">
            <p style="font-size:11px;font-weight:700;color:{{ $log->status === 'success' ? '#16a34a' : '#dc2626' }};margin:0">{{ ucfirst($log->status) }}</p>
            <p style="font-size:11px;color:#9ca3af;margin:1px 0 0 0">{{ $log->created_at->format('d M Y, H:i') }}</p>
        </div>
    </div>
    @endforeach
</div>
@endif

<script>
let twoFaSecret = '';

async function start2faSetup() {
    const btn = document.getElementById('btn-start-2fa');
    btn.textContent = 'Loading…';
    btn.disabled = true;
    try {
        const res = await fetch('{{ route('security.2fa.enable') }}', {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json'}
        });
        const data = await res.json();
        twoFaSecret = data.secret;
        document.getElementById('2fa-secret-display').textContent = twoFaSecret;
        document.getElementById('2fa-setup-steps').style.display = 'block';
        btn.style.display = 'none';
        drawQr(data.otpauth);
    } catch(e) {
        btn.textContent = 'Set Up 2FA';
        btn.disabled = false;
        alert('Failed to start 2FA setup. Please try again.');
    }
}

function drawQr(url) {
    const canvas = document.getElementById('qr-canvas');
    const ctx = canvas.getContext('2d');
    ctx.fillStyle = '#f9fafb';
    ctx.fillRect(0, 0, 160, 160);
    ctx.fillStyle = '#374151';
    ctx.font = '9px monospace';
    ctx.textAlign = 'center';
    ctx.fillText('Scan in authenticator app', 80, 20);
    ctx.fillText('or use the secret below', 80, 35);
    ctx.fillStyle = '#111827';
    const parts = url.split('secret=');
    if(parts[1]) {
        const secret = parts[1].split('&')[0];
        for(let i=0; i<secret.length && i<16; i++) {
            const x = 20 + (i % 8) * 15;
            const y = 55 + Math.floor(i / 8) * 15;
            ctx.fillRect(x, y, 12, 12);
        }
    }
    ctx.fillStyle = '#6b7280';
    ctx.font = '9px monospace';
    ctx.fillText('Use secret key above', 80, 140);
    ctx.fillText('in your auth app', 80, 152);
}

async function confirm2fa() {
    const code = document.getElementById('2fa-code-input').value.trim();
    const errEl = document.getElementById('2fa-confirm-error');
    if(code.length !== 6 || !/^\d{6}$/.test(code)) {
        errEl.style.display = 'block';
        errEl.textContent = 'Please enter a valid 6-digit code.';
        return;
    }
    try {
        const res = await fetch('{{ route('security.2fa.confirm') }}', {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json'},
            body: JSON.stringify({code, secret: twoFaSecret})
        });
        const data = await res.json();
        if(data.success) {
            window.location.reload();
        } else {
            errEl.style.display = 'block';
            errEl.textContent = data.message || 'Invalid code. Please try again.';
        }
    } catch(e) {
        errEl.style.display = 'block';
        errEl.textContent = 'Something went wrong. Please try again.';
    }
}
</script>
@endsection
