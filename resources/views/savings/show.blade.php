@extends('layouts.portal')
@section('title', $pocket->name)

@section('content')

{{-- Back + title --}}
<div style="display:flex;align-items:center;gap:12px;margin-bottom:28px">
    <a href="{{ route('savings') }}"
       style="width:36px;height:36px;border-radius:10px;background:white;border:1px solid #e5e7eb;display:flex;align-items:center;justify-content:center;color:#6b7280;text-decoration:none;flex-shrink:0">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#111827;margin-bottom:2px">{{ $pocket->name }}</h1>
        <p style="font-size:13px;color:#6b7280">{{ ucfirst($pocket->type) }} pocket</p>
    </div>
</div>

@foreach(['success','error'] as $key)
@if(session($key))
<div style="margin-bottom:16px;padding:12px 16px;background:{{ $key==='success'?'#f0fdf4':'#fef2f2' }};border:1px solid {{ $key==='success'?'#bbf7d0':'#fecaca' }};border-radius:10px;color:{{ $key==='success'?'#15803d':'#991b1b' }};font-size:13px;font-weight:600">
    {{ session($key) }}
</div>
@endif
@endforeach

@if($errors->any())
<div style="margin-bottom:16px;padding:12px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;color:#991b1b;font-size:13px;font-weight:600">{{ $errors->first() }}</div>
@endif

{{-- Pocket hero card --}}
@php $pct = $pocket->progressPercent(); @endphp
<div style="background:linear-gradient(135deg,#1e3a8a 0%,#2563eb 100%);border-radius:16px;padding:26px 28px;color:white;margin-bottom:20px;position:relative;overflow:hidden">
    <div style="position:absolute;top:-24px;right:-24px;width:130px;height:130px;border-radius:50%;background:rgba(255,255,255,0.06)"></div>
    <div style="position:absolute;bottom:-32px;right:60px;width:90px;height:90px;border-radius:50%;background:rgba(255,255,255,0.05)"></div>

    <div style="position:relative">
        <p style="font-size:11px;font-weight:700;color:rgba(191,219,254,0.85);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Current Balance</p>
        <p style="font-size:34px;font-weight:800;letter-spacing:-1px;margin-bottom:18px">NGN {{ number_format($pocket->balance, 2) }}</p>

        @if($pocket->target_amount)
        <div style="background:rgba(255,255,255,0.12);border-radius:12px;padding:14px 16px">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                <span style="font-size:12px;font-weight:600;color:rgba(191,219,254,0.85)">Progress to goal</span>
                <span style="font-size:13px;font-weight:800">{{ $pct }}%</span>
            </div>
            <div style="height:6px;border-radius:3px;background:rgba(255,255,255,0.2);overflow:hidden;margin-bottom:8px">
                <div style="height:100%;width:{{ $pct }}%;background:{{ $pct >= 100 ? '#4ade80' : 'rgba(255,255,255,0.85)' }};border-radius:3px"></div>
            </div>
            <div style="display:flex;justify-content:space-between">
                <span style="font-size:12px;color:rgba(191,219,254,0.75)">NGN {{ number_format($pocket->balance, 0) }}</span>
                <span style="font-size:12px;color:rgba(191,219,254,0.75)">NGN {{ number_format($pocket->target_amount, 0) }} goal</span>
            </div>
        </div>
        @endif

        @if($pocket->target_date)
        <p style="font-size:12px;color:rgba(191,219,254,0.75);margin-top:12px">Target date: {{ $pocket->target_date->format('d F Y') }}</p>
        @endif
    </div>
</div>

{{-- Deposit & Withdraw --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:20px">
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:20px">
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:14px">Add to Pocket</p>
        <form method="POST" action="{{ route('savings.deposit', $pocket->id) }}">
            @csrf
            <input type="number" name="amount" placeholder="Amount (NGN)" min="1" step="0.01"
                   style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;outline:none;box-sizing:border-box;margin-bottom:10px">
            <button type="submit"
                    style="width:100%;background:#2563eb;color:white;font-size:13px;font-weight:700;padding:10px;border-radius:9px;border:none;cursor:pointer">
                Deposit
            </button>
        </form>
    </div>

    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:20px">
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:14px">Withdraw Funds</p>
        <form method="POST" action="{{ route('savings.withdraw', $pocket->id) }}">
            @csrf
            <input type="number" name="amount" placeholder="Amount (NGN)" min="1" max="{{ $pocket->balance }}" step="0.01"
                   style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;outline:none;box-sizing:border-box;margin-bottom:10px">
            <button type="submit"
                    style="width:100%;background:white;color:#374151;font-size:13px;font-weight:700;padding:10px;border-radius:9px;border:1px solid #d1d5db;cursor:pointer">
                Withdraw
            </button>
        </form>
    </div>
</div>

{{-- Transaction history --}}
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);overflow:hidden;margin-bottom:20px">
    <div style="padding:20px 20px 0">
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:16px">Transaction History</p>
    </div>
    @if($history->isEmpty())
    <p style="text-align:center;padding:36px;color:#9ca3af;font-size:13px">No transactions yet.</p>
    @else
    <div>
        @foreach($history as $h)
        @php $isDep = in_array($h->type, ['deposit','interest','round_up']); @endphp
        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-top:1px solid #f3f4f6">
            <div style="display:flex;align-items:center;gap:12px">
                <div style="width:36px;height:36px;border-radius:10px;background:{{ $isDep ? '#f0fdf4' : '#fef2f2' }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    @if($isDep)
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="2.5"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
                    @else
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>
                    @endif
                </div>
                <div>
                    <p style="font-size:13px;font-weight:700;color:#111827">{{ ucfirst(str_replace('_', ' ', $h->type)) }}</p>
                    <p style="font-size:12px;color:#9ca3af">{{ $h->created_at->format('d M Y, H:i') }}</p>
                </div>
            </div>
            <div style="text-align:right">
                <p style="font-size:14px;font-weight:800;color:{{ $isDep ? '#15803d' : '#dc2626' }}">
                    {{ $isDep ? '+' : '−' }}NGN {{ number_format($h->amount, 2) }}
                </p>
                <p style="font-size:11px;color:#9ca3af">Bal: NGN {{ number_format($h->balance_after, 2) }}</p>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- Close pocket --}}
<form method="POST" action="{{ route('savings.destroy', $pocket->id) }}"
      onsubmit="return confirm('Close pocket? Any balance will be returned to your linked account.')">
    @csrf @method('DELETE')
    <button type="submit"
            style="background:none;border:1px solid #fecaca;color:#dc2626;font-size:13px;font-weight:700;padding:10px 20px;border-radius:9px;cursor:pointer">
        Close Pocket
    </button>
</form>

@endsection
