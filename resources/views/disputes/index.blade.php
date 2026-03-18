@extends('layouts.portal')
@section('title', 'Disputes Centre')

@section('content')

{{-- Page Header --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:28px;gap:16px;flex-wrap:wrap">
    <div>
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Support</p>
        <h1 style="font-size:20px;font-weight:800;color:#111827;margin-bottom:4px">Disputes Centre</h1>
        <p style="font-size:13px;color:#6b7280">Report and track issues with your transactions</p>
    </div>
    <a href="{{ route('disputes.create') }}"
       style="display:inline-flex;align-items:center;gap:8px;background:#dc2626;color:white;font-size:13px;font-weight:700;padding:11px 20px;border-radius:10px;border:none;cursor:pointer;text-decoration:none;white-space:nowrap;flex-shrink:0">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Raise a Dispute
    </a>
</div>

@if(session('success'))
<div style="margin-bottom:20px;padding:13px 16px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;color:#15803d;font-size:13px;font-weight:500">
    {{ session('success') }}
</div>
@endif

@if($disputes->isEmpty())

<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:72px 24px;text-align:center">
    <div style="width:56px;height:56px;border-radius:14px;background:#f0fdf4;display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px">
        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
    </div>
    <p style="font-size:15px;font-weight:700;color:#111827;margin-bottom:6px">No disputes on record</p>
    <p style="font-size:13px;color:#6b7280;max-width:360px;margin:0 auto 24px">All clear. If you notice an issue with a transaction, you can raise a dispute at any time.</p>
    <a href="{{ route('disputes.create') }}"
       style="display:inline-flex;align-items:center;gap:8px;background:#dc2626;color:white;font-size:13px;font-weight:700;padding:11px 24px;border-radius:10px;text-decoration:none">
        Raise a Dispute
    </a>
</div>

@else

<div style="display:flex;flex-direction:column;gap:12px">
    @foreach($disputes as $d)
    @php
        $sc = \App\Models\PortalDispute::$statusColors[$d->status] ?? ['#6b7280','#f3f4f6','#d1d5db'];
        $isOpen = in_array($d->status, ['open','investigating','escalated','pending']);
    @endphp
    <a href="{{ route('disputes.show', $d->id) }}"
       style="display:block;background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:20px;text-decoration:none;transition:border-color .15s"
       onmouseover="this.style.borderColor='#93c5fd'" onmouseout="this.style.borderColor='#e5e7eb'">

        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;margin-bottom:10px">
            <div style="min-width:0">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:5px;flex-wrap:wrap">
                    <p style="font-size:14px;font-weight:800;color:#111827">
                        {{ \App\Models\PortalDispute::$types[$d->type] ?? $d->type }}
                    </p>
                    {{-- Status badge --}}
                    <span style="display:inline-block;font-size:10px;font-weight:700;padding:3px 10px;border-radius:99px;background:{{ $sc[1] }};color:{{ $sc[0] }};letter-spacing:.04em;text-transform:uppercase">
                        {{ strtoupper($d->status) }}
                    </span>
                    @if($isOpen)
                    <span style="display:inline-flex;align-items:center;gap:4px;font-size:10px;font-weight:700;color:#d97706">
                        <span style="width:6px;height:6px;border-radius:50%;background:#d97706;display:inline-block;animation:pulse 1.5s infinite"></span>
                        In progress
                    </span>
                    @endif
                </div>
                <p style="font-size:12px;color:#9ca3af;font-family:monospace">
                    Ticket {{ $d->reference }}
                    <span style="font-family:sans-serif"> · </span>
                    {{ $d->created_at->format('d M Y') }}
                </p>
            </div>
            @if($d->disputed_amount)
            <p style="font-size:15px;font-weight:800;color:#dc2626;flex-shrink:0;white-space:nowrap">
                NGN {{ number_format($d->disputed_amount, 2) }}
            </p>
            @endif
        </div>

        <p style="font-size:12px;color:#6b7280;line-height:1.6;margin-bottom:0">{{ Str::limit($d->description, 120) }}</p>

        @if($d->resolved_at)
        <p style="font-size:11px;color:#9ca3af;margin-top:8px;padding-top:8px;border-top:1px solid #f3f4f6">
            Resolved {{ $d->resolved_at->diffForHumans() }}
        </p>
        @endif
    </a>
    @endforeach
</div>

@if(method_exists($disputes,'links') && $disputes->hasPages())
<div style="margin-top:16px">{{ $disputes->links() }}</div>
@endif

@endif

<style>
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }
</style>

@endsection
