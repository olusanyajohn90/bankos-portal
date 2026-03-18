@extends('layouts.portal')
@section('title', 'Loan #' . ($loan->loan_number ?? $loan->id))

@section('content')

{{-- Back + title --}}
<div style="display:flex;align-items:center;gap:12px;margin-bottom:28px">
    <a href="{{ route('loans') }}"
       style="width:36px;height:36px;border-radius:10px;background:white;border:1px solid #e5e7eb;display:flex;align-items:center;justify-content:center;color:#6b7280;text-decoration:none;flex-shrink:0">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#111827;margin-bottom:2px">Loan #{{ $loan->loan_number ?? substr($loan->id, 0, 8) }}</h1>
        <p style="font-size:13px;color:#6b7280">{{ ucfirst($loan->status) }} &middot; {{ ucfirst($loan->repayment_frequency ?? 'Monthly') }} repayment</p>
    </div>
</div>

@php
    $progress = $loan->principal_amount > 0
        ? max(0, min(100, (1 - $loan->outstanding_balance / $loan->principal_amount) * 100))
        : 0;
    $isActive = in_array($loan->status, ['active', 'overdue']);
    $statusColor = match($loan->status) {
        'active'      => '#15803d',
        'overdue'     => '#dc2626',
        'closed'      => '#6b7280',
        'pending'     => '#d97706',
        'approved'    => '#2563eb',
        default       => '#6b7280',
    };
    $statusBg = match($loan->status) {
        'active'      => '#f0fdf4',
        'overdue'     => '#fef2f2',
        'closed'      => '#f9fafb',
        'pending'     => '#fffbeb',
        'approved'    => '#eff6ff',
        default       => '#f9fafb',
    };
    $statusBorder = match($loan->status) {
        'active'      => '#bbf7d0',
        'overdue'     => '#fecaca',
        'closed'      => '#e5e7eb',
        'pending'     => '#fde68a',
        'approved'    => '#bfdbfe',
        default       => '#e5e7eb',
    };
@endphp

<div style="max-width:640px">

    {{-- Hero balance card --}}
    <div style="background:linear-gradient(135deg,#1e3a8a 0%,#2563eb 100%);border-radius:16px;padding:26px 28px;color:white;margin-bottom:16px;position:relative;overflow:hidden">
        <div style="position:absolute;top:-24px;right:-24px;width:130px;height:130px;border-radius:50%;background:rgba(255,255,255,0.06)"></div>
        <div style="position:absolute;bottom:-32px;right:60px;width:90px;height:90px;border-radius:50%;background:rgba(255,255,255,0.05)"></div>

        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:18px;position:relative">
            <div>
                <p style="color:rgba(191,219,254,0.8);font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Outstanding Balance</p>
                <p style="font-size:34px;font-weight:800;letter-spacing:-1px;line-height:1">
                    NGN {{ number_format((float)$loan->outstanding_balance, 2) }}
                </p>
                <p style="color:rgba(191,219,254,0.65);font-size:13px;margin-top:5px">
                    of NGN {{ number_format((float)$loan->principal_amount, 2) }} principal
                </p>
            </div>
            <span style="font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px;background:{{ $statusBg }};color:{{ $statusColor }};border:1px solid {{ $statusBorder }};flex-shrink:0">
                {{ strtoupper($loan->status) }}
            </span>
        </div>

        {{-- Repayment progress bar --}}
        @if($loan->principal_amount > 0)
        <div style="position:relative">
            <div style="display:flex;justify-content:space-between;font-size:12px;color:rgba(191,219,254,0.75);margin-bottom:7px">
                <span style="font-weight:700">{{ number_format($progress, 0) }}% repaid</span>
                <span>NGN {{ number_format((float)$loan->principal_amount - (float)$loan->outstanding_balance, 0) }} paid</span>
            </div>
            <div style="height:6px;border-radius:3px;background:rgba(255,255,255,0.2);overflow:hidden">
                <div style="height:100%;width:{{ $progress }}%;background:{{ $loan->status === 'overdue' ? '#fca5a5' : 'rgba(255,255,255,0.85)' }};border-radius:3px"></div>
            </div>
        </div>
        @endif
    </div>

    {{-- Overdue alert banner --}}
    @if($loan->status === 'overdue')
    <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:14px 16px;margin-bottom:16px;display:flex;align-items:center;gap:12px">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <p style="font-size:13px;color:#991b1b;font-weight:600">This loan is overdue. Please make a repayment immediately to avoid penalties.</p>
    </div>
    @endif

    {{-- Loan details grid --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:24px;margin-bottom:14px">
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:18px">Loan Details</p>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
            <div>
                <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Loan Number</p>
                <p style="font-size:14px;font-weight:700;color:#111827">{{ $loan->loan_number ?? '—' }}</p>
            </div>
            <div>
                <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Loan Type</p>
                <p style="font-size:14px;font-weight:700;color:#111827">{{ ucfirst(str_replace('_', ' ', $loan->type ?? 'Personal')) }}</p>
            </div>
            <div>
                <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Principal Amount</p>
                <p style="font-size:14px;font-weight:700;color:#111827">NGN {{ number_format((float)$loan->principal_amount, 2) }}</p>
            </div>
            <div>
                <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Interest Rate</p>
                <p style="font-size:14px;font-weight:700;color:#111827">{{ $loan->interest_rate ?? '—' }}% p.a.</p>
            </div>
            <div>
                <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Tenure</p>
                <p style="font-size:14px;font-weight:700;color:#111827">{{ $loan->tenure_days ?? '—' }} days</p>
            </div>
            <div>
                <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Repayment</p>
                <p style="font-size:14px;font-weight:700;color:#111827">{{ ucfirst($loan->repayment_frequency ?? 'Monthly') }}</p>
            </div>
            <div>
                <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Disbursed</p>
                <p style="font-size:14px;font-weight:700;color:#111827">{{ $loan->disbursed_at ? $loan->disbursed_at->format('d M Y') : '—' }}</p>
            </div>
            @if($loan->due_date)
            <div>
                <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Due Date</p>
                <p style="font-size:14px;font-weight:700;color:{{ $loan->status === 'overdue' ? '#dc2626' : '#111827' }}">{{ \Carbon\Carbon::parse($loan->due_date)->format('d M Y') }}</p>
            </div>
            @endif
            @if($loan->purpose)
            <div style="grid-column:1/-1">
                <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Purpose</p>
                <p style="font-size:13px;font-weight:500;color:#374151;line-height:1.5">{{ $loan->purpose }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Top-up button (active loans only) --}}
    @if($loan->status === 'active')
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:18px 24px;margin-bottom:14px;display:flex;align-items:center;justify-content:space-between">
        <div>
            <p style="font-size:14px;font-weight:700;color:#111827;margin-bottom:2px">Need more funds?</p>
            <p style="font-size:12px;color:#6b7280">Submit a top-up request for credit team review.</p>
        </div>
        <a href="{{ route('loans.topup', $loan->id) }}"
           style="display:inline-flex;align-items:center;gap:7px;background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d;font-size:13px;font-weight:700;padding:9px 16px;border-radius:9px;text-decoration:none;flex-shrink:0">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Request Top-Up
        </a>
    </div>
    @endif

    {{-- Repayment form (active / overdue) --}}
    @if($isActive)
    <div id="repay" style="background:white;border:2px solid #2563eb;border-radius:14px;padding:24px;margin-bottom:14px">
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:18px">Make a Repayment</p>

        @if($errors->any())
        <div style="margin-bottom:14px;padding:12px 14px;background:#fef2f2;border:1px solid #fecaca;border-radius:8px;color:#991b1b;font-size:13px;font-weight:600">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('loans.repay', $loan->id) }}">
            @csrf
            <div style="margin-bottom:14px">
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Debit Account</label>
                <select name="account_id" required style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;outline:none;background:white;box-sizing:border-box">
                    @foreach($accounts as $acct)
                    <option value="{{ $acct->id }}">{{ $acct->account_name }} — {{ $acct->account_number }} (NGN {{ number_format((float)$acct->available_balance, 2) }})</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-bottom:14px">
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Amount (NGN)</label>
                <input type="number" name="amount" id="repay-amount"
                       value="{{ number_format((float)$loan->outstanding_balance, 2, '.', '') }}"
                       min="1" step="0.01" max="{{ $loan->outstanding_balance }}"
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;outline:none;box-sizing:border-box" required>
                <p style="font-size:11px;color:#9ca3af;margin-top:5px">Max: NGN {{ number_format((float)$loan->outstanding_balance, 2) }}</p>
            </div>

            {{-- Quick-fill chips --}}
            @php $outstanding = (float)$loan->outstanding_balance; @endphp
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;margin-bottom:16px">
                <button type="button" onclick="document.getElementById('repay-amount').value='{{ number_format($outstanding * 0.25, 2, '.', '') }}'"
                        style="padding:9px;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:9px;font-size:12px;font-weight:700;color:#374151;cursor:pointer;line-height:1.3">
                    25%<br><span style="font-weight:500;color:#6b7280">NGN {{ number_format($outstanding * 0.25, 0) }}</span>
                </button>
                <button type="button" onclick="document.getElementById('repay-amount').value='{{ number_format($outstanding * 0.5, 2, '.', '') }}'"
                        style="padding:9px;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:9px;font-size:12px;font-weight:700;color:#374151;cursor:pointer;line-height:1.3">
                    50%<br><span style="font-weight:500;color:#6b7280">NGN {{ number_format($outstanding * 0.5, 0) }}</span>
                </button>
                <button type="button" onclick="document.getElementById('repay-amount').value='{{ number_format($outstanding, 2, '.', '') }}'"
                        style="padding:9px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:9px;font-size:12px;font-weight:700;color:#1d4ed8;cursor:pointer;line-height:1.3">
                    Full<br><span style="font-weight:500;color:#3b82f6">NGN {{ number_format($outstanding, 0) }}</span>
                </button>
            </div>

            <button type="submit"
                    style="width:100%;background:#2563eb;color:white;font-size:13px;font-weight:700;padding:13px;border-radius:10px;border:none;cursor:pointer">
                Process Repayment
            </button>
        </form>
    </div>

    {{-- Payoff calculator --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:24px;margin-bottom:14px">
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:18px">Payoff Calculator</p>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px">
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Monthly Payment (NGN)</label>
                <input type="number" id="calc-monthly" placeholder="e.g. 10,000" min="1"
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;outline:none;box-sizing:border-box"
                       oninput="calcPayoff()">
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Monthly Interest Rate (%)</label>
                <input type="number" id="calc-rate"
                       placeholder="{{ number_format(($loan->interest_rate ?? 0) / 12, 2) }}"
                       step="0.01" min="0"
                       value="{{ number_format(($loan->interest_rate ?? 0) / 12, 2) }}"
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;outline:none;box-sizing:border-box"
                       oninput="calcPayoff()">
            </div>
        </div>
        <div id="calc-result" style="background:#f9fafb;border-radius:12px;padding:16px;display:none">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div>
                    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Months to Payoff</p>
                    <p id="calc-months" style="font-size:20px;font-weight:800;color:#111827">—</p>
                </div>
                <div>
                    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Total Interest</p>
                    <p id="calc-interest" style="font-size:20px;font-weight:800;color:#dc2626">—</p>
                </div>
                <div>
                    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Total to Pay</p>
                    <p id="calc-total" style="font-size:20px;font-weight:800;color:#111827">—</p>
                </div>
                <div>
                    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Payoff By</p>
                    <p id="calc-date" style="font-size:16px;font-weight:700;color:#2563eb">—</p>
                </div>
            </div>
        </div>
        <p style="font-size:11px;color:#9ca3af;margin-top:10px">Enter your planned monthly payment to see how quickly you can clear this loan.</p>
    </div>
    @endif

    {{-- Repayment history --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:24px">
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:18px">Repayment History</p>
        @if($repayments->isEmpty())
        <p style="font-size:13px;color:#9ca3af;text-align:center;padding:28px 0">No repayments recorded yet.</p>
        @else
        <div>
            @foreach($repayments as $r)
            <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 0;border-bottom:1px solid #f3f4f6">
                <div style="display:flex;align-items:center;gap:12px">
                    <div style="width:36px;height:36px;border-radius:10px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div>
                        <p style="font-size:14px;font-weight:700;color:#111827">NGN {{ number_format((float)$r->amount, 2) }}</p>
                        <p style="font-size:12px;color:#9ca3af">{{ $r->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
                <div style="text-align:right">
                    <p style="font-size:11px;font-family:monospace;color:#6b7280;margin-bottom:4px">{{ $r->reference }}</p>
                    <span style="font-size:11px;font-weight:700;padding:3px 9px;border-radius:20px;background:#f0fdf4;color:#15803d">PAID</span>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>

<script>
function calcPayoff() {
    const monthly = parseFloat(document.getElementById('calc-monthly').value) || 0;
    const rateInput = parseFloat(document.getElementById('calc-rate').value) || 0;
    const balance = {{ (float)$loan->outstanding_balance }};
    const result = document.getElementById('calc-result');
    if (monthly <= 0) { result.style.display = 'none'; return; }
    const r = rateInput / 100;
    let bal = balance, months = 0, totalInterest = 0;
    if (r === 0) {
        months = Math.ceil(bal / monthly);
    } else {
        while (bal > 0 && months < 600) {
            const interest = bal * r;
            totalInterest += interest;
            bal = bal + interest - monthly;
            months++;
            if (bal < 0) bal = 0;
        }
        if (months >= 600) {
            document.getElementById('calc-months').textContent   = 'Payment too low';
            document.getElementById('calc-interest').textContent = '—';
            document.getElementById('calc-total').textContent    = '—';
            document.getElementById('calc-date').textContent     = '—';
            result.style.display = 'block';
            return;
        }
    }
    const payoffDate = new Date();
    payoffDate.setMonth(payoffDate.getMonth() + months);
    document.getElementById('calc-months').textContent   = months + ' months';
    document.getElementById('calc-interest').textContent = 'NGN ' + totalInterest.toLocaleString('en-NG', {minimumFractionDigits:2, maximumFractionDigits:2});
    document.getElementById('calc-total').textContent    = 'NGN ' + (balance + totalInterest).toLocaleString('en-NG', {minimumFractionDigits:2, maximumFractionDigits:2});
    document.getElementById('calc-date').textContent     = payoffDate.toLocaleDateString('en-NG', {month:'short', year:'numeric'});
    result.style.display = 'block';
}
</script>
@endsection
