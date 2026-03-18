@extends('layouts.portal')
@section('title', 'Investments')

@section('content')

{{-- Page header --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px">
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#111827;margin-bottom:3px">Investments</h1>
        <p style="font-size:13px;color:#6b7280">Fixed deposits &middot; Earn guaranteed returns on your money.</p>
    </div>
    <a href="{{ route('investments.create') }}"
       style="display:inline-flex;align-items:center;gap:7px;background:#15803d;color:white;font-size:13px;font-weight:700;padding:11px 20px;border-radius:10px;text-decoration:none">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Investment
    </a>
</div>

{{-- Portfolio summary hero --}}
<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:28px">
    <div style="background:linear-gradient(135deg,#166534 0%,#15803d 100%);border-radius:14px;padding:22px;color:white;position:relative;overflow:hidden">
        <div style="position:absolute;top:-16px;right:-16px;width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,0.08)"></div>
        <p style="font-size:11px;font-weight:700;color:rgba(187,247,208,0.85);text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px">Total Invested</p>
        <p style="font-size:22px;font-weight:800;letter-spacing:-0.5px">NGN {{ number_format($totalInvested, 0) }}</p>
    </div>
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:22px">
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px">Total Returns</p>
        <p style="font-size:22px;font-weight:800;color:#15803d;letter-spacing:-0.5px">NGN {{ number_format($totalEarned, 0) }}</p>
    </div>
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:22px">
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px">Active Positions</p>
        <p style="font-size:22px;font-weight:800;color:#111827;letter-spacing:-0.5px">{{ $investments->where('status','active')->count() }}</p>
    </div>
</div>

@if($investments->isEmpty())
{{-- Empty state --}}
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:72px 24px;text-align:center">
    <div style="width:64px;height:64px;border-radius:16px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;margin:0 auto 20px">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="1.5"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
    </div>
    <p style="font-size:16px;font-weight:800;color:#111827;margin-bottom:6px">No investments yet</p>
    <p style="font-size:13px;color:#6b7280;max-width:340px;margin:0 auto 24px;line-height:1.6">Start earning guaranteed interest on your idle funds with a fixed deposit.</p>
    <a href="{{ route('investments.create') }}"
       style="display:inline-flex;align-items:center;gap:7px;background:#15803d;color:white;font-size:13px;font-weight:700;padding:11px 20px;border-radius:10px;text-decoration:none">
        Start Investing
    </a>
</div>
@else
<p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px">Portfolio</p>
<div style="display:flex;flex-direction:column;gap:12px">
    @foreach($investments as $inv)
    @php
        $progress = $inv->status === 'active'
            ? max(0, min(100, (now()->diffInDays($inv->start_date) / $inv->duration_days) * 100))
            : 100;
        $statusColors = [
            'active'   => ['#15803d', '#f0fdf4', '#bbf7d0'],
            'matured'  => ['#2563eb', '#eff6ff', '#bfdbfe'],
            'broken'   => ['#dc2626', '#fef2f2', '#fecaca'],
            'pending'  => ['#d97706', '#fffbeb', '#fde68a'],
        ];
        $sc = $statusColors[$inv->status] ?? ['#6b7280', '#f9fafb', '#e5e7eb'];
    @endphp
    <a href="{{ route('investments.show', $inv->id) }}"
       style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:22px;text-decoration:none;display:block">

        {{-- Top row --}}
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:14px">
            <div>
                <p style="font-size:16px;font-weight:800;color:#111827;margin-bottom:4px">{{ $inv->name }}</p>
                <p style="font-size:12px;color:#6b7280">{{ $inv->duration_days }} days &middot; {{ $inv->interest_rate }}% p.a.</p>
            </div>
            <div style="text-align:right">
                <p style="font-size:20px;font-weight:800;color:#111827;letter-spacing:-0.5px;margin-bottom:5px">NGN {{ number_format($inv->principal, 0) }}</p>
                <span style="font-size:11px;font-weight:700;padding:3px 9px;border-radius:20px;background:{{ $sc[1] }};color:{{ $sc[0] }};border:1px solid {{ $sc[2] }}">
                    {{ strtoupper($inv->status) }}
                </span>
            </div>
        </div>

        @if($inv->status === 'active')
        {{-- Maturity progress --}}
        <div>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
                <p style="font-size:12px;color:#6b7280">Matures {{ $inv->maturity_date->format('d M Y') }}</p>
                <p style="font-size:12px;font-weight:700;color:#6b7280">{{ $inv->days_remaining }} days left</p>
            </div>
            <div style="height:6px;border-radius:3px;background:#f3f4f6;overflow:hidden;margin-bottom:8px">
                <div style="height:100%;width:{{ $progress }}%;background:#15803d;border-radius:3px"></div>
            </div>
            <p style="font-size:12px;font-weight:700;color:#15803d">+NGN {{ number_format($inv->accrued_interest, 2) }} accrued interest</p>
        </div>
        @elseif($inv->status === 'matured')
        <div style="display:flex;align-items:center;gap:8px;background:#f0fdf4;border-radius:9px;padding:10px 14px">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            <p style="font-size:12px;font-weight:700;color:#15803d">Matured {{ $inv->matured_at?->format('d M Y') }} &middot; NGN {{ number_format($inv->expected_interest, 2) }} earned</p>
        </div>
        @endif
    </a>
    @endforeach
</div>
@endif

@endsection
