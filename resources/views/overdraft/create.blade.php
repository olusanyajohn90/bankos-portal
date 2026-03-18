@extends('layouts.portal')
@section('title', 'Apply for Overdraft')

@section('content')

<div style="display:flex;align-items:center;gap:12px;margin-bottom:28px">
    <a href="{{ route('overdraft') }}"
       style="display:flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:9px;border:1px solid #e5e7eb;color:#6b7280;text-decoration:none;flex-shrink:0;background:white">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#111827;margin:0 0 3px">Apply for Overdraft</h1>
        <p style="font-size:13px;color:#6b7280;margin:0">Subject to credit assessment &middot; Decision in 2–3 business days</p>
    </div>
</div>

@if(session('error'))
<div style="display:flex;align-items:flex-start;gap:10px;padding:13px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;font-size:13px;color:#991b1b;margin-bottom:18px;font-weight:500">
    <svg style="flex-shrink:0;margin-top:1px" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <span>{{ session('error') }}</span>
</div>
@endif

@if($errors->any())
<div style="display:flex;align-items:flex-start;gap:10px;padding:13px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;font-size:13px;color:#991b1b;margin-bottom:18px;font-weight:500">
    <svg style="flex-shrink:0;margin-top:1px" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <span>{{ $errors->first() }}</span>
</div>
@endif

<div style="max-width:600px">
    <form method="POST" action="{{ route('overdraft.store') }}">
        @csrf

        {{-- Account --}}
        <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:22px;margin-bottom:14px">
            <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin:0 0 16px">Account</p>
            <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:7px">Select Account <span style="color:#dc2626">*</span></label>
            <select name="account_id" required
                    style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;outline:none;background:white;box-sizing:border-box;color:#111827">
                <option value="">— Select an active account —</option>
                @foreach($accounts as $acc)
                <option value="{{ $acc->id }}" {{ old('account_id') === $acc->id ? 'selected' : '' }}>
                    {{ $acc->account_name }} ({{ $acc->account_number }})
                    @if($acc->account_type) — {{ strtoupper($acc->account_type) }} @endif
                </option>
                @endforeach
            </select>
            <p style="font-size:11px;color:#9ca3af;margin:6px 0 0">Overdraft facilities are available for current and savings accounts. Eligibility varies by account type.</p>
        </div>

        {{-- Overdraft Details --}}
        <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:22px;margin-bottom:14px">
            <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin:0 0 16px">Overdraft Details</p>

            <div style="margin-bottom:18px">
                <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:7px">Requested Overdraft Limit (NGN) <span style="color:#dc2626">*</span></label>
                <div style="position:relative">
                    <span style="position:absolute;left:13px;top:50%;transform:translateY(-50%);font-size:12px;color:#9ca3af;font-weight:700;pointer-events:none">NGN</span>
                    <input type="number" name="requested_limit" id="requested_limit"
                           value="{{ old('requested_limit') }}"
                           min="10000" step="1000" required
                           placeholder="e.g. 50000"
                           oninput="updateLimitDisplay(this.value)"
                           style="width:100%;padding:10px 12px 10px 52px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;outline:none;box-sizing:border-box;color:#111827">
                </div>
                <div id="limit-display" style="display:none;margin-top:8px;padding:10px 14px;background:#eff6ff;border-radius:9px;font-size:13px;color:#1d4ed8;font-weight:700"></div>
                <p style="font-size:11px;color:#9ca3af;margin:6px 0 0">Minimum: NGN 10,000. The actual approved amount may differ.</p>
            </div>

            <div>
                <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:7px">Purpose of Overdraft <span style="color:#dc2626">*</span></label>
                <textarea name="purpose" rows="3" maxlength="500" required
                          placeholder="Describe why you need the overdraft facility, e.g. to cover payroll shortfall, supplier payment, medical expenses..."
                          style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;outline:none;box-sizing:border-box;resize:vertical;color:#111827">{{ old('purpose') }}</textarea>
                <p style="font-size:11px;color:#9ca3af;margin:5px 0 0">Max 500 characters. Be specific — this helps our credit team assess your request.</p>
            </div>
        </div>

        {{-- Financial Information --}}
        <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:22px;margin-bottom:14px">
            <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin:0 0 4px">Financial Information</p>
            <p style="font-size:12px;color:#9ca3af;margin:0 0 16px">Optional — improves approval chances</p>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div>
                    <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:7px">Monthly Income (NGN)</label>
                    <div style="position:relative">
                        <span style="position:absolute;left:13px;top:50%;transform:translateY(-50%);font-size:12px;color:#9ca3af;font-weight:700;pointer-events:none">NGN</span>
                        <input type="number" name="monthly_income"
                               value="{{ old('monthly_income') }}"
                               min="0" step="1000"
                               placeholder="e.g. 150000"
                               style="width:100%;padding:10px 12px 10px 52px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;outline:none;box-sizing:border-box;color:#111827">
                    </div>
                </div>

                <div>
                    <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:7px">Employer / Business</label>
                    <input type="text" name="employer"
                           value="{{ old('employer') }}"
                           maxlength="200"
                           placeholder="Where you work or your business name"
                           style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;outline:none;box-sizing:border-box;color:#111827">
                </div>
            </div>
        </div>

        {{-- Disclaimer --}}
        <div style="display:flex;gap:12px;padding:16px 18px;background:#fffbeb;border:1px solid #fde68a;border-radius:12px;margin-bottom:22px">
            <div style="flex-shrink:0;margin-top:1px">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#92400e" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            <div>
                <p style="font-size:13px;font-weight:700;color:#92400e;margin:0 0 7px">Important — Please Read</p>
                <ul style="font-size:12px;color:#92400e;line-height:1.8;margin:0;padding-left:16px">
                    <li>Approval is subject to a credit assessment by our lending team.</li>
                    <li>Approval of an application does not guarantee drawdown of funds.</li>
                    <li>Interest will be charged on the outstanding overdrawn balance at the bank's current rate.</li>
                    <li>The approved limit may be lower than the requested amount based on your creditworthiness.</li>
                    <li>The bank reserves the right to revoke or reduce the facility at any time.</li>
                </ul>
            </div>
        </div>

        <button type="submit"
                style="width:100%;background:#2563eb;color:white;font-size:13px;font-weight:700;padding:13px;border-radius:10px;border:none;cursor:pointer">
            Submit Application
        </button>
    </form>
</div>

<script>
function updateLimitDisplay(val) {
    var display = document.getElementById('limit-display');
    var num = parseFloat(val);
    if (isNaN(num) || num <= 0) { display.style.display = 'none'; return; }
    display.style.display = 'block';
    display.textContent = 'Requesting: NGN ' + num.toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
document.addEventListener('DOMContentLoaded', function() {
    var input = document.getElementById('requested_limit');
    if (input && input.value) updateLimitDisplay(input.value);
});
</script>

@endsection
