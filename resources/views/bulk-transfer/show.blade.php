@extends('layouts.portal')
@section('title', 'Bulk Transfer — ' . $transfer->reference)

@section('content')

{{-- Page Header --}}
<div style="display:flex;align-items:center;gap:14px;margin-bottom:28px">
    <a href="{{ route('bulk-transfer') }}"
       style="display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:8px;border:1px solid #e5e7eb;background:white;color:#6b7280;text-decoration:none;flex-shrink:0">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div>
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Bulk Transfer</p>
        <h1 style="font-size:20px;font-weight:800;color:#111827">{{ $transfer->label ?: $transfer->reference }}</h1>
        <p style="font-size:12px;color:#9ca3af;font-family:monospace;margin-top:2px">{{ $transfer->reference }}</p>
    </div>
</div>

{{-- Alerts --}}
@if(session('success'))
<div style="margin-bottom:20px;padding:13px 16px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;color:#15803d;font-size:13px;font-weight:500">
    {{ session('success') }}
</div>
@endif

@php
    $stColors = [
        'draft'      => ['#f3f4f6','#374151','#d1d5db'],
        'pending'    => ['#fef3c7','#92400e','#fcd34d'],
        'processing' => ['#dbeafe','#1d4ed8','#93c5fd'],
        'completed'  => ['#dcfce7','#15803d','#86efac'],
        'partial'    => ['#ffedd5','#c2410c','#fdba74'],
        'failed'     => ['#fee2e2','#991b1b','#fca5a5'],
    ];
    [$stbg, $sttxt, $stbrd] = $stColors[$transfer->status] ?? ['#f3f4f6','#374151','#d1d5db'];
@endphp

{{-- Job Status Summary Card --}}
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:24px;margin-bottom:20px">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px">
        <div>
            <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Job Status</p>
            <span style="display:inline-block;font-size:12px;font-weight:700;padding:5px 12px;border-radius:99px;background:{{ $stbg }};color:{{ $sttxt }};border:1px solid {{ $stbrd }};letter-spacing:.02em">
                {{ ucfirst($transfer->status) }}
            </span>
        </div>
        <a href="{{ route('bulk-transfer.download', $transfer->id) }}"
           style="display:inline-flex;align-items:center;gap:7px;background:#f8fafc;color:#374151;font-size:12px;font-weight:700;padding:9px 16px;border-radius:9px;text-decoration:none;border:1px solid #e5e7eb">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Download CSV
        </a>
    </div>

    {{-- Stats row --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:0;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden">
        <div style="padding:16px 20px;border-right:1px solid #e5e7eb">
            <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Total Amount</p>
            <p style="font-size:20px;font-weight:800;color:#111827">NGN {{ number_format($transfer->total_amount, 2) }}</p>
        </div>
        <div style="padding:16px 20px;border-right:1px solid #e5e7eb">
            <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Total Records</p>
            <p style="font-size:20px;font-weight:800;color:#111827">{{ $transfer->recipient_count }}</p>
        </div>
        <div style="padding:16px 20px;border-right:1px solid #e5e7eb">
            <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Successful</p>
            <p style="font-size:20px;font-weight:800;color:#15803d">{{ $transfer->processed_count ?? 0 }}</p>
        </div>
        <div style="padding:16px 20px">
            <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Failed</p>
            <p style="font-size:20px;font-weight:800;color:{{ ($transfer->failed_count ?? 0) > 0 ? '#dc2626' : '#111827' }}">{{ $transfer->failed_count ?? 0 }}</p>
        </div>
    </div>

    {{-- Meta row --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:20px;padding-top:20px;border-top:1px solid #f3f4f6">
        <div>
            <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px">Source Account</p>
            @if($account)
            <p style="font-size:13px;font-weight:600;color:#111827;font-family:monospace">{{ $account->account_number }}</p>
            <p style="font-size:11px;color:#9ca3af">{{ $account->account_name }}</p>
            @else
            <p style="font-size:13px;color:#6b7280">—</p>
            @endif
        </div>
        <div>
            <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px">Submitted</p>
            <p style="font-size:13px;font-weight:600;color:#111827">
                {{ $transfer->submitted_at ? \Carbon\Carbon::parse($transfer->submitted_at)->format('d M Y, H:i') : '—' }}
            </p>
        </div>
    </div>

    @if(($transfer->failed_count ?? 0) > 0)
    <div style="margin-top:16px;padding:11px 14px;background:#fef2f2;border:1px solid #fecaca;border-radius:9px;font-size:12px;color:#991b1b;font-weight:500">
        {{ $transfer->failed_count }} item(s) failed to process. {{ $transfer->processed_count ?? 0 }} processed successfully.
    </div>
    @endif
</div>

{{-- Items Table --}}
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);overflow:hidden">
    <div style="padding:16px 20px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between">
        <p style="font-size:13px;font-weight:700;color:#111827">Transfer Items</p>
        <p style="font-size:12px;color:#9ca3af">{{ $items->count() }} records</p>
    </div>
    <div style="overflow-x:auto">
        <table style="width:100%;border-collapse:collapse;min-width:720px">
            <thead>
                <tr style="background:#f8fafc">
                    <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;text-align:center;border-bottom:2px solid #e5e7eb">#</th>
                    <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;text-align:left;border-bottom:2px solid #e5e7eb">Name</th>
                    <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;text-align:left;border-bottom:2px solid #e5e7eb">Account</th>
                    <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;text-align:left;border-bottom:2px solid #e5e7eb">Bank</th>
                    <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;text-align:right;border-bottom:2px solid #e5e7eb">Amount</th>
                    <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;text-align:left;border-bottom:2px solid #e5e7eb">Narration</th>
                    <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;text-align:center;border-bottom:2px solid #e5e7eb">Status</th>
                    <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;text-align:left;border-bottom:2px solid #e5e7eb">Note</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                @php
                    $isc = [
                        'processed' => ['#dcfce7','#15803d'],
                        'pending'   => ['#fef3c7','#92400e'],
                        'failed'    => ['#fee2e2','#991b1b'],
                    ];
                    [$ibg, $itxt] = $isc[$item->status] ?? ['#f3f4f6','#374151'];
                    $rowBg = $item->status === 'failed' ? 'background:#fffbfb' : '';
                @endphp
                <tr style="border-bottom:1px solid #f3f4f6;{{ $rowBg }}">
                    <td style="padding:12px 14px;text-align:center;font-size:12px;color:#9ca3af;font-weight:600">{{ $item->row_number }}</td>
                    <td style="padding:12px 14px;font-size:13px;font-weight:600;color:#111827">{{ $item->beneficiary_name }}</td>
                    <td style="padding:12px 14px;font-size:12px;font-family:monospace;color:#374151;font-weight:600">{{ $item->account_number }}</td>
                    <td style="padding:12px 14px;font-size:12px;color:#6b7280">{{ $item->bank_name ?: ($item->bank_code ?: '—') }}</td>
                    <td style="padding:12px 14px;text-align:right;font-size:13px;font-weight:700;color:#111827">
                        NGN {{ number_format($item->amount, 2) }}
                    </td>
                    <td style="padding:12px 14px;font-size:12px;color:#6b7280;max-width:180px">
                        {{ $item->narration ?: '—' }}
                    </td>
                    <td style="padding:12px 14px;text-align:center">
                        <span style="display:inline-block;font-size:11px;font-weight:700;padding:4px 10px;border-radius:99px;background:{{ $ibg }};color:{{ $itxt }}">
                            {{ ucfirst($item->status) }}
                        </span>
                    </td>
                    <td style="padding:12px 14px;font-size:12px;color:#dc2626;font-weight:500">
                        {{ $item->failure_reason ?: '' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
