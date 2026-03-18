@extends('layouts.portal')
@section('title', 'Transfer')

@section('content')

{{-- Page Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px">
    <div style="display:flex;align-items:center;gap:12px">
        <a href="{{ route('dashboard') }}" style="width:34px;height:34px;border-radius:9px;border:1px solid #e5e7eb;background:white;display:grid;place-items:center;color:#6b7280;text-decoration:none;flex-shrink:0">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        </a>
        <div>
            <h1 style="font-size:20px;font-weight:800;color:#111827;margin:0">Send Money</h1>
            <p style="font-size:13px;color:#6b7280;margin:3px 0 0">Transfer funds to any account in this bank</p>
        </div>
    </div>
    <a href="{{ route('interbank-transfer') }}" style="display:flex;align-items:center;gap:7px;font-size:13px;font-weight:700;color:#2563eb;text-decoration:none;padding:9px 16px;border:1px solid #dbeafe;border-radius:10px;background:#eff6ff">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
        NIP Transfer
    </a>
</div>

{{-- Validation errors --}}
@if($errors->any())
<div style="margin-bottom:20px;padding:14px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:12px">
    <div style="display:flex;align-items:flex-start;gap:10px">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <div>
            @foreach($errors->all() as $error)
            <p style="font-size:13px;color:#991b1b;margin:0 0 3px;line-height:1.5">{{ $error }}</p>
            @endforeach
        </div>
    </div>
</div>
@endif

<div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start">

{{-- LEFT: Form --}}
<div>
    <form method="POST" action="{{ route('transfer.store') }}" id="transfer-form">
        @csrf

        {{-- Step 1: From Account --}}
        <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;padding:22px 24px;margin-bottom:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06)">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
                <div style="width:26px;height:26px;border-radius:50%;background:#2563eb;display:grid;place-items:center;flex-shrink:0">
                    <span style="font-size:11px;font-weight:800;color:white">1</span>
                </div>
                <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin:0">From Account</p>
            </div>

            <select name="from_account_id" id="from_account_id" required
                    style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;color:#374151;outline:none;background:white;box-sizing:border-box;appearance:none;background-image:url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E\");background-repeat:no-repeat;background-position:right 12px center">
                <option value="">Select account...</option>
                @foreach($accounts as $account)
                <option value="{{ $account->id }}"
                    data-balance="{{ $account->available_balance }}"
                    data-currency="{{ $account->currency ?? 'NGN' }}"
                    {{ old('from_account_id') == $account->id || request('from') == $account->id ? 'selected' : '' }}>
                    {{ $account->account_name }} — {{ $account->account_number }}
                </option>
                @endforeach
            </select>

            {{-- Balance chip (shown after selection) --}}
            <div id="balance-hint" style="display:none;margin-top:12px">
                <div style="display:flex;align-items:center;justify-content:space-between;padding:11px 14px;background:#f8fafc;border:1px solid #e5e7eb;border-radius:9px">
                    <p style="font-size:12px;color:#6b7280;margin:0">Available balance</p>
                    <p id="balance-display" style="font-size:14px;font-weight:800;color:#111827;margin:0;font-family:monospace"></p>
                </div>
            </div>
        </div>

        {{-- Step 2: Recipient --}}
        <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;padding:22px 24px;margin-bottom:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06)">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
                <div style="width:26px;height:26px;border-radius:50%;background:#2563eb;display:grid;place-items:center;flex-shrink:0">
                    <span style="font-size:11px;font-weight:800;color:white">2</span>
                </div>
                <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin:0">Recipient</p>
            </div>

            <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Account Number</label>
            <input type="text" name="to_account_number" id="to_account_number"
                   value="{{ old('to_account_number') }}"
                   maxlength="10" placeholder="Enter 10-digit account number"
                   style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:15px;color:#111827;font-family:monospace;letter-spacing:2px;outline:none;box-sizing:border-box @error('to_account_number') border-color:#dc2626 @enderror"
                   autocomplete="off">

            {{-- Lookup states --}}
            <div id="lookup-spinner" style="display:none;margin-top:10px;display:none;align-items:center;gap:8px">
                <svg style="animation:spin 1s linear infinite" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2"><circle cx="12" cy="12" r="10" stroke-opacity="0.2"/><path d="M12 2a10 10 0 0 1 10 10"/></svg>
                <span style="font-size:12px;color:#9ca3af">Verifying account...</span>
            </div>
            <div id="lookup-result" style="display:none;margin-top:10px">
                <div style="display:flex;align-items:center;gap:9px;padding:11px 14px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:9px">
                    <div style="width:28px;height:28px;border-radius:50%;background:#dcfce7;display:grid;place-items:center;flex-shrink:0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div>
                        <p style="font-size:10px;font-weight:700;color:#15803d;text-transform:uppercase;letter-spacing:.05em;margin:0">Account Verified</p>
                        <p style="font-size:13px;font-weight:700;color:#14532d;margin:2px 0 0" id="lookup-name"></p>
                    </div>
                </div>
            </div>
            <div id="lookup-error" style="display:none;margin-top:10px">
                <div style="display:flex;align-items:center;gap:9px;padding:11px 14px;background:#fef2f2;border:1px solid #fecaca;border-radius:9px">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span style="font-size:13px;color:#991b1b;font-weight:600" id="lookup-error-msg">Account not found</span>
                </div>
            </div>
        </div>

        {{-- Step 3: Amount + Narration --}}
        <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;padding:22px 24px;margin-bottom:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06)">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
                <div style="width:26px;height:26px;border-radius:50%;background:#2563eb;display:grid;place-items:center;flex-shrink:0">
                    <span style="font-size:11px;font-weight:800;color:white">3</span>
                </div>
                <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin:0">Transfer Details</p>
            </div>

            <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:8px">Amount</label>
            <div style="position:relative;margin-bottom:18px">
                <span style="position:absolute;left:16px;top:50%;transform:translateY(-50%);font-size:22px;font-weight:700;color:#9ca3af;pointer-events:none">₦</span>
                <input type="number" name="amount" id="amount"
                       value="{{ old('amount') }}"
                       min="1" step="0.01" placeholder="0.00"
                       style="width:100%;padding:16px 16px 16px 48px;border:1px solid #d1d5db;border-radius:10px;font-size:28px;font-weight:800;color:#111827;letter-spacing:-0.5px;outline:none;box-sizing:border-box;font-family:monospace @error('amount') border-color:#dc2626 @enderror">
            </div>

            <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">
                Narration <span style="font-size:12px;font-weight:400;color:#9ca3af">(optional)</span>
            </label>
            <input type="text" name="description" value="{{ old('description') }}"
                   placeholder="e.g. Rent payment, school fees..."
                   style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;color:#374151;outline:none;box-sizing:border-box">
        </div>

        {{-- Step 4: PIN --}}
        <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;padding:22px 24px;margin-bottom:20px;box-shadow:0 1px 3px rgba(0,0,0,0.06)">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
                <div style="width:26px;height:26px;border-radius:50%;background:#2563eb;display:grid;place-items:center;flex-shrink:0">
                    <span style="font-size:11px;font-weight:800;color:white">4</span>
                </div>
                <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin:0">Confirm with PIN</p>
            </div>

            <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Transaction PIN</label>
            <input type="password" name="pin" id="pin"
                   inputmode="numeric" maxlength="4" placeholder="● ● ● ●"
                   style="width:100%;padding:12px 16px;border:1px solid #d1d5db;border-radius:9px;font-size:22px;text-align:center;letter-spacing:10px;font-family:monospace;color:#111827;outline:none;box-sizing:border-box @error('pin') border-color:#dc2626 @enderror"
                   autocomplete="off">
            @error('pin')
            <p style="font-size:12px;color:#dc2626;margin:5px 0 0">{{ $message }}</p>
            @enderror
            <p style="font-size:12px;color:#9ca3af;margin:8px 0 0;line-height:1.5">
                Enter your 4-digit transaction PIN to authorise this transfer.
                <a href="{{ route('security') }}" style="color:#2563eb;font-weight:600;text-decoration:none">Set PIN</a>
            </p>
        </div>

        {{-- Submit button --}}
        <button type="submit" id="submit-btn"
                style="width:100%;background:#2563eb;color:white;font-size:15px;font-weight:700;padding:15px 20px;border-radius:12px;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;letter-spacing:0.01em">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
            Transfer Now
        </button>
    </form>
</div>

{{-- RIGHT: Info panel --}}
<div>
    {{-- Processing info --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;padding:20px;margin-bottom:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06)">
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin:0 0 14px">Transfer Info</p>
        <div style="display:flex;flex-direction:column;gap:12px">
            <div style="display:flex;align-items:flex-start;gap:10px">
                <div style="width:32px;height:32px;border-radius:8px;background:#eff6ff;display:grid;place-items:center;flex-shrink:0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div>
                    <p style="font-size:13px;font-weight:600;color:#111827;margin:0">Instant Processing</p>
                    <p style="font-size:12px;color:#6b7280;margin:2px 0 0;line-height:1.5">Intra-bank transfers are processed instantly, 24/7.</p>
                </div>
            </div>
            <div style="display:flex;align-items:flex-start;gap:10px">
                <div style="width:32px;height:32px;border-radius:8px;background:#f0fdf4;display:grid;place-items:center;flex-shrink:0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div>
                    <p style="font-size:13px;font-weight:600;color:#111827;margin:0">Secured Transfer</p>
                    <p style="font-size:12px;color:#6b7280;margin:2px 0 0;line-height:1.5">Your PIN authorises every transaction for your protection.</p>
                </div>
            </div>
            <div style="display:flex;align-items:flex-start;gap:10px">
                <div style="width:32px;height:32px;border-radius:8px;background:#fffbeb;display:grid;place-items:center;flex-shrink:0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </div>
                <div>
                    <p style="font-size:13px;font-weight:600;color:#111827;margin:0">Verify Recipient</p>
                    <p style="font-size:12px;color:#6b7280;margin:2px 0 0;line-height:1.5">Always confirm the account name before sending.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Inter-bank CTA --}}
    <div style="background:linear-gradient(135deg,#1e3a8a,#2563eb);border-radius:14px;padding:20px;color:white">
        <p style="font-size:11px;font-weight:700;color:rgba(191,219,254,0.9);text-transform:uppercase;letter-spacing:.06em;margin:0 0 6px">Sending to another bank?</p>
        <p style="font-size:13px;color:rgba(219,234,254,0.85);margin:0 0 14px;line-height:1.5">Use NIP Transfer to send money to any Nigerian bank via NIBSS.</p>
        <a href="{{ route('interbank-transfer') }}" style="display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,0.18);color:white;font-size:13px;font-weight:700;padding:9px 16px;border-radius:9px;text-decoration:none;border:1px solid rgba(255,255,255,0.2)">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            Go to NIP Transfer
        </a>
    </div>
</div>

</div>{{-- end grid --}}

<style>
@keyframes spin { to { transform: rotate(360deg); } }
</style>

<script>
const lookupUrl  = "{{ route('transfer.lookup') }}";
const acctInput  = document.getElementById('to_account_number');
const fromSelect = document.getElementById('from_account_id');
const balHint    = document.getElementById('balance-hint');
const balDisplay = document.getElementById('balance-display');
let lookupTimer;

fromSelect.addEventListener('change', function() {
    const opt = this.selectedOptions[0];
    if (opt.value) {
        const bal = parseFloat(opt.dataset.balance).toLocaleString('en-NG', {minimumFractionDigits: 2});
        balDisplay.textContent = (opt.dataset.currency || 'NGN') + ' ' + bal;
        balHint.style.display = 'block';
    } else {
        balHint.style.display = 'none';
    }
});

acctInput.addEventListener('input', function() {
    clearTimeout(lookupTimer);
    const val = this.value.replace(/\D/g, '');
    this.value = val;

    document.getElementById('lookup-result').style.display = 'none';
    document.getElementById('lookup-error').style.display = 'none';
    document.getElementById('lookup-spinner').style.display = 'none';

    if (val.length === 10) {
        document.getElementById('lookup-spinner').style.display = 'flex';
        lookupTimer = setTimeout(() => {
            fetch(lookupUrl + '?account_number=' + val, {
                headers: {'X-Requested-With': 'XMLHttpRequest'}
            })
            .then(r => r.json())
            .then(data => {
                document.getElementById('lookup-spinner').style.display = 'none';
                if (data.success) {
                    document.getElementById('lookup-name').textContent = data.account_name;
                    document.getElementById('lookup-result').style.display = 'block';
                } else {
                    document.getElementById('lookup-error-msg').textContent = data.message || 'Account not found';
                    document.getElementById('lookup-error').style.display = 'block';
                }
            })
            .catch(() => {
                document.getElementById('lookup-spinner').style.display = 'none';
                document.getElementById('lookup-error-msg').textContent = 'Could not verify account.';
                document.getElementById('lookup-error').style.display = 'block';
            });
        }, 500);
    }
});

if (fromSelect.value) fromSelect.dispatchEvent(new Event('change'));
if (acctInput.value.length === 10) acctInput.dispatchEvent(new Event('input'));
</script>
@endsection
