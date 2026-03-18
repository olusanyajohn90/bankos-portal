@extends('layouts.portal')
@section('title', 'Open New Account')

@section('content')

{{-- Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px">
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#111827">Open a New Account</h1>
        <p style="font-size:12px;color:#9ca3af;margin-top:3px">Instantly add another account &mdash; no branch visit needed</p>
    </div>
</div>

@if(session('success'))
<div style="margin-bottom:18px;padding:12px 16px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;display:flex;align-items:center;gap:10px">
    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
    <p style="font-size:13px;color:#15803d">{{ session('success') }}</p>
</div>
@endif

@if($errors->any())
<div style="margin-bottom:18px;padding:12px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px">
    @foreach($errors->all() as $err)
    <p style="font-size:13px;color:#991b1b;margin-bottom:2px">{{ $err }}</p>
    @endforeach
</div>
@endif

{{-- Existing accounts strip --}}
@if($existing->isNotEmpty())
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:18px 22px;margin-bottom:22px">
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px">Your Existing Accounts</p>
    <div style="display:flex;flex-wrap:wrap;gap:8px">
        @foreach($existing as $acct)
        <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;padding:8px 14px;display:flex;align-items:center;gap:10px">
            <div style="width:8px;height:8px;border-radius:50%;background:#16a34a;flex-shrink:0"></div>
            <div>
                <p style="font-size:12px;font-weight:700;color:#111827">{{ ucfirst($acct->type) }}</p>
                <p style="font-size:10px;color:#9ca3af;font-family:monospace">{{ $acct->account_number }}</p>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Account type selection --}}
<form method="POST" action="{{ route('account-opening.store') }}" id="acct-form">
    @csrf
    <input type="hidden" name="account_type" id="selected-type" value="">

    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:14px">Select Account Type</p>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:22px">
        @foreach($accountTypes as $at)
        @php $alreadyHas = $existing->where('type', $at['type'])->count() > 0; @endphp

        @php
        $typeIcons = [
            'savings'     => '<path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>',
            'current'     => '<rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/>',
            'domiciliary' => '<circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>',
            'kids'        => '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>',
        ];
        $iconPath = $typeIcons[$at['type']] ?? '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>';
        @endphp

        <div onclick="{{ $alreadyHas ? '' : "selectType('{$at['type']}')" }}"
             id="type-card-{{ $at['type'] }}"
             style="background:white;border:2px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:22px;cursor:{{ $alreadyHas ? 'default' : 'pointer' }};opacity:{{ $alreadyHas ? '.55' : '1' }};transition:all .2s;position:relative">

            @if($alreadyHas)
            <span style="position:absolute;top:12px;right:12px;font-size:10px;font-weight:700;background:#f0fdf4;color:#16a34a;padding:3px 9px;border-radius:20px;border:1px solid #bbf7d0">ACTIVE</span>
            @endif

            {{-- Icon --}}
            <div style="width:46px;height:46px;border-radius:12px;background:#eff6ff;display:flex;align-items:center;justify-content:center;margin-bottom:14px">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $iconPath !!}</svg>
            </div>

            <p style="font-size:15px;font-weight:800;color:#111827;margin-bottom:5px">{{ $at['label'] }}</p>
            <p style="font-size:12px;color:#6b7280;line-height:1.6;margin-bottom:14px">{{ $at['desc'] }}</p>

            <div style="display:flex;justify-content:space-between;align-items:center;padding-top:12px;border-top:1px solid #f3f4f6">
                <div style="display:flex;align-items:center;gap:5px">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    <span style="font-size:11px;color:#9ca3af">Min: {{ $at['min_bal'] > 0 ? 'NGN '.number_format($at['min_bal'], 0) : 'None' }}</span>
                </div>
                @if($at['interest'])
                <span style="font-size:11px;font-weight:700;color:#16a34a">{{ $at['interest'] }}</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    {{-- Confirm section --}}
    <div id="confirm-section" style="display:none;max-width:520px">

        {{-- Selected type banner --}}
        <div id="confirm-card" style="background:linear-gradient(135deg,#1e40af,#2563eb);border-radius:14px;padding:22px;color:white;margin-bottom:16px;text-align:center;position:relative;overflow:hidden">
            <div style="position:absolute;top:-20px;right:-20px;width:100px;height:100px;border-radius:50%;background:rgba(255,255,255,0.07)"></div>
            <div style="width:44px;height:44px;border-radius:12px;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;margin:0 auto 12px">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <p style="font-size:11px;color:rgba(255,255,255,0.65);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Opening</p>
            <p id="confirm-label" style="font-size:22px;font-weight:800"></p>
            <p style="font-size:12px;color:rgba(255,255,255,0.6);margin-top:4px">Your account will be created instantly</p>
        </div>

        {{-- Purpose field --}}
        <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:22px;margin-bottom:16px">
            <div style="margin-bottom:16px">
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:7px">
                    Purpose <span style="font-size:11px;font-weight:400;color:#9ca3af">(optional)</span>
                </label>
                <input type="text" name="purpose" placeholder="e.g. Business transactions, salary account..."
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none"
                       onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#d1d5db'">
            </div>

            {{-- Instant info note --}}
            <div style="padding:12px 14px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:9px;display:flex;align-items:center;gap:10px">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <p style="font-size:12px;color:#15803d">Account number will be generated instantly after submission.</p>
            </div>
        </div>

        <button type="submit"
                style="width:100%;background:#15803d;color:white;font-size:13px;font-weight:700;padding:13px 20px;border-radius:10px;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px"
                onmouseover="this.style.background='#166534'" onmouseout="this.style.background='#15803d'">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
            Open Account Now
        </button>
    </div>

</form>

<script>
const typeLabels = {
    savings:     'Savings Account',
    current:     'Current Account',
    domiciliary: 'Domiciliary Account',
    kids:        'Kids Account',
};

function selectType(type) {
    // Reset all cards
    document.querySelectorAll('[id^="type-card-"]').forEach(function(el) {
        el.style.borderColor = '#e5e7eb';
        el.style.background  = 'white';
        el.style.boxShadow   = '0 1px 3px rgba(0,0,0,0.06)';
    });

    // Highlight selected
    var card = document.getElementById('type-card-' + type);
    card.style.borderColor = '#2563eb';
    card.style.background  = '#f8fbff';
    card.style.boxShadow   = '0 0 0 3px rgba(37,99,235,0.12)';

    document.getElementById('selected-type').value  = type;
    document.getElementById('confirm-label').textContent = typeLabels[type] || type;
    document.getElementById('confirm-section').style.display = 'block';
    document.getElementById('confirm-section').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}
</script>
@endsection
