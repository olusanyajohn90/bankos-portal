@extends('layouts.portal')
@section('title', 'Dashboard')

@section('content')
@php
    $hour     = now()->hour;
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
    $name     = $customer->first_name ?? explode(' ', $customer->full_name ?? 'Customer')[0];
    $currency = $accounts->first()?->currency ?? 'NGN';

    $kycTier = $customer->kyc_tier ?? $customer->kyc_level ?? 1;
    $tierColors = [1 => ['#d97706','#fffbeb','#fde68a'], 2 => ['#2563eb','#eff6ff','#bfdbfe'], 3 => ['#16a34a','#f0fdf4','#bbf7d0']];
    $tc = $tierColors[$kycTier] ?? $tierColors[1];
@endphp

{{-- Greeting --}}
<div style="margin-bottom:18px">
    <p style="font-size:13px;color:#9ca3af;margin-bottom:2px">{{ $greeting }},</p>
    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
        <h1 style="font-size:20px;font-weight:800;color:#111827">{{ $name }}</h1>
        <span style="font-size:11px;font-weight:700;padding:3px 9px;border-radius:20px;background:{{ $tc[1] }};color:{{ $tc[0] }};border:1px solid {{ $tc[2] }}">
            KYC Tier {{ $kycTier }}
        </span>
        <span style="font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0">
            NDIC Insured
        </span>
    </div>
</div>

{{-- Total balance hero --}}
<div style="background:linear-gradient(135deg,#1e3a8a 0%,#2563eb 60%,#3b82f6 100%);border-radius:18px;padding:26px 24px;color:white;margin-bottom:22px;position:relative;overflow:hidden">
    <div style="position:absolute;top:-40px;right:-40px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,0.05)"></div>
    <div style="position:absolute;bottom:-50px;right:30px;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,0.04)"></div>
    <div style="position:absolute;top:20px;right:80px;width:60px;height:60px;border-radius:50%;background:rgba(255,255,255,0.04)"></div>

    <p style="color:rgba(255,255,255,0.6);font-size:11px;margin-bottom:4px;text-transform:uppercase;letter-spacing:.06em;font-weight:600">Total Available Balance</p>
    <p style="font-size:36px;font-weight:800;letter-spacing:-1.5px;margin-bottom:2px;line-height:1.1">
        {{ $currency }} {{ number_format((float) $totalBalance, 2) }}
    </p>
    <p style="color:rgba(255,255,255,0.5);font-size:12px;margin-bottom:22px">
        Across {{ $accounts->count() }} {{ $accounts->count() === 1 ? 'account' : 'accounts' }}
        @if($savingsCount > 0)
        &middot; {{ $savingsCount }} savings pocket{{ $savingsCount > 1 ? 's' : '' }} ({{ $currency }} {{ number_format((float)$savingsTotal, 2) }})
        @endif
    </p>

    {{-- Hero quick action pills --}}
    <div style="display:flex;gap:10px;flex-wrap:wrap">
        <a href="{{ route('transfer') }}"
           style="display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,0.18);border:1px solid rgba(255,255,255,0.28);color:white;font-size:12px;font-weight:600;padding:8px 14px;border-radius:9px;text-decoration:none">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M7 16V4m0 0L3 8m4-4 4 4"/><path d="M17 8v12m0 0 4-4m-4 4-4-4"/></svg>
            Send Money
        </a>
        <a href="{{ route('bills') }}"
           style="display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.2);color:white;font-size:12px;font-weight:600;padding:8px 14px;border-radius:9px;text-decoration:none">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 12h8M8 8h8M8 16h5"/></svg>
            Pay Bills
        </a>
        <a href="{{ route('savings') }}"
           style="display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.2);color:white;font-size:12px;font-weight:600;padding:8px 14px;border-radius:9px;text-decoration:none">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            Pockets
        </a>
    </div>
</div>

{{-- Quick actions grid --}}
<div style="margin-bottom:26px">

    {{-- Banking row --}}
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px">Banking</p>
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-bottom:18px">

        {{-- Transfer --}}
        <a href="{{ route('transfer') }}" style="display:flex;flex-direction:column;align-items:center;gap:7px;padding:14px 8px;background:white;border:1px solid #f3f4f6;border-radius:12px;text-decoration:none;box-shadow:0 1px 3px rgba(0,0,0,0.04)">
            <div style="width:40px;height:40px;border-radius:50%;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.8"><path d="M7 16V4m0 0L3 8m4-4 4 4"/><path d="M17 8v12m0 0 4-4m-4 4-4-4"/></svg>
            </div>
            <span style="font-size:10px;font-weight:600;color:#374151;text-align:center;line-height:1.3">Transfer</span>
        </a>

        {{-- Pay Bills --}}
        <a href="{{ route('bills') }}" style="display:flex;flex-direction:column;align-items:center;gap:7px;padding:14px 8px;background:white;border:1px solid #f3f4f6;border-radius:12px;text-decoration:none;box-shadow:0 1px 3px rgba(0,0,0,0.04)">
            <div style="width:40px;height:40px;border-radius:50%;background:#fffbeb;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 12h8M8 8h8M8 16h5"/></svg>
            </div>
            <span style="font-size:10px;font-weight:600;color:#374151;text-align:center;line-height:1.3">Pay Bills</span>
        </a>

        {{-- Airtime --}}
        <a href="{{ route('airtime') }}" style="display:flex;flex-direction:column;align-items:center;gap:7px;padding:14px 8px;background:white;border:1px solid #f3f4f6;border-radius:12px;text-decoration:none;box-shadow:0 1px 3px rgba(0,0,0,0.04)">
            <div style="width:40px;height:40px;border-radius:50%;background:#ecfeff;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0891b2" stroke-width="1.8"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
            </div>
            <span style="font-size:10px;font-weight:600;color:#374151;text-align:center;line-height:1.3">Airtime</span>
        </a>

        {{-- Pay Link --}}
        <a href="{{ route('pay-requests') }}" style="display:flex;flex-direction:column;align-items:center;gap:7px;padding:14px 8px;background:white;border:1px solid #f3f4f6;border-radius:12px;text-decoration:none;box-shadow:0 1px 3px rgba(0,0,0,0.04)">
            <div style="width:40px;height:40px;border-radius:50%;background:#f5f3ff;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="1.8"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
            </div>
            <span style="font-size:10px;font-weight:600;color:#374151;text-align:center;line-height:1.3">Pay Link</span>
        </a>
    </div>

    {{-- Save & Invest row --}}
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px">Save &amp; Invest</p>
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-bottom:18px">

        {{-- Pockets --}}
        <a href="{{ route('savings') }}" style="display:flex;flex-direction:column;align-items:center;gap:7px;padding:14px 8px;background:white;border:1px solid #f3f4f6;border-radius:12px;text-decoration:none;box-shadow:0 1px 3px rgba(0,0,0,0.04)">
            <div style="width:40px;height:40px;border-radius:50%;background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="1.8"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </div>
            <span style="font-size:10px;font-weight:600;color:#374151;text-align:center;line-height:1.3">Pockets</span>
        </a>

        {{-- Challenges --}}
        <a href="{{ route('savings-challenges') }}" style="display:flex;flex-direction:column;align-items:center;gap:7px;padding:14px 8px;background:white;border:1px solid #f3f4f6;border-radius:12px;text-decoration:none;box-shadow:0 1px 3px rgba(0,0,0,0.04)">
            <div style="width:40px;height:40px;border-radius:50%;background:#fffbeb;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="1.8"><path d="M6 9H4.5a2.5 2.5 0 010-5H6"/><path d="M18 9h1.5a2.5 2.5 0 000-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><line x1="12" y1="2" x2="12" y2="14"/></svg>
            </div>
            <span style="font-size:10px;font-weight:600;color:#374151;text-align:center;line-height:1.3">Challenges</span>
        </a>

        {{-- Invest --}}
        <a href="{{ route('investments') }}" style="display:flex;flex-direction:column;align-items:center;gap:7px;padding:14px 8px;background:white;border:1px solid #f3f4f6;border-radius:12px;text-decoration:none;box-shadow:0 1px 3px rgba(0,0,0,0.04)">
            <div style="width:40px;height:40px;border-radius:50%;background:#ecfdf5;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="1.8"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/></svg>
            </div>
            <span style="font-size:10px;font-weight:600;color:#374151;text-align:center;line-height:1.3">Invest</span>
        </a>

        {{-- Budget --}}
        <a href="{{ route('budget') }}" style="display:flex;flex-direction:column;align-items:center;gap:7px;padding:14px 8px;background:white;border:1px solid #f3f4f6;border-radius:12px;text-decoration:none;box-shadow:0 1px 3px rgba(0,0,0,0.04)">
            <div style="width:40px;height:40px;border-radius:50%;background:#f5f3ff;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="1.8"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            </div>
            <span style="font-size:10px;font-weight:600;color:#374151;text-align:center;line-height:1.3">Budget</span>
        </a>
    </div>

    {{-- Tools row --}}
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px">Tools</p>
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px">

        {{-- Loans --}}
        <a href="{{ route('loans') }}" style="display:flex;flex-direction:column;align-items:center;gap:7px;padding:14px 8px;background:white;border:1px solid #f3f4f6;border-radius:12px;text-decoration:none;box-shadow:0 1px 3px rgba(0,0,0,0.04)">
            <div style="width:40px;height:40px;border-radius:50%;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.8"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
            </div>
            <span style="font-size:10px;font-weight:600;color:#374151;text-align:center;line-height:1.3">Loans</span>
        </a>

        {{-- Cards --}}
        <a href="{{ route('cards') }}" style="display:flex;flex-direction:column;align-items:center;gap:7px;padding:14px 8px;background:white;border:1px solid #f3f4f6;border-radius:12px;text-decoration:none;box-shadow:0 1px 3px rgba(0,0,0,0.04)">
            <div style="width:40px;height:40px;border-radius:50%;background:#fdf2f8;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#be185d" stroke-width="1.8"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            </div>
            <span style="font-size:10px;font-weight:600;color:#374151;text-align:center;line-height:1.3">Cards</span>
        </a>

        {{-- Credit --}}
        <a href="{{ route('credit-score') }}" style="display:flex;flex-direction:column;align-items:center;gap:7px;padding:14px 8px;background:white;border:1px solid #f3f4f6;border-radius:12px;text-decoration:none;box-shadow:0 1px 3px rgba(0,0,0,0.04)">
            <div style="width:40px;height:40px;border-radius:50%;background:#fffbeb;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="1.8"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
            </div>
            <span style="font-size:10px;font-weight:600;color:#374151;text-align:center;line-height:1.3">Credit</span>
        </a>

        {{-- New A/C --}}
        <a href="{{ route('account-opening') }}" style="display:flex;flex-direction:column;align-items:center;gap:7px;padding:14px 8px;background:white;border:1px solid #f3f4f6;border-radius:12px;text-decoration:none;box-shadow:0 1px 3px rgba(0,0,0,0.04)">
            <div style="width:40px;height:40px;border-radius:50%;background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
            </div>
            <span style="font-size:10px;font-weight:600;color:#374151;text-align:center;line-height:1.3">New A/C</span>
        </a>
    </div>
</div>

{{-- Active loans alert --}}
@if($activeLoans->isNotEmpty())
<div style="margin-bottom:24px">
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px">Active Loans</p>
    <div style="display:flex;flex-direction:column;gap:10px">
        @foreach($activeLoans as $loan)
        @php
            $prog = $loan->principal_amount > 0 ? max(0,min(100,(1-$loan->outstanding_balance/$loan->principal_amount)*100)) : 0;
        @endphp
        <a href="{{ route('loans.show', $loan->id) }}" style="background:white;border:1px solid {{ $loan->status === 'overdue' ? '#fecaca' : '#e5e7eb' }};border-radius:14px;padding:18px;text-decoration:none;display:block;box-shadow:0 1px 3px rgba(0,0,0,0.06)">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px">
                <div>
                    <p style="font-size:13px;font-weight:700;color:#111827">Loan #{{ $loan->loan_number ?? substr($loan->id,0,8) }}</p>
                    <p style="font-size:12px;color:#9ca3af;margin-top:2px">Outstanding: NGN {{ number_format((float)$loan->outstanding_balance,2) }}</p>
                </div>
                <span style="font-size:10px;font-weight:700;padding:4px 9px;border-radius:20px;background:{{ $loan->status==='overdue'?'#fef2f2':'#f0fdf4' }};color:{{ $loan->status==='overdue'?'#dc2626':'#16a34a' }};border:1px solid {{ $loan->status==='overdue'?'#fecaca':'#bbf7d0' }}">
                    {{ strtoupper($loan->status) }}
                </span>
            </div>
            <div style="height:5px;background:#f3f4f6;border-radius:4px;overflow:hidden">
                <div style="height:100%;width:{{ $prog }}%;background:{{ $loan->status==='overdue'?'#f87171':'#2563eb' }};border-radius:4px;transition:width .3s"></div>
            </div>
            <p style="font-size:11px;color:#9ca3af;margin-top:5px">{{ number_format($prog,0) }}% repaid</p>
        </a>
        @endforeach
    </div>
</div>
@endif

{{-- Accounts --}}
<p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px">My Accounts</p>

@if($accounts->isEmpty())
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;text-align:center;padding:48px 24px;box-shadow:0 1px 3px rgba(0,0,0,0.06)">
    <svg style="margin:0 auto 12px;display:block;color:#d1d5db" xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
    <p style="font-size:14px;color:#6b7280">No active accounts found.</p>
</div>
@else
<div style="display:grid;gap:14px;grid-template-columns:repeat(auto-fill,minmax(280px,1fr))">
    @foreach($accounts as $account)
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;padding:22px;box-shadow:0 1px 3px rgba(0,0,0,0.06)">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:16px">
            <div>
                <p style="font-size:14px;font-weight:700;color:#111827">{{ $account->account_name }}</p>
                <div style="display:flex;align-items:center;gap:6px;margin-top:4px">
                    <span style="font-size:11px;font-weight:600;padding:2px 8px;border-radius:20px;background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe">
                        {{ ucfirst(str_replace('_', ' ', $account->type ?? 'savings')) }}
                    </span>
                </div>
            </div>
            <div style="width:38px;height:38px;border-radius:10px;background:#f8fafc;border:1px solid #f3f4f6;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="1.8"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            </div>
        </div>

        <div style="margin-bottom:6px">
            <p style="font-size:11px;color:#9ca3af;margin-bottom:4px;font-weight:500">Available Balance</p>
            <p style="font-size:28px;font-weight:800;color:#111827;letter-spacing:-1px;line-height:1.1">
                {{ $account->currency ?? 'NGN' }} {{ number_format((float) $account->available_balance, 2) }}
            </p>
            @if(abs((float)$account->ledger_balance - (float)$account->available_balance) > 0.001)
            <p style="font-size:11px;color:#9ca3af;margin-top:4px">
                Ledger: {{ $account->currency ?? 'NGN' }} {{ number_format((float) $account->ledger_balance, 2) }}
            </p>
            @endif
        </div>

        <p style="font-family:monospace;font-size:12px;color:#9ca3af;margin-bottom:14px;letter-spacing:.04em">
            •••• •••• {{ substr($account->account_number, -4) }}
        </p>

        <div style="display:flex;gap:8px;border-top:1px solid #f3f4f6;padding-top:14px">
            <a href="{{ route('accounts.show', $account->id) }}" style="display:inline-flex;align-items:center;gap:4px;font-size:12px;font-weight:600;color:#2563eb;text-decoration:none">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                Details
            </a>
            <span style="color:#e5e7eb;font-size:12px">|</span>
            <a href="{{ route('transactions', $account->id) }}" style="display:inline-flex;align-items:center;gap:4px;font-size:12px;font-weight:600;color:#6b7280;text-decoration:none">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                Activity
            </a>
            <span style="color:#e5e7eb;font-size:12px">|</span>
            <a href="{{ route('accounts.statement', $account->id) }}" style="display:inline-flex;align-items:center;gap:4px;font-size:12px;font-weight:600;color:#6b7280;text-decoration:none">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Statement
            </a>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Recent Transactions --}}
@if($recentTxns->isNotEmpty())
<p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-top:30px;margin-bottom:12px">Recent Transactions</p>
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.06)">
    @foreach($recentTxns as $txn)
    @php
        $isCredit = in_array($txn->type, ['deposit', 'credit', 'disbursement', 'interest', 'reversal']);
    @endphp
    <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 18px;border-bottom:1px solid #f9fafb">
        <div style="display:flex;align-items:center;gap:12px">
            <div style="width:38px;height:38px;border-radius:50%;background:{{ $isCredit ? '#f0fdf4' : '#fef2f2' }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="{{ $isCredit ? '#16a34a' : '#dc2626' }}" stroke-width="2.5">
                    @if($isCredit)
                    <line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/>
                    @else
                    <line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/>
                    @endif
                </svg>
            </div>
            <div>
                <p style="font-size:13px;font-weight:600;color:#111827">{{ Str::limit($txn->description ?? ucfirst($txn->type), 38) }}</p>
                <p style="font-size:11px;color:#9ca3af;margin-top:1px">{{ $txn->created_at->diffForHumans() }}</p>
            </div>
        </div>
        <p style="font-size:13px;font-weight:700;color:{{ $isCredit ? '#16a34a' : '#111827' }};white-space:nowrap;margin-left:12px">
            {{ $isCredit ? '+' : '-' }}NGN {{ number_format((float)$txn->amount, 2) }}
        </p>
    </div>
    @endforeach
    <div style="padding:14px 18px;text-align:center">
        @if($accounts->isNotEmpty())
        <a href="{{ route('transactions', $accounts->first()->id) }}" style="font-size:12px;font-weight:600;color:#2563eb;text-decoration:none">View all transactions &rarr;</a>
        @endif
    </div>
</div>
@endif

@endsection
