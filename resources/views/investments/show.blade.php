@extends('layouts.portal')
@section('title', $investment->name)

@section('content')

<div style="display:flex;align-items:center;gap:12px;margin-bottom:28px">
    <a href="{{ route('investments') }}" style="width:34px;height:34px;border-radius:9px;border:1px solid #e5e7eb;background:white;display:flex;align-items:center;justify-content:center;color:#6b7280;flex-shrink:0;text-decoration:none">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#111827">{{ $investment->name }}</h1>
        <p style="font-size:12px;color:#9ca3af;margin-top:2px;font-family:monospace">{{ $investment->reference }}</p>
    </div>
</div>

@php
$statusMap = [
    'active'  => ['color'=>'#15803d', 'bg'=>'#f0fdf4', 'border'=>'#bbf7d0'],
    'matured' => ['color'=>'#1d4ed8', 'bg'=>'#eff6ff', 'border'=>'#bfdbfe'],
    'broken'  => ['color'=>'#991b1b', 'bg'=>'#fef2f2', 'border'=>'#fecaca'],
    'pending' => ['color'=>'#92400e', 'bg'=>'#fffbeb', 'border'=>'#fde68a'],
];
$sm       = $statusMap[$investment->status] ?? ['color'=>'#6b7280','bg'=>'#f9fafb','border'=>'#e5e7eb'];
$progress = $investment->status === 'active'
    ? max(0, min(100, (now()->diffInDays($investment->start_date) / max(1, $investment->duration_days)) * 100))
    : 100;
@endphp

<div style="max-width:580px">

    {{-- Hero card --}}
    <div style="background:linear-gradient(135deg,#14532d,#16a34a);border-radius:14px;padding:26px;color:white;margin-bottom:16px;position:relative;overflow:hidden">
        <div style="position:absolute;top:-25px;right:-25px;width:130px;height:130px;border-radius:50%;background:rgba(255,255,255,0.07)"></div>
        <div style="position:absolute;bottom:-20px;left:-10px;width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,0.04)"></div>

        {{-- Status badge + amount --}}
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:18px">
            <div>
                <p style="font-size:10px;font-weight:700;color:rgba(255,255,255,0.6);text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px">Principal Amount</p>
                <p style="font-size:32px;font-weight:800;letter-spacing:-0.5px;line-height:1">NGN {{ number_format($investment->principal, 2) }}</p>
                <p style="font-size:12px;color:rgba(255,255,255,0.6);margin-top:5px">{{ $investment->interest_rate }}% p.a. &middot; {{ $investment->duration_days }} days</p>
            </div>
            <span style="font-size:11px;font-weight:700;padding:5px 12px;border-radius:20px;background:{{ $sm['bg'] }};color:{{ $sm['color'] }};border:1px solid {{ $sm['border'] }}">
                {{ strtoupper($investment->status) }}
            </span>
        </div>

        {{-- Progress bar --}}
        <div style="margin-bottom:6px">
            <div style="display:flex;justify-content:space-between;margin-bottom:6px">
                <span style="font-size:11px;color:rgba(255,255,255,0.6)">Progress</span>
                <span style="font-size:11px;font-weight:700;color:rgba(255,255,255,0.9)">{{ round($progress) }}%</span>
            </div>
            <div style="height:7px;background:rgba(255,255,255,0.2);border-radius:7px;overflow:hidden">
                <div style="height:100%;width:{{ $progress }}%;background:rgba(255,255,255,0.85);border-radius:7px;transition:width .5s"></div>
            </div>
        </div>

        @if($investment->status === 'active')
        <p style="font-size:11px;color:rgba(255,255,255,0.55);margin-top:6px">{{ $investment->days_remaining }} days remaining until maturity</p>
        @endif
    </div>

    {{-- Key metrics --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px">
        <div style="background:white;border:1px solid #e5e7eb;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:16px">
            <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px">Expected Interest</p>
            <p style="font-size:20px;font-weight:800;color:#16a34a">+NGN {{ number_format($investment->expected_interest, 2) }}</p>
        </div>
        <div style="background:white;border:1px solid #e5e7eb;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:16px">
            <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px">Maturity Amount</p>
            <p style="font-size:20px;font-weight:800;color:#111827">NGN {{ number_format($investment->maturity_amount, 2) }}</p>
        </div>
        @if($investment->status === 'active')
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:16px;grid-column:1/-1">
            <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px">Accrued Interest (Today)</p>
            <p style="font-size:22px;font-weight:800;color:#16a34a">+NGN {{ number_format($investment->accrued_interest, 2) }}</p>
        </div>
        @endif
    </div>

    {{-- Investment details --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:22px;margin-bottom:14px">
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:16px">Investment Details</p>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
            <div>
                <p style="font-size:11px;color:#9ca3af;margin-bottom:4px;text-transform:uppercase;letter-spacing:.04em;font-weight:600">Start Date</p>
                <p style="font-size:14px;font-weight:700;color:#111827">{{ $investment->start_date->format('d M Y') }}</p>
            </div>
            <div>
                <p style="font-size:11px;color:#9ca3af;margin-bottom:4px;text-transform:uppercase;letter-spacing:.04em;font-weight:600">Maturity Date</p>
                <p style="font-size:14px;font-weight:700;color:#111827">{{ $investment->maturity_date->format('d M Y') }}</p>
            </div>
            <div>
                <p style="font-size:11px;color:#9ca3af;margin-bottom:4px;text-transform:uppercase;letter-spacing:.04em;font-weight:600">Interest Rate</p>
                <p style="font-size:14px;font-weight:700;color:#111827">{{ $investment->interest_rate }}% p.a.</p>
            </div>
            <div>
                <p style="font-size:11px;color:#9ca3af;margin-bottom:4px;text-transform:uppercase;letter-spacing:.04em;font-weight:600">Duration</p>
                <p style="font-size:14px;font-weight:700;color:#111827">{{ $investment->duration_days }} days</p>
            </div>
        </div>
    </div>

    {{-- Early liquidation --}}
    @if($investment->status === 'active')
    <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:14px;padding:20px">
        <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:14px">
            <div style="width:38px;height:38px;border-radius:10px;background:#fee2e2;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            <div>
                <p style="font-size:13px;font-weight:700;color:#991b1b;margin-bottom:4px">Early Liquidation</p>
                <p style="font-size:12px;color:#dc2626;line-height:1.6">
                    Withdrawing early incurs a 10% penalty on accrued interest.
                    You would receive <strong>NGN {{ number_format($investment->principal + $investment->accrued_interest * 0.9, 2) }}</strong> today.
                </p>
            </div>
        </div>
        <form method="POST" action="{{ route('investments.liquidate', $investment->id) }}"
              onsubmit="return confirm('Are you sure? A 10% penalty on accrued interest will be applied.')">
            @csrf
            <button type="submit"
                    style="background:#dc2626;color:white;font-size:13px;font-weight:700;padding:10px 20px;border-radius:9px;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:7px">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                Liquidate Now
            </button>
        </form>
    </div>
    @endif

    {{-- Penalty notice --}}
    @if($investment->penalty_amount > 0)
    <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:10px;padding:14px 16px;display:flex;align-items:center;gap:10px;margin-top:12px">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#ea580c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <p style="font-size:12px;color:#c2410c;font-weight:600">Early withdrawal penalty applied: NGN {{ number_format($investment->penalty_amount, 2) }}</p>
    </div>
    @endif

</div>
@endsection
