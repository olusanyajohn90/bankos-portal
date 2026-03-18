<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ ($official ?? false) ? 'Official' : '' }} Statement — {{ $reference }}</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body {
    font-family: 'DejaVu Sans', sans-serif;
    font-size: 11px;
    color: #111827;
    background: #ffffff;
}
p { margin:0; padding:0; }

/* ── Screen wrapper (web preview only) ── */
.screen-shell { background:#f3f4f6; padding:24px 0 48px; }
.page-card    { max-width:800px; margin:0 auto; background:#fff; border-radius:6px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,0.12); }

/* ── Print bar (web preview only) ── */
.print-bar { background:#1e40af; padding:12px 24px; }
.print-bar p { color:rgba(255,255,255,0.85); font-size:12px; display:inline; }
.btn-print { background:white; color:#1d4ed8; font-size:12px; font-weight:700; padding:8px 20px; border-radius:6px; border:none; cursor:pointer; float:right; }

/* ── Header ── */
.bank-header { background:#1e3a8a; padding:0; }
.hdr-name    { font-size:22px; font-weight:800; color:#ffffff; letter-spacing:-0.3px; }
.hdr-meta    { font-size:10px; color:rgba(255,255,255,0.75); margin-top:4px; line-height:1.7; }
.hdr-stitle  { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:1px; color:rgba(255,255,255,0.75); }
.hdr-sref    { font-size:18px; font-weight:700; color:#ffffff; margin-top:2px; }
.hdr-sdate   { font-size:10px; color:rgba(255,255,255,0.65); margin-top:2px; }
.gold-line   { height:3px; background:#c9a84c; }

/* ── Info section ── */
.info-label  { font-size:9px; text-transform:uppercase; letter-spacing:0.06em; color:#9ca3af; margin-bottom:10px; font-weight:700; }
.info-key    { font-size:11px; color:#6b7280; padding:3px 0; }
.info-val    { font-size:11px; font-weight:600; color:#111827; text-align:right; padding:3px 0; }

/* ── Period bar ── */
.period-bar { background:#f8fafc; border-bottom:1px solid #e5e7eb; border-top:1px solid #e5e7eb; padding:10px 20px; }
.period-text { font-size:12px; color:#374151; }

/* ── Transactions ── */
.txn-table   { width:100%; border-collapse:collapse; }
.txn-table thead tr { background:#f9fafb; border-bottom:2px solid #e5e7eb; }
.txn-table th { padding:9px 16px; font-size:9.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:#6b7280; text-align:left; }
.txn-table th.r { text-align:right; }
.txn-table tbody tr { border-bottom:1px solid #f3f4f6; }
.txn-table td { padding:9px 16px; font-size:11px; color:#374151; vertical-align:top; }
.txn-table td.r  { text-align:right; }
.txn-table td.mono { font-family:'DejaVu Sans Mono', monospace; font-size:10px; }
.cr { color:#15803d; font-weight:700; }
.dr { color:#dc2626; font-weight:700; }
.badge-cr { font-size:8.5px; font-weight:700; padding:2px 5px; border-radius:3px; background:#f0fdf4; color:#16a34a; }
.badge-dr { font-size:8.5px; font-weight:700; padding:2px 5px; border-radius:3px; background:#fef2f2; color:#dc2626; }
.txn-ref  { font-size:9.5px; color:#d1d5db; font-family:'DejaVu Sans Mono',monospace; display:block; margin-top:1px; }
.empty-td { text-align:center; padding:36px; color:#9ca3af; font-size:12px; }

/* ── Summary ── */
.summary-label { font-size:9px; text-transform:uppercase; letter-spacing:0.05em; color:#9ca3af; margin-bottom:4px; }
.summary-value { font-size:15px; font-weight:800; }

/* ── Footer ── */
.footer-text  { font-size:10px; color:#6b7280; line-height:1.9; }
.footer-text strong { color:#374151; }

/* ── Stamp ── */
.stamp-outer { width:86px; height:86px; border:2px solid #2563eb; border-radius:43px; text-align:center; }
.stamp-inner { width:70px; height:70px; border:1px dashed #2563eb; border-radius:35px; margin:6px auto 0; text-align:center; }
.stamp-text  { font-size:8px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; line-height:1.7; color:#2563eb; padding-top:14px; }
</style>
</head>
<body>
@php
    $creditTypes = ['deposit', 'disbursement', 'interest'];
@endphp

{{-- ===== SCREEN SHELL (web preview only) ===== --}}
@if(!($forPdf ?? false))
<div class="screen-shell">
<div class="page-card">

    {{-- Print bar --}}
    <div class="print-bar">
        @if($official ?? false)
        <p>Official Statement &mdash; Ref: <strong style="color:white">{{ $reference }}</strong> &nbsp;|&nbsp; Fee charged: NGN {{ number_format($fee ?? 0, 2) }}</p>
        @else
        <p>Standard Statement &mdash; <strong style="color:white">{{ $account->account_name }}</strong></p>
        @endif
        <button class="btn-print" onclick="window.print()">Print / Save PDF</button>
        <div style="clear:both"></div>
    </div>
@endif

    {{-- ===== HEADER ===== --}}
    <table class="bank-header" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td style="padding:24px 28px 20px; vertical-align:bottom">
                <div class="hdr-name">{{ $tenant?->name ?? config('app.name') }}</div>
                <div class="hdr-meta">
                    @if($tenant?->cbn_license_number)CBN License: {{ $tenant->cbn_license_number }}<br>@endif
                    @if($tenant?->contact_phone)Tel: {{ $tenant->contact_phone }}&nbsp;&nbsp;@endif
                    @if($tenant?->contact_email){{ $tenant->contact_email }}@endif
                </div>
            </td>
            <td style="padding:24px 28px 20px; vertical-align:top; text-align:right; width:220px">
                <div class="hdr-stitle">{{ ($official ?? false) ? 'Official Account Statement' : 'Account Statement' }}</div>
                <div class="hdr-sref">{{ $reference }}</div>
                <div class="hdr-sdate">Generated: {{ now()->format('d F Y, H:i') }}</div>
            </td>
        </tr>
    </table>
    <div class="gold-line"></div>

    {{-- ===== ACCOUNT + CUSTOMER INFO ===== --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="border-bottom:1px solid #e5e7eb">
        <tr>
            <td style="width:50%; padding:16px 24px; vertical-align:top; border-right:1px solid #e5e7eb">
                <div class="info-label">Account Information</div>
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr><td class="info-key">Account Name</td><td class="info-val">{{ $account->account_name }}</td></tr>
                    <tr><td class="info-key">Account Number</td><td class="info-val" style="font-family:'DejaVu Sans Mono',monospace">{{ $account->account_number }}</td></tr>
                    <tr><td class="info-key">Account Type</td><td class="info-val">{{ ucfirst(str_replace('_',' ',$account->type ?? 'savings')) }}</td></tr>
                    <tr><td class="info-key">Currency</td><td class="info-val">{{ $account->currency ?? 'NGN' }}</td></tr>
                    <tr><td class="info-key">Current Balance</td><td class="info-val" style="color:#1d4ed8">{{ $account->currency ?? 'NGN' }} {{ number_format((float)$account->available_balance, 2) }}</td></tr>
                </table>
            </td>
            <td style="width:50%; padding:16px 24px; vertical-align:top">
                <div class="info-label">Customer Information</div>
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr><td class="info-key">Full Name</td><td class="info-val">{{ $customer->first_name }} {{ $customer->last_name }}</td></tr>
                    <tr><td class="info-key">Customer No.</td><td class="info-val" style="font-family:'DejaVu Sans Mono',monospace">{{ $customer->customer_number ?? '—' }}</td></tr>
                    <tr><td class="info-key">Email</td><td class="info-val">{{ $customer->email ?? '—' }}</td></tr>
                    <tr><td class="info-key">Phone</td><td class="info-val">{{ $customer->phone ?? '—' }}</td></tr>
                    <tr><td class="info-key">KYC Tier</td><td class="info-val">{{ strtoupper(str_replace('_',' ',$customer->kyc_tier ?? 'level_1')) }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ===== PERIOD BAR ===== --}}
    <table class="period-bar" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td class="period-text">
                Statement Period: <strong>{{ $from ? $from->format('d M Y') : 'All time' }}</strong>
                to <strong>{{ $to ? $to->format('d M Y') : now()->format('d M Y') }}</strong>
                &nbsp;&nbsp;
                <span style="font-size:11px;color:#9ca3af">{{ $transactions->count() }} transaction(s)</span>
            </td>
        </tr>
    </table>

    {{-- ===== TRANSACTIONS ===== --}}
    <table class="txn-table" width="100%" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width:100px">Date</th>
                <th>Description</th>
                <th class="r" style="width:120px">Amount</th>
                <th class="r" style="width:48px">Type</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $txn)
            @php $credit = in_array($txn->type, $creditTypes); @endphp
            <tr>
                <td class="mono" style="white-space:nowrap">
                    {{ $txn->created_at?->format('d M Y') }}<br>
                    <span style="color:#d1d5db;font-size:9.5px">{{ $txn->created_at?->format('H:i') }}</span>
                </td>
                <td>
                    {{ $txn->description ?? ucfirst($txn->type ?? '—') }}
                    @if($txn->reference)<span class="txn-ref">{{ $txn->reference }}</span>@endif
                </td>
                <td class="r {{ $credit ? 'cr' : 'dr' }}">
                    {{ $credit ? '+' : '−' }}{{ number_format((float)$txn->amount, 2) }}
                </td>
                <td class="r">
                    <span class="{{ $credit ? 'badge-cr' : 'badge-dr' }}">{{ $credit ? 'CR' : 'DR' }}</span>
                </td>
            </tr>
            @empty
            <tr><td class="empty-td" colspan="4">No transactions found for the selected period.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- ===== SUMMARY ===== --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="border-top:2px solid #1e40af; background:#f8fafc">
        <tr>
            <td style="width:33.33%; padding:14px 20px; text-align:center; border-right:1px solid #e5e7eb; vertical-align:middle">
                <div class="summary-label">Total Credits</div>
                <div class="summary-value cr">+{{ number_format((float)$totalCredit, 2) }}</div>
            </td>
            <td style="width:33.33%; padding:14px 20px; text-align:center; border-right:1px solid #e5e7eb; vertical-align:middle">
                <div class="summary-label">Total Debits</div>
                <div class="summary-value dr">&#8722;{{ number_format((float)$totalDebit, 2) }}</div>
            </td>
            <td style="width:33.33%; padding:14px 20px; text-align:center; vertical-align:middle">
                <div class="summary-label">Closing Balance</div>
                <div class="summary-value" style="color:#1d4ed8">{{ $account->currency ?? 'NGN' }} {{ number_format((float)$account->available_balance, 2) }}</div>
            </td>
        </tr>
    </table>

    {{-- ===== FOOTER ===== --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="border-top:1px solid #e5e7eb; padding:0">
        <tr>
            <td style="padding:18px 24px; vertical-align:middle">
                <div class="footer-text">
                    <strong>{{ $tenant?->name ?? config('app.name') }}</strong><br>
                    @if($official ?? false)
                    This is an official account statement issued by {{ $tenant?->name ?? 'the bank' }}.<br>
                    @else
                    This is an unsigned statement for personal use only. Not valid for official purposes.<br>
                    @endif
                    Statement Reference: <strong>{{ $reference }}</strong> &nbsp;|&nbsp; Generated: {{ now()->format('d F Y \a\t H:i') }}<br>
                    This document is computer-generated and valid without a physical signature unless otherwise stated.
                </div>
            </td>
            <td style="padding:18px 24px; text-align:right; vertical-align:middle; width:120px">
                @if($official ?? false)
                <div class="stamp-outer">
                    <div class="stamp-inner">
                        <div class="stamp-text">OFFICIAL<br>STATEMENT<br>{{ now()->format('Y') }}<br>VERIFIED</div>
                    </div>
                </div>
                @else
                <div style="width:86px;height:86px;border:2px dashed #d1d5db;border-radius:43px;text-align:center;display:inline-block">
                    <div style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;line-height:1.7;color:#9ca3af;padding-top:28px">UNSIGNED<br>COPY</div>
                </div>
                @endif
            </td>
        </tr>
    </table>

@if(!($forPdf ?? false))
</div>{{-- /page-card --}}
</div>{{-- /screen-shell --}}
@endif

</body>
</html>
