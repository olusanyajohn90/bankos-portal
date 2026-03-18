<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body {
    font-family: 'DejaVu Sans', sans-serif;
    font-size: 11px;
    color: #111827;
    background: #fff;
    padding: 0;
}

.dot-border-top, .dot-border-bottom {
    border-top: 2.5px dotted #111827;
    margin: 0 12px;
}
.dot-border-bottom { margin-top: 0; }

.header {
    padding: 20px 24px 16px;
    display: table;
    width: 100%;
}
.header-left  { display: table-cell; vertical-align: middle; }
.header-right { display: table-cell; vertical-align: middle; text-align: right; }
.brand-name   { font-size: 18px; font-weight: 800; color: #111827; letter-spacing: -0.3px; }
.brand-tagline{ font-size: 9px; color: #6b7280; margin-top: 2px; letter-spacing: 0.3px; }
.receipt-label{ font-size: 13px; font-weight: 700; color: #374151; }

.hero {
    text-align: center;
    padding: 24px 24px 20px;
}
.amount-value {
    font-size: 38px;
    font-weight: 900;
    letter-spacing: -1px;
    line-height: 1;
    margin-bottom: 8px;
}
.status-text {
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 6px;
}
.datetime-text {
    font-size: 11px;
    color: #6b7280;
}

.divider { border: none; border-top: 1px solid #e5e7eb; margin: 0 24px; }

.section { padding: 16px 24px; }
.section-title {
    font-size: 9px;
    font-weight: 700;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 10px;
}

.detail-row { display: table; width: 100%; margin-bottom: 9px; }
.detail-label {
    display: table-cell;
    font-size: 11px;
    color: #6b7280;
    vertical-align: top;
    width: 38%;
}
.detail-value {
    display: table-cell;
    font-size: 11px;
    font-weight: 700;
    color: #111827;
    text-align: right;
    vertical-align: top;
}
.detail-sub {
    display: block;
    font-size: 10px;
    font-weight: 400;
    color: #6b7280;
    margin-top: 1px;
}
.mono { font-family: 'DejaVu Sans Mono', monospace; font-size: 10px; }

.narration-box {
    background: #f9fafb;
    border-radius: 6px;
    padding: 10px 12px;
    font-size: 11px;
    color: #374151;
    line-height: 1.6;
    margin-top: 4px;
}

.footer {
    padding: 14px 24px 20px;
    text-align: center;
}
.footer-text {
    font-size: 9.5px;
    color: #9ca3af;
    line-height: 1.8;
}
</style>
</head>
<body>
@php
    $creditTypes = ['deposit', 'disbursement', 'interest', 'reversal'];
    $isCredit    = in_array($transaction->type, $creditTypes);
    $amtColor    = $isCredit ? '#16a34a' : '#111827';
    $amtPrefix   = $isCredit ? '+' : '';
    $statusOk    = in_array($transaction->status ?? '', ['completed', 'success', '']);
    $statusColor = $statusOk ? '#111827' : '#dc2626';
    $statusText  = match($transaction->status ?? '') {
        'completed', 'success', '' => 'Successful',
        'failed'                    => 'Failed',
        'reversed'                  => 'Reversed',
        default                     => ucfirst($transaction->status ?? 'Pending'),
    };
@endphp

{{-- Top dotted border --}}
<div class="dot-border-top"></div>

{{-- Header: brand left, "Transaction Receipt" right --}}
<table class="header" width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td class="header-left">
            <div class="brand-name">{{ $tenant->name ?? 'bankOS' }}</div>
            @if($tenant?->tagline ?? false)
            <div class="brand-tagline">{{ $tenant->tagline }}</div>
            @endif
        </td>
        <td class="header-right">
            <div class="receipt-label">Transaction Receipt</div>
        </td>
    </tr>
</table>

<hr class="divider">

{{-- Hero: amount + status + date --}}
<div class="hero">
    <div class="amount-value" style="color:{{ $amtColor }}">
        {{ $amtPrefix }}&#8358;{{ number_format((float)$transaction->amount, 2) }}
    </div>
    <div class="status-text" style="color:{{ $statusColor }}">{{ $statusText }}</div>
    <div class="datetime-text">
        {{ $transaction->created_at?->format('M jS, Y H:i:s') }}
    </div>
</div>

<hr class="divider">

{{-- Recipient / Beneficiary --}}
@if($transaction->recipient_name || $transaction->recipient_account || $transaction->recipient_bank)
<div class="section">
    <div class="section-title">Recipient Details</div>
    <div class="detail-row">
        <div class="detail-label">Recipient</div>
        <div class="detail-value">
            {{ strtoupper($transaction->recipient_name ?? '—') }}
            @if($transaction->recipient_bank || $transaction->recipient_account)
            <span class="detail-sub">
                {{ $transaction->recipient_bank ?? '' }}
                @if($transaction->recipient_bank && $transaction->recipient_account) | @endif
                {{ $transaction->recipient_account ?? '' }}
            </span>
            @endif
        </div>
    </div>
</div>
<hr class="divider">
@endif

{{-- Sender / Account --}}
<div class="section">
    <div class="section-title">Sender Details</div>
    <div class="detail-row">
        <div class="detail-label">Account Name</div>
        <div class="detail-value">
            {{ strtoupper($account->account_name) }}
            <span class="detail-sub">
                {{ $tenant->name ?? 'bankOS' }} | {{ $account->account_number }}
            </span>
        </div>
    </div>
</div>

<hr class="divider">

{{-- Transaction IDs --}}
<div class="section">
    <div class="detail-row">
        <div class="detail-label">Transaction No.</div>
        <div class="detail-value mono">{{ $transaction->reference }}</div>
    </div>
    @if($transaction->session_id ?? false)
    <div class="detail-row">
        <div class="detail-label">Session ID</div>
        <div class="detail-value mono">{{ $transaction->session_id }}</div>
    </div>
    @endif
    <div class="detail-row">
        <div class="detail-label">Type</div>
        <div class="detail-value">{{ ucwords(str_replace('_', ' ', $transaction->type ?? '')) }}</div>
    </div>
    @if($transaction->balance_after !== null)
    <div class="detail-row">
        <div class="detail-label">Balance After</div>
        <div class="detail-value" style="color:#2563eb">&#8358;{{ number_format((float)$transaction->balance_after, 2) }}</div>
    </div>
    @endif
    @if($transaction->fee ?? false)
    <div class="detail-row">
        <div class="detail-label">Fee</div>
        <div class="detail-value">&#8358;{{ number_format((float)$transaction->fee, 2) }}</div>
    </div>
    @endif
</div>

@if($transaction->description)
<hr class="divider">
<div class="section">
    <div class="section-title">Narration</div>
    <div class="narration-box">{{ $transaction->description }}</div>
</div>
@endif

<hr class="divider">

{{-- Footer --}}
<div class="footer">
    <div class="footer-text">
        {{ $tenant->name ?? 'bankOS' }} is licensed by the Central Bank of Nigeria.<br>
        This is a computer-generated receipt and requires no physical signature.<br>
        Unauthorised alteration of this document is a criminal offence.
    </div>
</div>

{{-- Bottom dotted border --}}
<div class="dot-border-bottom"></div>

</body>
</html>
