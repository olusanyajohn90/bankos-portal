@extends('layouts.portal')
@section('title', 'Standing Orders')

@section('content')

{{-- Page Header --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:28px;gap:16px">
    <div>
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Payments</p>
        <h1 style="font-size:20px;font-weight:800;color:#111827;margin-bottom:4px">Standing Orders</h1>
        <p style="font-size:13px;color:#6b7280">Automated recurring transfers executed on your schedule</p>
    </div>
    <a href="{{ route('standing-orders.create') }}"
       style="display:inline-flex;align-items:center;gap:8px;background:#2563eb;color:white;font-size:13px;font-weight:700;padding:11px 20px;border-radius:10px;border:none;cursor:pointer;text-decoration:none;white-space:nowrap;flex-shrink:0">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Standing Order
    </a>
</div>

{{-- Alerts --}}
@if(session('success'))
<div style="margin-bottom:20px;padding:13px 16px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;color:#15803d;font-size:13px;font-weight:500">
    {{ session('success') }}
</div>
@endif

@if($orders->isEmpty())

{{-- Empty state --}}
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:64px 24px;text-align:center">
    <div style="width:56px;height:56px;border-radius:14px;background:#eff6ff;display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px">
        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.8"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
    </div>
    <p style="font-size:15px;font-weight:700;color:#111827;margin-bottom:6px">No standing orders yet</p>
    <p style="font-size:13px;color:#6b7280;margin-bottom:24px;max-width:380px;margin-left:auto;margin-right:auto">
        Set up automated recurring transfers — weekly savings, monthly rent, regular vendor payments.
    </p>
    <a href="{{ route('standing-orders.create') }}"
       style="display:inline-flex;align-items:center;gap:8px;background:#2563eb;color:white;font-size:13px;font-weight:700;padding:11px 24px;border-radius:10px;text-decoration:none">
        Set Up First Order
    </a>
</div>

@else

<div style="display:flex;flex-direction:column;gap:12px">
    @foreach($orders as $order)
    @php
        $freq = ucfirst($order->frequency);
        $freqColors = [
            'daily'   => ['#eff6ff','#1d4ed8'],
            'weekly'  => ['#f0fdf4','#15803d'],
            'monthly' => ['#faf5ff','#7c3aed'],
        ];
        [$fbg, $ftxt] = $freqColors[strtolower($order->frequency)] ?? ['#f3f4f6','#374151'];

        $statusConfig = [
            'active'    => ['dot' => '#16a34a', 'label' => 'Active',    'bg' => '#f0fdf4', 'txt' => '#15803d'],
            'paused'    => ['dot' => '#d97706', 'label' => 'Paused',    'bg' => '#fef9c3', 'txt' => '#854d0e'],
            'cancelled' => ['dot' => '#9ca3af', 'label' => 'Cancelled', 'bg' => '#f3f4f6', 'txt' => '#6b7280'],
        ];
        $sc = $statusConfig[$order->status] ?? $statusConfig['cancelled'];
    @endphp
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:20px">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:14px">
            <div style="min-width:0">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:5px;flex-wrap:wrap">
                    <p style="font-size:14px;font-weight:800;color:#111827">{{ $order->narration }}</p>
                    {{-- Frequency badge --}}
                    <span style="display:inline-block;font-size:10px;font-weight:700;padding:3px 9px;border-radius:99px;background:{{ $fbg }};color:{{ $ftxt }};letter-spacing:.04em;text-transform:uppercase">
                        {{ $freq }}
                    </span>
                    {{-- Status badge --}}
                    <span style="display:inline-flex;align-items:center;gap:5px;font-size:10px;font-weight:700;padding:3px 9px;border-radius:99px;background:{{ $sc['bg'] }};color:{{ $sc['txt'] }};letter-spacing:.04em;text-transform:uppercase">
                        <span style="width:5px;height:5px;border-radius:50%;background:{{ $sc['dot'] }};display:inline-block"></span>
                        {{ $sc['label'] }}
                    </span>
                </div>
                <p style="font-size:12px;color:#6b7280;font-family:monospace">
                    {{ $order->beneficiary_account_number }}
                    <span style="font-family:sans-serif;color:#9ca3af"> · </span>
                    {{ $order->beneficiary_name }}
                </p>
            </div>
            <div style="text-align:right;flex-shrink:0">
                <p style="font-size:20px;font-weight:800;color:#111827;white-space:nowrap">NGN {{ number_format($order->amount, 2) }}</p>
                <p style="font-size:11px;color:#9ca3af;margin-top:2px">per {{ strtolower($order->frequency) }}</p>
            </div>
        </div>

        <div style="display:flex;align-items:center;justify-content:space-between;padding-top:14px;border-top:1px solid #f3f4f6;flex-wrap:wrap;gap:10px">
            <div style="display:flex;align-items:center;gap:20px">
                <div>
                    <p style="font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px">Next Run</p>
                    <p style="font-size:12px;font-weight:600;color:#374151">
                        {{ $order->next_run_date ? \Carbon\Carbon::parse($order->next_run_date)->format('d M Y') : '—' }}
                    </p>
                </div>
                <div>
                    <p style="font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px">Runs Completed</p>
                    <p style="font-size:12px;font-weight:600;color:#374151">{{ $order->runs_completed ?? 0 }}</p>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:8px">
                @if($order->status === 'active')
                <form method="POST" action="{{ route('standing-orders.pause', $order->id) }}">
                    @csrf
                    <button type="submit"
                            style="font-size:12px;font-weight:700;color:#92400e;background:#fef9c3;border:1px solid #fde047;padding:6px 14px;border-radius:8px;cursor:pointer">
                        Pause
                    </button>
                </form>
                @elseif($order->status === 'paused')
                <form method="POST" action="{{ route('standing-orders.resume', $order->id) }}">
                    @csrf
                    <button type="submit"
                            style="font-size:12px;font-weight:700;color:#15803d;background:#f0fdf4;border:1px solid #86efac;padding:6px 14px;border-radius:8px;cursor:pointer">
                        Resume
                    </button>
                </form>
                @endif

                @if($order->status !== 'cancelled')
                <form method="POST" action="{{ route('standing-orders.destroy', $order->id) }}"
                      onsubmit="return confirm('Cancel this standing order? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            style="font-size:12px;font-weight:700;color:#dc2626;background:#fef2f2;border:1px solid #fecaca;padding:6px 14px;border-radius:8px;cursor:pointer">
                        Cancel
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

@endsection
