@extends('layouts.portal')
@section('title', 'Split Bills')

@section('content')
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px">
    <div style="display:flex;align-items:center;gap:12px">
        <a href="{{ route('dashboard') }}"
           style="display:flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:9px;border:1px solid #e5e7eb;color:#6b7280;text-decoration:none;flex-shrink:0;background:white">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        </a>
        <div>
            <h1 style="font-size:20px;font-weight:800;color:#111827;margin:0 0 3px">Split Bills</h1>
            <p style="font-size:13px;color:#6b7280;margin:0">Split a bill or expense among multiple people</p>
        </div>
    </div>
    <a href="{{ route('split-bills.create') }}"
       style="background:#2563eb;color:white;font-size:13px;font-weight:700;padding:11px 20px;border-radius:10px;text-decoration:none;display:inline-flex;align-items:center;gap:7px">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Split
    </a>
</div>

@if(session('success'))
<div style="margin-bottom:18px;padding:13px 16px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;color:#15803d;font-size:13px;font-weight:500">
    {{ session('success') }}
</div>
@endif

@if($bills->isEmpty())
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:64px 24px;text-align:center">
    <div style="width:56px;height:56px;border-radius:14px;background:#f3f4f6;display:grid;place-items:center;margin:0 auto 16px">
        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
    </div>
    <p style="font-size:15px;font-weight:700;color:#374151;margin-bottom:6px">No split bills yet</p>
    <p style="font-size:13px;color:#9ca3af;margin-bottom:22px">Create a split bill to share expenses with others</p>
    <a href="{{ route('split-bills.create') }}"
       style="background:#2563eb;color:white;font-size:13px;font-weight:700;padding:11px 22px;border-radius:10px;text-decoration:none">
        Create Your First Split Bill
    </a>
</div>
@else
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);overflow:hidden">
    @foreach($bills as $bill)
    @php
        $pct = $bill->total_amount > 0 ? min(100, round(($bill->collected_amount / $bill->total_amount) * 100)) : 0;
        $statusMap = [
            'open'      => ['bg'=>'#eff6ff','text'=>'#2563eb'],
            'completed' => ['bg'=>'#f0fdf4','text'=>'#15803d'],
            'cancelled' => ['bg'=>'#f9fafb','text'=>'#6b7280'],
        ];
        [$sbg, $scol] = $statusMap[$bill->status] ?? ['#f9fafb','#6b7280'];
    @endphp
    <div style="padding:18px 22px;border-bottom:1px solid #f3f4f6">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:12px;gap:12px">
            <div style="flex:1;min-width:0">
                <a href="{{ route('split-bills.show', $bill->id) }}"
                   style="font-size:15px;font-weight:700;color:#111827;text-decoration:none;display:block;margin-bottom:3px">{{ $bill->title }}</a>
                <p style="font-size:11px;color:#9ca3af;font-family:monospace;margin:0">{{ $bill->reference }}</p>
            </div>
            <span style="font-size:10px;font-weight:800;padding:4px 10px;border-radius:99px;background:{{ $sbg }};color:{{ $scol }};white-space:nowrap;flex-shrink:0;letter-spacing:.04em">
                {{ strtoupper($bill->status) }}
            </span>
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between;font-size:12px;color:#6b7280;margin-bottom:10px;flex-wrap:wrap;gap:6px">
            <span>NGN {{ number_format($bill->per_person_amount, 2) }} &times; {{ $bill->participant_count }} people</span>
            <span style="font-weight:700;color:#111827;font-size:13px">Total: NGN {{ number_format($bill->total_amount, 2) }}</span>
        </div>
        <div style="background:#f3f4f6;border-radius:99px;height:5px;overflow:hidden;margin-bottom:7px">
            <div style="background:#2563eb;height:100%;width:{{ $pct }}%;border-radius:99px"></div>
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between;font-size:11px;color:#9ca3af">
            <span>{{ $bill->paid_count }} of {{ $bill->participant_count }} paid</span>
            <span style="font-weight:600;color:#374151">NGN {{ number_format($bill->collected_amount, 2) }} collected</span>
        </div>
    </div>
    @endforeach
    @if($bills->hasPages())
    <div style="padding:14px 22px;background:#f9fafb;border-top:1px solid #f3f4f6">
        {{ $bills->links() }}
    </div>
    @endif
</div>
@endif
@endsection
