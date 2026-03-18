@extends('layouts.portal')
@section('title', 'Bulk Transfer')

@section('content')

{{-- Page Header --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:28px;gap:16px">
    <div>
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Payments</p>
        <h1 style="font-size:20px;font-weight:800;color:#111827;margin-bottom:4px">Bulk Transfer</h1>
        <p style="font-size:13px;color:#6b7280">Send payments to multiple recipients at once — payroll, vendors, commissions</p>
    </div>
    <a href="{{ route('bulk-transfer.create') }}"
       style="display:inline-flex;align-items:center;gap:8px;background:#2563eb;color:white;font-size:13px;font-weight:700;padding:11px 20px;border-radius:10px;border:none;cursor:pointer;text-decoration:none;white-space:nowrap;flex-shrink:0">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Bulk Transfer
    </a>
</div>

{{-- Alerts --}}
@if(session('success'))
<div style="margin-bottom:20px;padding:13px 16px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;color:#15803d;font-size:13px;font-weight:500">
    {{ session('success') }}
</div>
@endif
@if($errors->any())
<div style="margin-bottom:20px;padding:13px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;color:#991b1b;font-size:13px;font-weight:500">
    {{ $errors->first() }}
</div>
@endif

@if($transfers->isEmpty())
{{-- Empty state --}}
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:64px 24px;text-align:center">
    <div style="width:56px;height:56px;border-radius:14px;background:#eff6ff;display:inline-flex;align-items:center;justify-content:center;margin-bottom:16px">
        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.8"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
    </div>
    <p style="font-size:15px;font-weight:700;color:#111827;margin-bottom:6px">No bulk transfers yet</p>
    <p style="font-size:13px;color:#6b7280;margin-bottom:24px;max-width:340px;margin-left:auto;margin-right:auto">Upload a CSV file to send payments to multiple recipients simultaneously.</p>
    <a href="{{ route('bulk-transfer.create') }}"
       style="display:inline-flex;align-items:center;gap:8px;background:#2563eb;color:white;font-size:13px;font-weight:700;padding:11px 24px;border-radius:10px;text-decoration:none">
        Start a Bulk Transfer
    </a>
</div>

@else
{{-- Stats Bar --}}
@php
    $totalJobs   = $transfers->total();
    $totalAmount = $transfers->sum('total_amount');
    $completedJobs = $transfers->where('status','completed')->count();
@endphp
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:20px">
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:18px 20px">
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px">Total Jobs</p>
        <p style="font-size:26px;font-weight:800;color:#111827">{{ number_format($totalJobs) }}</p>
    </div>
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:18px 20px">
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px">Total Amount Sent</p>
        <p style="font-size:26px;font-weight:800;color:#111827">NGN {{ number_format($totalAmount, 2) }}</p>
    </div>
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:18px 20px">
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px">Completed</p>
        <p style="font-size:26px;font-weight:800;color:#15803d">{{ number_format($completedJobs) }}</p>
    </div>
</div>

{{-- Table --}}
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);overflow:hidden">
    <div style="padding:16px 20px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between">
        <p style="font-size:13px;font-weight:700;color:#111827">Transfer Jobs</p>
        <p style="font-size:12px;color:#9ca3af">{{ $transfers->total() }} total</p>
    </div>
    <div style="overflow-x:auto">
        <table style="width:100%;border-collapse:collapse;min-width:680px">
            <thead>
                <tr style="background:#f8fafc">
                    <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;text-align:left;border-bottom:2px solid #e5e7eb">Reference</th>
                    <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;text-align:left;border-bottom:2px solid #e5e7eb">Label</th>
                    <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;text-align:right;border-bottom:2px solid #e5e7eb">Total Amount</th>
                    <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;text-align:center;border-bottom:2px solid #e5e7eb">Recipients</th>
                    <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;text-align:center;border-bottom:2px solid #e5e7eb">Status</th>
                    <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;text-align:left;border-bottom:2px solid #e5e7eb">Date</th>
                    <th style="padding:10px 14px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;text-align:center;border-bottom:2px solid #e5e7eb">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transfers as $t)
                @php
                    $stColors = [
                        'draft'      => ['#f3f4f6','#374151'],
                        'pending'    => ['#fef3c7','#92400e'],
                        'processing' => ['#dbeafe','#1d4ed8'],
                        'completed'  => ['#dcfce7','#15803d'],
                        'partial'    => ['#ffedd5','#c2410c'],
                        'failed'     => ['#fee2e2','#991b1b'],
                    ];
                    [$stbg, $sttxt] = $stColors[$t->status] ?? ['#f3f4f6','#374151'];
                @endphp
                <tr style="border-bottom:1px solid #f3f4f6">
                    <td style="padding:12px 14px;font-size:12px;font-family:monospace;color:#374151;font-weight:600">{{ $t->reference }}</td>
                    <td style="padding:12px 14px;font-size:13px;color:#111827;font-weight:500">{{ $t->label ?: '—' }}</td>
                    <td style="padding:12px 14px;text-align:right;font-size:13px;font-weight:700;color:#111827">
                        NGN {{ number_format($t->total_amount, 2) }}
                    </td>
                    <td style="padding:12px 14px;text-align:center;font-size:13px;color:#374151;font-weight:600">{{ $t->recipient_count }}</td>
                    <td style="padding:12px 14px;text-align:center">
                        <span style="display:inline-block;font-size:11px;font-weight:700;padding:4px 10px;border-radius:99px;background:{{ $stbg }};color:{{ $sttxt }};letter-spacing:.02em">
                            {{ ucfirst($t->status) }}
                        </span>
                    </td>
                    <td style="padding:12px 14px;font-size:12px;color:#6b7280">
                        {{ $t->submitted_at ? \Carbon\Carbon::parse($t->submitted_at)->format('d M Y, H:i') : \Carbon\Carbon::parse($t->created_at)->format('d M Y') }}
                    </td>
                    <td style="padding:12px 14px;text-align:center">
                        <a href="{{ route('bulk-transfer.show', $t->id) }}"
                           style="display:inline-flex;align-items:center;gap:4px;font-size:12px;font-weight:700;color:#2563eb;text-decoration:none;padding:5px 12px;border:1px solid #bfdbfe;border-radius:7px;background:#eff6ff">
                            View
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($transfers->hasPages())
    <div style="padding:14px 20px;border-top:1px solid #f3f4f6;display:flex;justify-content:center">
        {{ $transfers->links() }}
    </div>
    @endif
</div>
@endif

@endsection
