@extends('layouts.portal')
@section('title', 'Account Statement')

@section('content')

{{-- Page header --}}
<div style="display:flex;align-items:center;gap:12px;margin-bottom:28px">
    <a href="{{ route('accounts.show', $account->id) }}" style="width:34px;height:34px;border-radius:9px;border:1px solid #e5e7eb;background:white;display:flex;align-items:center;justify-content:center;color:#6b7280;flex-shrink:0;text-decoration:none">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#111827">Account Statement</h1>
        <p style="font-size:12px;color:#9ca3af;margin-top:2px">{{ $account->account_name }} &mdash; <span style="font-family:monospace">{{ $account->account_number }}</span></p>
    </div>
</div>

<div style="max-width:660px">

    @if($errors->any())
    <div style="margin-bottom:16px;padding:12px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;display:flex;align-items:center;gap:10px">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <p style="font-size:13px;color:#991b1b">{{ $errors->first() }}</p>
    </div>
    @endif

    @if(session('success'))
    <div style="margin-bottom:16px;padding:12px 16px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;display:flex;align-items:center;gap:10px">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <p style="font-size:13px;color:#15803d">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Statement Period --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:22px;margin-bottom:14px">
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:14px">Statement Period</p>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">From Date</label>
                <input type="date" id="shared-from"
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none"
                       onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#d1d5db'">
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">To Date</label>
                <input type="date" id="shared-to"
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none"
                       onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#d1d5db'">
            </div>
        </div>
        <p style="font-size:11px;color:#9ca3af;margin-top:8px">Leave blank to include all transactions</p>
    </div>

    {{-- Standard Statement --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:22px;margin-bottom:12px">
        <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:18px">
            <div style="width:44px;height:44px;border-radius:12px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            </div>
            <div style="flex:1">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:3px">
                    <p style="font-size:15px;font-weight:700;color:#111827">Standard Statement</p>
                    <span style="font-size:11px;font-weight:700;color:#15803d;background:#f0fdf4;padding:2px 8px;border-radius:20px;border:1px solid #bbf7d0">FREE</span>
                </div>
                <p style="font-size:12px;color:#6b7280;line-height:1.6">Instant download. Suitable for personal records and most online verifications. No official stamp.</p>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
            <form method="GET" action="{{ route('accounts.statement.pdf', $account->id) }}">
                <input type="hidden" name="from" id="pdf-from">
                <input type="hidden" name="to" id="pdf-to">
                <button type="submit"
                        style="width:100%;background:#2563eb;color:white;font-size:13px;font-weight:700;padding:11px 12px;border-radius:10px;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:7px"
                        onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Download PDF
                </button>
            </form>

            <form method="GET" action="{{ route('accounts.statement.excel', $account->id) }}">
                <input type="hidden" name="from" id="excel-from">
                <input type="hidden" name="to" id="excel-to">
                <button type="submit"
                        style="width:100%;background:#15803d;color:white;font-size:13px;font-weight:700;padding:11px 12px;border-radius:10px;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:7px"
                        onmouseover="this.style.background='#166534'" onmouseout="this.style.background='#15803d'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="9" y1="3" x2="9" y2="21"/></svg>
                    Download Excel
                </button>
            </form>
        </div>
    </div>

    {{-- Official Signed Statement --}}
    <div style="background:white;border:2px solid #2563eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:22px">
        <div style="display:flex;align-items:flex-start;gap:14px;margin-bottom:18px">
            <div style="width:44px;height:44px;border-radius:12px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </div>
            <div style="flex:1">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:3px">
                    <p style="font-size:15px;font-weight:700;color:#111827">Official Bank Statement</p>
                    <span style="font-size:10px;font-weight:700;color:#2563eb;background:#eff6ff;padding:2px 8px;border-radius:20px;border:1px solid #bfdbfe">RECOMMENDED</span>
                </div>
                <p style="font-size:12px;color:#6b7280;line-height:1.6">Officially stamped and referenced. Required for visa applications, loan requests, tenancy agreements, and official use. Delivered as PDF to your registered email.</p>
                <div style="margin-top:10px;display:flex;flex-wrap:wrap;gap:14px">
                    <div style="display:flex;align-items:center;gap:6px">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        <span style="font-size:12px;color:#374151;font-weight:600">{{ auth('customer')->user()->email ?? '—' }}</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:6px">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        <span style="font-size:13px;font-weight:800;color:#2563eb">NGN {{ number_format($fee, 2) }}</span>
                        <span style="font-size:11px;color:#9ca3af">fee</span>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('accounts.statement.signed', $account->id) }}">
            @csrf
            <input type="hidden" name="from" id="signed-from">
            <input type="hidden" name="to" id="signed-to">
            <button type="submit"
                    style="width:100%;background:linear-gradient(135deg,#1e40af,#2563eb);color:white;font-size:13px;font-weight:700;padding:12px 20px;border-radius:10px;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                Email Official Statement &mdash; NGN {{ number_format($fee, 2) }}
            </button>
        </form>
        <p style="font-size:11px;color:#9ca3af;margin-top:8px;text-align:center">NGN {{ number_format($fee, 2) }} will be deducted from this account. PDF sent to your email.</p>
    </div>

</div>

<script>
const fromInput = document.getElementById('shared-from');
const toInput   = document.getElementById('shared-to');
function syncDates() {
    document.getElementById('pdf-from').value    = fromInput.value;
    document.getElementById('pdf-to').value      = toInput.value;
    document.getElementById('excel-from').value  = fromInput.value;
    document.getElementById('excel-to').value    = toInput.value;
    document.getElementById('signed-from').value = fromInput.value;
    document.getElementById('signed-to').value   = toInput.value;
}
fromInput.addEventListener('change', syncDates);
toInput.addEventListener('change', syncDates);
</script>
@endsection
