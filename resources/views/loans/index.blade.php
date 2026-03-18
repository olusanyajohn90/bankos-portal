@extends('layouts.portal')
@section('title', 'Loans')

@section('content')

{{-- Page header --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px">
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#111827;margin-bottom:3px">My Loans</h1>
        <p style="font-size:13px;color:#6b7280">View your loan history, track repayments and manage your credit.</p>
    </div>
    <a href="{{ route('loans.apply') }}"
       style="display:inline-flex;align-items:center;gap:7px;background:#2563eb;color:white;font-size:13px;font-weight:700;padding:11px 20px;border-radius:10px;border:none;cursor:pointer;text-decoration:none">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Apply for a Loan
    </a>
</div>

@if(!$loans->isEmpty())
{{-- Hero stat cards --}}
@php
    $totalBorrowed   = $loans->sum('principal_amount');
    $totalOutstanding = $loans->sum('outstanding_balance');
    $nextPayment     = $loans->where('status','active')->sortBy('due_date')->first();
@endphp
<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:28px">
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:20px">
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px">Total Borrowed</p>
        <p style="font-size:22px;font-weight:800;color:#111827;letter-spacing:-0.5px">NGN {{ number_format($totalBorrowed, 0) }}</p>
        <p style="font-size:12px;color:#9ca3af;margin-top:4px">{{ $loans->count() }} loan{{ $loans->count() === 1 ? '' : 's' }} total</p>
    </div>
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:20px">
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px">Outstanding Balance</p>
        <p style="font-size:22px;font-weight:800;color:{{ $totalOutstanding > 0 ? '#dc2626' : '#15803d' }};letter-spacing:-0.5px">NGN {{ number_format($totalOutstanding, 0) }}</p>
        <p style="font-size:12px;color:#9ca3af;margin-top:4px">Across all active loans</p>
    </div>
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:20px">
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px">Next Payment Due</p>
        @if($nextPayment && $nextPayment->due_date)
            <p style="font-size:22px;font-weight:800;color:#111827;letter-spacing:-0.5px">{{ \Carbon\Carbon::parse($nextPayment->due_date)->format('d M') }}</p>
            <p style="font-size:12px;color:#9ca3af;margin-top:4px">{{ \Carbon\Carbon::parse($nextPayment->due_date)->format('Y') }} &middot; {{ $nextPayment->loan_number }}</p>
        @else
            <p style="font-size:22px;font-weight:800;color:#9ca3af">—</p>
            <p style="font-size:12px;color:#9ca3af;margin-top:4px">No upcoming payments</p>
        @endif
    </div>
</div>
@endif

@if($loans->isEmpty())
{{-- Empty state --}}
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:72px 24px;text-align:center">
    <div style="width:64px;height:64px;border-radius:16px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;margin:0 auto 20px">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.5"><rect x="2" y="3" width="20" height="18" rx="3"/><line x1="8" y1="9" x2="16" y2="9"/><line x1="8" y1="13" x2="14" y2="13"/><line x1="8" y1="17" x2="11" y2="17"/></svg>
    </div>
    <p style="font-size:16px;font-weight:800;color:#111827;margin-bottom:6px">No loans yet</p>
    <p style="font-size:13px;color:#6b7280;max-width:340px;margin:0 auto 24px">You have no loan records. Apply for a loan to get started with flexible financing.</p>
    <a href="{{ route('loans.apply') }}"
       style="display:inline-flex;align-items:center;gap:7px;background:#2563eb;color:white;font-size:13px;font-weight:700;padding:11px 20px;border-radius:10px;text-decoration:none">
        Apply for a Loan
    </a>
</div>
@else
{{-- Loan list --}}
<p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px">All Loans</p>
<div style="display:flex;flex-direction:column;gap:12px">
    @foreach($loans as $loan)
    @php
        $statusColor = match($loan->status) {
            'active'      => ['#15803d', '#f0fdf4', '#bbf7d0'],
            'overdue'     => ['#dc2626', '#fef2f2', '#fecaca'],
            'pending'     => ['#d97706', '#fffbeb', '#fde68a'],
            'approved'    => ['#2563eb', '#eff6ff', '#bfdbfe'],
            'closed'      => ['#6b7280', '#f9fafb', '#e5e7eb'],
            'written_off' => ['#6b7280', '#f9fafb', '#e5e7eb'],
            default       => ['#6b7280', '#f9fafb', '#e5e7eb'],
        };
        $progress = $loan->principal_amount > 0
            ? max(0, min(100, (1 - $loan->outstanding_balance / $loan->principal_amount) * 100))
            : 0;
        $isOverdue = $loan->status === 'overdue';
    @endphp
    <div style="background:white;border:1px solid {{ $isOverdue ? '#fecaca' : '#e5e7eb' }};border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:20px">

        {{-- Top row: loan number + status badge --}}
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:16px">
            <div>
                <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:3px">Loan Number</p>
                <p style="font-size:16px;font-weight:800;color:#111827">{{ $loan->loan_number ?? 'N/A' }}</p>
            </div>
            <span style="font-size:11px;font-weight:700;padding:3px 9px;border-radius:20px;background:{{ $statusColor[1] }};color:{{ $statusColor[0] }};border:1px solid {{ $statusColor[2] }}">
                {{ strtoupper(str_replace('_', ' ', $loan->status)) }}
            </span>
        </div>

        {{-- Stat grid --}}
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:16px">
            <div>
                <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Principal</p>
                <p style="font-size:14px;font-weight:700;color:#111827">NGN {{ number_format((float)$loan->principal_amount, 0) }}</p>
            </div>
            <div>
                <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Outstanding</p>
                <p style="font-size:14px;font-weight:700;color:{{ $loan->outstanding_balance > 0 ? '#dc2626' : '#15803d' }}">
                    NGN {{ number_format((float)$loan->outstanding_balance, 0) }}
                </p>
            </div>
            <div>
                <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Tenure</p>
                <p style="font-size:14px;font-weight:700;color:#111827">{{ $loan->tenure_days ?? '—' }} days</p>
            </div>
            <div>
                <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Disbursed</p>
                <p style="font-size:13px;font-weight:600;color:#374151">{{ $loan->disbursed_at ? $loan->disbursed_at->format('d M Y') : '—' }}</p>
            </div>
            <div>
                <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Repayment</p>
                <p style="font-size:13px;font-weight:600;color:#374151">{{ ucfirst($loan->repayment_frequency ?? 'Monthly') }}</p>
            </div>
            @if($loan->due_date)
            <div>
                <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Due Date</p>
                <p style="font-size:13px;font-weight:600;color:{{ $isOverdue ? '#dc2626' : '#374151' }}">{{ \Carbon\Carbon::parse($loan->due_date)->format('d M Y') }}</p>
            </div>
            @endif
        </div>

        {{-- Progress bar --}}
        @if(in_array($loan->status, ['active', 'overdue']) && $loan->principal_amount > 0)
        <div style="margin-bottom:14px">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
                <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em">Repaid</p>
                <p style="font-size:12px;font-weight:700;color:{{ $isOverdue ? '#dc2626' : '#2563eb' }}">{{ number_format($progress, 0) }}%</p>
            </div>
            <div style="height:6px;border-radius:3px;background:#f3f4f6;overflow:hidden">
                <div style="height:100%;width:{{ $progress }}%;background:{{ $isOverdue ? '#dc2626' : '#2563eb' }};border-radius:3px"></div>
            </div>
        </div>
        @endif

        {{-- Actions --}}
        <div style="display:flex;align-items:center;gap:12px;border-top:1px solid #f3f4f6;padding-top:14px">
            <a href="{{ route('loans.show', $loan->id) }}"
               style="font-size:13px;font-weight:700;color:#2563eb;text-decoration:none">View Details</a>
            @if(in_array($loan->status, ['active', 'overdue']))
            <span style="width:1px;height:14px;background:#e5e7eb;display:inline-block"></span>
            <a href="{{ route('loans.show', $loan->id) }}#repay"
               style="font-size:13px;font-weight:700;color:#15803d;text-decoration:none">Make Repayment</a>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endif

@endsection
