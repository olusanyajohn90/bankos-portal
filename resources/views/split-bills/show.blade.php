@extends('layouts.portal')
@section('title', 'Split Bill — ' . $bill->title)

@section('content')
@php
    $pct = $bill->total_amount > 0 ? min(100, round(($bill->collected_amount / $bill->total_amount) * 100)) : 0;
    $statusMap = [
        'open'      => ['bg'=>'#eff6ff','text'=>'#2563eb'],
        'completed' => ['bg'=>'#f0fdf4','text'=>'#15803d'],
        'cancelled' => ['bg'=>'#f9fafb','text'=>'#6b7280'],
    ];
    [$sbg, $scol] = $statusMap[$bill->status] ?? ['#f9fafb','#6b7280'];
@endphp

<div style="display:flex;align-items:center;gap:12px;margin-bottom:28px">
    <a href="{{ route('split-bills') }}"
       style="display:flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:9px;border:1px solid #e5e7eb;color:#6b7280;text-decoration:none;flex-shrink:0;background:white">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div style="flex:1;min-width:0">
        <h1 style="font-size:20px;font-weight:800;color:#111827;margin:0 0 3px">{{ $bill->title }}</h1>
        <p style="font-size:11px;color:#9ca3af;font-family:monospace;margin:0">{{ $bill->reference }}</p>
    </div>
    <span style="font-size:10px;font-weight:800;padding:5px 12px;border-radius:99px;background:{{ $sbg }};color:{{ $scol }};letter-spacing:.04em;flex-shrink:0">
        {{ strtoupper($bill->status) }}
    </span>
</div>

@if(session('success'))
<div style="margin-bottom:18px;padding:13px 16px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;color:#15803d;font-size:13px;font-weight:500">
    {{ session('success') }}
</div>
@endif
@if($errors->any())
<div style="margin-bottom:18px;padding:13px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;color:#991b1b;font-size:13px">
    @foreach($errors->all() as $e)<p style="margin:0">{{ $e }}</p>@endforeach
</div>
@endif

{{-- Summary Cards --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:18px">
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:18px">
        <p style="font-size:11px;color:#9ca3af;margin:0 0 6px;text-transform:uppercase;letter-spacing:.05em;font-weight:600">Total Bill</p>
        <p style="font-size:20px;font-weight:800;color:#111827;margin:0">NGN {{ number_format($bill->total_amount, 2) }}</p>
    </div>
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:18px">
        <p style="font-size:11px;color:#9ca3af;margin:0 0 6px;text-transform:uppercase;letter-spacing:.05em;font-weight:600">Collected</p>
        <p style="font-size:20px;font-weight:800;color:#15803d;margin:0 0 8px">NGN {{ number_format($bill->collected_amount, 2) }}</p>
        <div style="background:#f3f4f6;border-radius:99px;height:4px;overflow:hidden">
            <div style="background:#15803d;height:100%;width:{{ $pct }}%;border-radius:99px"></div>
        </div>
        <p style="font-size:11px;color:#9ca3af;margin:5px 0 0">{{ $pct }}% collected</p>
    </div>
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:18px">
        <p style="font-size:11px;color:#9ca3af;margin:0 0 6px;text-transform:uppercase;letter-spacing:.05em;font-weight:600">Per Person</p>
        <p style="font-size:20px;font-weight:800;color:#2563eb;margin:0 0 4px">NGN {{ number_format($bill->per_person_amount, 2) }}</p>
        <p style="font-size:11px;color:#9ca3af;margin:0">{{ $bill->paid_count }} / {{ $bill->participant_count }} paid</p>
    </div>
</div>

{{-- Account + Description --}}
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:18px 20px;margin-bottom:18px">
    @if($account)
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:{{ $bill->description || $bill->expires_at ? '10px' : '0' }}">
        <span style="font-size:12px;color:#9ca3af">Receiving account:</span>
        <span style="font-size:13px;font-weight:700;color:#111827">{{ $account->account_name }}</span>
        <span style="font-size:13px;color:#6b7280">&mdash; {{ $account->account_number }}</span>
    </div>
    @endif
    @if($bill->description)
    <p style="font-size:13px;color:#6b7280;margin:0 0 6px">{{ $bill->description }}</p>
    @endif
    @if($bill->expires_at)
    <p style="font-size:11px;color:#9ca3af;margin:0">Expires: {{ \Carbon\Carbon::parse($bill->expires_at)->format('d M Y') }}</p>
    @endif
</div>

{{-- Participants --}}
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);overflow:hidden;margin-bottom:18px">
    <div style="padding:16px 20px;border-bottom:1px solid #f3f4f6">
        <p style="font-size:13px;font-weight:700;color:#111827;margin:0">Participants</p>
    </div>
    @foreach($participants as $p)
    @php
        $pColors = [
            'paid'    => ['bg'=>'#f0fdf4','text'=>'#15803d'],
            'pending' => ['bg'=>'#fffbeb','text'=>'#92400e'],
            'skipped' => ['bg'=>'#f9fafb','text'=>'#6b7280'],
        ];
        [$pbg, $pcol] = $pColors[$p->status] ?? ['#f9fafb','#6b7280'];
    @endphp
    <div style="padding:16px 20px;border-bottom:1px solid #f9fafb;display:flex;align-items:center;justify-content:space-between;gap:12px">
        <div style="flex:1;min-width:0">
            <p style="font-size:13px;font-weight:700;color:#111827;margin:0 0 3px">{{ $p->name }}</p>
            <p style="font-size:11px;color:#9ca3af;margin:0">
                @if($p->phone){{ $p->phone }}@endif
                @if($p->phone && $p->email) &middot; @endif
                @if($p->email){{ $p->email }}@endif
                @if($p->paid_at) &middot; Paid {{ \Carbon\Carbon::parse($p->paid_at)->format('d M Y') }}@endif
                @if($p->reference) &middot; <span style="font-family:monospace">{{ $p->reference }}</span>@endif
            </p>
        </div>
        <div style="display:flex;align-items:center;gap:10px;flex-shrink:0">
            <span style="font-size:14px;font-weight:800;color:#111827">NGN {{ number_format($p->amount, 2) }}</span>
            <span style="font-size:10px;font-weight:800;padding:4px 9px;border-radius:99px;background:{{ $pbg }};color:{{ $pcol }};letter-spacing:.03em">
                {{ strtoupper($p->status) }}
            </span>
            @if($bill->status === 'open' && $p->status === 'pending')
            <form method="POST" action="{{ route('split-bills.mark-paid', [$bill->id, $p->id]) }}" style="margin:0">
                @csrf
                <button type="submit"
                        style="background:#2563eb;color:white;font-size:11px;font-weight:700;padding:6px 12px;border-radius:7px;border:none;cursor:pointer">
                    Mark Paid
                </button>
            </form>
            @endif
        </div>
    </div>
    @endforeach
</div>

@if($bill->status === 'open')
<div style="display:flex;justify-content:flex-end">
    <form method="POST" action="{{ route('split-bills.cancel', $bill->id) }}" style="display:inline">
        @csrf
        @method('DELETE')
        <button type="submit" onclick="return confirm('Cancel this split bill? This cannot be undone.')"
                style="background:white;border:1px solid #fecaca;color:#dc2626;font-size:13px;font-weight:700;padding:10px 20px;border-radius:10px;cursor:pointer">
            Cancel Split Bill
        </button>
    </form>
</div>
@endif
@endsection
