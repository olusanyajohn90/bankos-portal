@extends('layouts.portal')
@section('title', 'Overdraft Facility')

@section('content')

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;gap:12px;flex-wrap:wrap">
    <div style="display:flex;align-items:center;gap:12px">
        <a href="{{ route('dashboard') }}"
           style="display:flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:9px;border:1px solid #e5e7eb;color:#6b7280;text-decoration:none;flex-shrink:0;background:white">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        </a>
        <div>
            <h1 style="font-size:20px;font-weight:800;color:#111827;margin:0 0 3px">Overdraft Facility</h1>
            <p style="font-size:13px;color:#6b7280;margin:0">Access funds beyond your account balance</p>
        </div>
    </div>
    <a href="{{ route('overdraft.create') }}"
       style="display:inline-flex;align-items:center;gap:7px;font-size:13px;font-weight:700;color:white;background:#2563eb;padding:11px 20px;border-radius:10px;text-decoration:none;flex-shrink:0">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Apply for Overdraft
    </a>
</div>

@if(session('error'))
<div style="display:flex;align-items:flex-start;gap:10px;padding:13px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;font-size:13px;color:#991b1b;margin-bottom:18px;font-weight:500">
    <svg style="flex-shrink:0;margin-top:1px" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <span>{{ session('error') }}</span>
</div>
@endif

{{-- Info Banner --}}
<div style="background:linear-gradient(135deg,#1e3a8a 0%,#1d4ed8 60%,#2563eb 100%);border-radius:16px;padding:24px 28px;margin-bottom:24px;color:white;position:relative;overflow:hidden">
    <div style="position:absolute;right:-20px;top:-20px;width:140px;height:140px;border-radius:50%;background:rgba(255,255,255,0.05)"></div>
    <div style="position:absolute;right:50px;bottom:-40px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,0.03)"></div>
    <div style="position:relative">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
            <div style="width:36px;height:36px;border-radius:10px;background:rgba(255,255,255,0.15);display:grid;place-items:center;flex-shrink:0">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <p style="font-size:15px;font-weight:700;color:white;margin:0">What is an Overdraft?</p>
        </div>
        <p style="font-size:13px;color:rgba(255,255,255,0.85);line-height:1.7;max-width:600px;margin:0 0 18px">
            An overdraft facility allows you to spend beyond your available account balance — up to an approved limit. Interest is charged only on the amount used, and repayment is automatic when funds are deposited.
        </p>
        <div style="display:flex;gap:24px;flex-wrap:wrap">
            <div>
                <p style="font-size:10px;color:rgba(255,255,255,0.6);letter-spacing:.05em;text-transform:uppercase;margin:0 0 3px">Minimum Limit</p>
                <p style="font-size:14px;font-weight:800;color:white;margin:0">NGN 10,000</p>
            </div>
            <div>
                <p style="font-size:10px;color:rgba(255,255,255,0.6);letter-spacing:.05em;text-transform:uppercase;margin:0 0 3px">Decision Time</p>
                <p style="font-size:14px;font-weight:800;color:white;margin:0">2–3 Business Days</p>
            </div>
            <div>
                <p style="font-size:10px;color:rgba(255,255,255,0.6);letter-spacing:.05em;text-transform:uppercase;margin:0 0 3px">Subject To</p>
                <p style="font-size:14px;font-weight:800;color:white;margin:0">Credit Assessment</p>
            </div>
        </div>
    </div>
</div>

{{-- Approved Facilities --}}
@php
    $approvedRequests = $requests->where('status', 'approved')->where('approved_limit', '>', 0);
    $accountsById = $accounts->keyBy('id');
@endphp

@if($approvedRequests->isNotEmpty())
<p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:14px">Active Overdraft Facilities</p>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px;margin-bottom:24px">
    @foreach($approvedRequests as $approvedReq)
    @php $acc = $accountsById->get($approvedReq->account_id); @endphp
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:20px">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:16px">
            <div>
                <p style="font-size:14px;font-weight:700;color:#111827;margin:0 0 3px">{{ $acc?->account_name ?? 'Account' }}</p>
                <p style="font-size:12px;color:#9ca3af;margin:0">{{ $acc?->account_number ?? $approvedReq->account_id }}</p>
            </div>
            <span style="font-size:10px;font-weight:800;padding:4px 10px;border-radius:99px;background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;text-transform:uppercase;letter-spacing:.04em;flex-shrink:0">Approved</span>
        </div>
        <div style="background:#f0fdf4;border-radius:10px;padding:14px 16px">
            <p style="font-size:11px;color:#15803d;margin:0 0 4px;font-weight:700;text-transform:uppercase;letter-spacing:.04em">Approved Limit</p>
            <p style="font-size:24px;font-weight:800;color:#15803d;margin:0">NGN {{ number_format($approvedReq->approved_limit, 2) }}</p>
        </div>
        @if($approvedReq->review_note)
        <p style="font-size:12px;color:#6b7280;margin:10px 0 0;line-height:1.5">{{ $approvedReq->review_note }}</p>
        @endif
        <p style="font-size:11px;color:#9ca3af;margin:8px 0 0;font-family:monospace">{{ $approvedReq->reference }}</p>
    </div>
    @endforeach
</div>
@endif

{{-- All Applications Table --}}
@if($requests->isNotEmpty())
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);overflow:hidden">
    <div style="padding:18px 22px;border-bottom:1px solid #f3f4f6">
        <p style="font-size:14px;font-weight:700;color:#111827;margin:0 0 2px">All Applications</p>
        <p style="font-size:12px;color:#9ca3af;margin:0">Your overdraft request history</p>
    </div>
    <div style="overflow-x:auto">
        <table style="width:100%;border-collapse:collapse;font-size:13px">
            <thead>
                <tr style="background:#f9fafb">
                    <th style="padding:10px 22px;text-align:left;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;white-space:nowrap">Reference</th>
                    <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;white-space:nowrap">Account</th>
                    <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;white-space:nowrap">Requested</th>
                    <th style="padding:10px 16px;text-align:right;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;white-space:nowrap">Approved</th>
                    <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;white-space:nowrap">Status</th>
                    <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;white-space:nowrap">Date</th>
                    <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($requests as $req)
                @php
                    $statusMap = [
                        'pending'      => ['bg'=>'#fffbeb','text'=>'#92400e','border'=>'#fde68a'],
                        'under_review' => ['bg'=>'#eff6ff','text'=>'#1d4ed8','border'=>'#bfdbfe'],
                        'approved'     => ['bg'=>'#f0fdf4','text'=>'#15803d','border'=>'#bbf7d0'],
                        'rejected'     => ['bg'=>'#fef2f2','text'=>'#991b1b','border'=>'#fecaca'],
                        'cancelled'    => ['bg'=>'#f9fafb','text'=>'#6b7280','border'=>'#e5e7eb'],
                    ];
                    $sm = $statusMap[$req->status] ?? $statusMap['pending'];
                    $acc = $accountsById->get($req->account_id);
                    $statusLabel = ['pending'=>'Pending','under_review'=>'Under Review','approved'=>'Approved','rejected'=>'Rejected','cancelled'=>'Cancelled'];
                @endphp
                <tr style="border-top:1px solid #f3f4f6">
                    <td style="padding:14px 22px;font-weight:700;color:#374151;font-family:monospace;font-size:12px">{{ $req->reference }}</td>
                    <td style="padding:14px 16px">
                        <p style="font-size:13px;font-weight:600;color:#374151;margin:0 0 2px">{{ $acc?->account_name ?? '—' }}</p>
                        <p style="font-size:11px;color:#9ca3af;margin:0">{{ $acc?->account_number ?? $req->account_id }}</p>
                    </td>
                    <td style="padding:14px 16px;text-align:right;font-weight:700;color:#374151;white-space:nowrap">NGN {{ number_format($req->requested_limit, 2) }}</td>
                    <td style="padding:14px 16px;text-align:right;white-space:nowrap">
                        @if($req->approved_limit)
                        <span style="font-weight:800;color:#15803d">NGN {{ number_format($req->approved_limit, 2) }}</span>
                        @else
                        <span style="color:#d1d5db">—</span>
                        @endif
                    </td>
                    <td style="padding:14px 16px">
                        <span style="font-size:11px;font-weight:700;padding:4px 10px;border-radius:99px;background:{{ $sm['bg'] }};color:{{ $sm['text'] }};border:1px solid {{ $sm['border'] }};white-space:nowrap;letter-spacing:.03em">
                            {{ $statusLabel[$req->status] ?? $req->status }}
                        </span>
                    </td>
                    <td style="padding:14px 16px;color:#9ca3af;font-size:12px;white-space:nowrap">{{ \Carbon\Carbon::parse($req->created_at)->format('d M Y') }}</td>
                    <td style="padding:14px 16px">
                        @if(in_array($req->status, ['pending', 'under_review']))
                        <form method="POST" action="{{ route('overdraft.cancel', $req->id) }}" onsubmit="return confirm('Cancel this overdraft application?')">
                            @csrf
                            <button type="submit" style="font-size:11px;font-weight:700;color:#dc2626;background:none;border:none;cursor:pointer;padding:0">Cancel</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @if($req->review_note)
                <tr style="border-top:none">
                    <td colspan="7" style="padding:0 22px 14px 22px">
                        <p style="font-size:12px;color:#6b7280;background:#f9fafb;padding:9px 13px;border-radius:8px;border-left:3px solid #d1d5db;line-height:1.6;margin:0">
                            <strong>Note:</strong> {{ $req->review_note }}
                        </p>
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@else
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:56px;text-align:center">
    <div style="width:56px;height:56px;border-radius:14px;background:#eff6ff;display:grid;place-items:center;margin:0 auto 16px">
        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
    </div>
    <p style="font-size:15px;font-weight:700;color:#374151;margin:0 0 6px">No overdraft applications yet</p>
    <p style="font-size:13px;color:#9ca3af;margin:0 auto 22px;max-width:340px">Apply for an overdraft facility to access funds when you need them most.</p>
    <a href="{{ route('overdraft.create') }}"
       style="display:inline-flex;align-items:center;gap:7px;font-size:13px;font-weight:700;color:white;background:#2563eb;padding:11px 24px;border-radius:10px;text-decoration:none">
        Apply Now
    </a>
</div>
@endif

@endsection
