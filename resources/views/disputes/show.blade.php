@extends('layouts.portal')
@section('title', 'Dispute #' . $dispute->reference)

@section('content')

{{-- Page Header --}}
<div style="display:flex;align-items:center;gap:14px;margin-bottom:28px">
    <a href="{{ route('disputes') }}"
       style="display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:8px;border:1px solid #e5e7eb;background:white;color:#6b7280;text-decoration:none;flex-shrink:0">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div>
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Disputes Centre</p>
        <h1 style="font-size:20px;font-weight:800;color:#111827">Dispute {{ $dispute->reference }}</h1>
        <p style="font-size:12px;color:#9ca3af;margin-top:2px">Submitted {{ $dispute->created_at->format('d M Y, H:i') }}</p>
    </div>
</div>

@php
    $sc = \App\Models\PortalDispute::$statusColors[$dispute->status] ?? ['#6b7280','#f3f4f6','#d1d5db'];

    $steps = [
        [
            'label' => 'Submitted',
            'done'  => true,
            'date'  => $dispute->created_at->format('d M Y'),
        ],
        [
            'label' => 'Under Review',
            'done'  => in_array($dispute->status, ['investigating','resolved','rejected','escalated']),
            'date'  => in_array($dispute->status, ['investigating','resolved','rejected','escalated']) ? 'In progress' : null,
        ],
        [
            'label' => 'Resolved',
            'done'  => in_array($dispute->status, ['resolved','rejected']),
            'date'  => $dispute->resolved_at?->format('d M Y') ?? null,
        ],
    ];
@endphp

<div style="max-width:580px">

    {{-- Summary Card --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:24px;margin-bottom:16px">

        {{-- Type + Status row --}}
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:20px">
            <div>
                <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Dispute Type</p>
                <p style="font-size:15px;font-weight:800;color:#111827">
                    {{ \App\Models\PortalDispute::$types[$dispute->type] ?? $dispute->type }}
                </p>
            </div>
            <span style="display:inline-block;font-size:11px;font-weight:700;padding:5px 14px;border-radius:99px;background:{{ $sc[1] }};color:{{ $sc[0] }};letter-spacing:.04em;text-transform:uppercase;flex-shrink:0;margin-top:2px">
                {{ strtoupper($dispute->status) }}
            </span>
        </div>

        {{-- Stats strip --}}
        @if($dispute->disputed_amount)
        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:14px 18px;margin-bottom:20px">
            <p style="font-size:11px;font-weight:700;color:#991b1b;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Disputed Amount</p>
            <p style="font-size:24px;font-weight:800;color:#dc2626">NGN {{ number_format($dispute->disputed_amount, 2) }}</p>
        </div>
        @endif

        {{-- Meta grid --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;padding-bottom:18px;margin-bottom:18px;border-bottom:1px solid #f3f4f6">
            <div>
                <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Ticket Reference</p>
                <p style="font-size:13px;font-weight:700;color:#111827;font-family:monospace">{{ $dispute->reference }}</p>
            </div>
            <div>
                <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Date Submitted</p>
                <p style="font-size:13px;font-weight:600;color:#374151">{{ $dispute->created_at->format('d M Y, H:i') }}</p>
            </div>
        </div>

        {{-- Description --}}
        <div>
            <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px">Description</p>
            <p style="font-size:13px;color:#374151;line-height:1.7">{{ $dispute->description }}</p>
        </div>
    </div>

    {{-- Timeline Card --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:24px;margin-bottom:16px">
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:22px">Status Timeline</p>

        <div style="display:flex;align-items:flex-start">
            @foreach($steps as $i => $step)

            {{-- Step node --}}
            <div style="display:flex;flex-direction:column;align-items:center;flex:1;position:relative">
                <div style="width:38px;height:38px;border-radius:50%;background:{{ $step['done'] ? '#2563eb' : '#f3f4f6' }};border:{{ $step['done'] ? '3px solid #bfdbfe' : '3px solid #e5e7eb' }};display:flex;align-items:center;justify-content:center;margin-bottom:8px;z-index:1">
                    @if($step['done'])
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                    @else
                    <span style="width:8px;height:8px;border-radius:50%;background:#d1d5db;display:block"></span>
                    @endif
                </div>
                <p style="font-size:11px;font-weight:700;color:{{ $step['done'] ? '#1d4ed8' : '#9ca3af' }};text-align:center;line-height:1.4">
                    {{ $step['label'] }}
                </p>
                @if($step['date'])
                <p style="font-size:10px;color:#9ca3af;text-align:center;margin-top:2px">{{ $step['date'] }}</p>
                @endif
            </div>

            {{-- Connector line --}}
            @if(!$loop->last)
            <div style="flex:1;height:3px;background:{{ $steps[$i+1]['done'] ? '#2563eb' : '#e5e7eb' }};margin-top:17px;border-radius:2px"></div>
            @endif

            @endforeach
        </div>
    </div>

    {{-- Bank Response / Resolution --}}
    @if($dispute->resolution_notes)
    @php
        $isResolved = $dispute->status === 'resolved';
        $resBg  = $isResolved ? '#f0fdf4' : '#fef2f2';
        $resBrd = $isResolved ? '#bbf7d0' : '#fecaca';
        $resHd  = $isResolved ? '#166534' : '#991b1b';
        $resTxt = $isResolved ? '#15803d' : '#b91c1c';
    @endphp
    <div style="background:{{ $resBg }};border:1px solid {{ $resBrd }};border-radius:14px;padding:20px">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px">
            <div style="width:28px;height:28px;border-radius:7px;background:{{ $isResolved ? '#dcfce7' : '#fee2e2' }};display:inline-flex;align-items:center;justify-content:center">
                @if($isResolved)
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="{{ $resHd }}" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                @else
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="{{ $resHd }}" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                @endif
            </div>
            <p style="font-size:12px;font-weight:800;color:{{ $resHd }}">Bank Response</p>
        </div>
        <p style="font-size:13px;color:{{ $resTxt }};line-height:1.7">{{ $dispute->resolution_notes }}</p>
        @if($dispute->resolved_at)
        <p style="font-size:11px;color:#9ca3af;margin-top:10px;padding-top:10px;border-top:1px solid {{ $resBrd }}">
            {{ $dispute->resolved_at->format('d M Y, H:i') }}
        </p>
        @endif
    </div>
    @endif

</div>

@endsection
