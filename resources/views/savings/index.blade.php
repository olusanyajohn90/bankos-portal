@extends('layouts.portal')
@section('title', 'Savings Pockets')

@section('content')

{{-- Page header --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px">
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#111827;margin-bottom:3px">Savings Pockets</h1>
        <p style="font-size:13px;color:#6b7280">Set goals, save automatically, and grow your money over time.</p>
    </div>
    <a href="{{ route('savings.create') }}"
       style="display:inline-flex;align-items:center;gap:7px;background:#2563eb;color:white;font-size:13px;font-weight:700;padding:11px 20px;border-radius:10px;text-decoration:none">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Pocket
    </a>
</div>

@if(session('success'))
<div style="margin-bottom:18px;padding:12px 16px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;color:#15803d;font-size:13px;font-weight:600">
    {{ session('success') }}
</div>
@endif

{{-- Summary hero --}}
<div style="background:linear-gradient(135deg,#1e3a8a 0%,#2563eb 100%);border-radius:16px;padding:24px 28px;color:white;margin-bottom:24px;position:relative;overflow:hidden">
    <div style="position:absolute;top:-24px;right:-24px;width:130px;height:130px;border-radius:50%;background:rgba(255,255,255,0.06)"></div>
    <div style="position:absolute;bottom:-32px;right:60px;width:90px;height:90px;border-radius:50%;background:rgba(255,255,255,0.05)"></div>
    <div style="display:flex;align-items:center;justify-content:space-between;position:relative">
        <div>
            <p style="font-size:11px;font-weight:700;color:rgba(191,219,254,0.85);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Total Savings Balance</p>
            <p style="font-size:32px;font-weight:800;letter-spacing:-1px;margin-bottom:4px">NGN {{ number_format($totalSaved, 2) }}</p>
            <p style="font-size:13px;color:rgba(191,219,254,0.7)">Across all pockets</p>
        </div>
        <div style="background:rgba(255,255,255,0.12);border-radius:14px;padding:16px 20px;text-align:center;flex-shrink:0">
            <p style="font-size:11px;font-weight:700;color:rgba(191,219,254,0.8);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Pockets</p>
            <p style="font-size:28px;font-weight:800">{{ $pockets->count() }}</p>
        </div>
    </div>
</div>

@if($pockets->isEmpty())
{{-- Empty state --}}
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:72px 24px;text-align:center">
    <div style="width:64px;height:64px;border-radius:16px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;margin:0 auto 20px">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.5"><path d="M20 12V22H4V12"/><path d="M22 7H2v5h20V7z"/><path d="M12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 010-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 000-5C13 2 12 7 12 7z"/></svg>
    </div>
    <p style="font-size:16px;font-weight:800;color:#111827;margin-bottom:6px">No savings pockets yet</p>
    <p style="font-size:13px;color:#6b7280;max-width:360px;margin:0 auto 24px;line-height:1.6">Create a pocket for a goal — school fees, travel, emergency fund — and start saving today.</p>
    <a href="{{ route('savings.create') }}"
       style="display:inline-flex;align-items:center;gap:7px;background:#2563eb;color:white;font-size:13px;font-weight:700;padding:11px 20px;border-radius:10px;text-decoration:none">
        Create First Pocket
    </a>
</div>
@else
<p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px">Your Pockets</p>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
    @foreach($pockets as $pocket)
    @php $pct = $pocket->progressPercent(); @endphp
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:22px;position:relative">

        {{-- Pocket header --}}
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:16px">
            <div>
                <p style="font-size:15px;font-weight:800;color:#111827;margin-bottom:3px">{{ $pocket->name }}</p>
                <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em">{{ ucfirst($pocket->type) }}</p>
            </div>
            <a href="{{ route('savings.show', $pocket->id) }}"
               style="font-size:12px;font-weight:700;color:#2563eb;text-decoration:none;background:#eff6ff;padding:5px 10px;border-radius:8px">Manage</a>
        </div>

        {{-- Balance --}}
        <p style="font-size:24px;font-weight:800;color:#111827;letter-spacing:-0.5px;margin-bottom:4px">NGN {{ number_format($pocket->balance, 2) }}</p>

        @if($pocket->target_amount)
        <p style="font-size:12px;color:#9ca3af;margin-bottom:10px">of NGN {{ number_format($pocket->target_amount, 0) }} goal</p>
        <div style="height:6px;border-radius:3px;background:#f3f4f6;overflow:hidden;margin-bottom:6px">
            <div style="height:100%;width:{{ $pct }}%;background:{{ $pct >= 100 ? '#15803d' : '#2563eb' }};border-radius:3px"></div>
        </div>
        <p style="font-size:12px;font-weight:700;color:{{ $pct >= 100 ? '#15803d' : '#6b7280' }}">{{ $pct }}%{{ $pct >= 100 ? ' — Goal reached!' : ' complete' }}</p>
        @else
        <p style="font-size:12px;color:#9ca3af">No target set</p>
        @endif

        @if($pocket->target_date)
        <p style="font-size:12px;color:#6b7280;margin-top:8px">Target: {{ $pocket->target_date->format('d M Y') }}</p>
        @endif
    </div>
    @endforeach
</div>
@endif

@endsection
