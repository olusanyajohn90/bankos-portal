@extends('layouts.portal')
@section('title', 'Loan Calculator')

@section('content')

{{-- Header --}}
<div style="display:flex;align-items:center;gap:12px;margin-bottom:28px">
    <a href="{{ route('loans') }}" style="width:34px;height:34px;border-radius:9px;border:1px solid #e5e7eb;background:white;display:flex;align-items:center;justify-content:center;color:#6b7280;flex-shrink:0;text-decoration:none">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#111827">Loan Calculator</h1>
        <p style="font-size:12px;color:#9ca3af;margin-top:2px">Estimate your repayments before applying</p>
    </div>
</div>

<div style="display:grid;grid-template-columns:380px 1fr;gap:20px;align-items:start">

    {{-- LEFT: Input card --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:26px">
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:20px">Loan Parameters</p>

        {{-- Loan Amount --}}
        <div style="margin-bottom:18px">
            <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:7px">Loan Amount (NGN)</label>
            <div style="position:relative">
                <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:13px;font-weight:700;color:#6b7280">NGN</span>
                <input id="lc-amount" type="number" min="10000" step="1000" value="100000"
                       style="width:100%;box-sizing:border-box;padding:11px 12px 11px 46px;border:1px solid #d1d5db;border-radius:9px;font-size:14px;font-weight:700;color:#111827;outline:none"
                       onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#d1d5db'">
            </div>
        </div>

        {{-- Tenor --}}
        <div style="margin-bottom:18px">
            <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:7px">Tenor</label>
            <div style="display:flex;gap:8px">
                <input id="lc-tenor" type="number" min="1" step="1" value="12"
                       style="flex:1;padding:11px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:14px;font-weight:700;color:#111827;outline:none"
                       onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#d1d5db'">
                <div style="display:flex;border:1px solid #d1d5db;border-radius:9px;overflow:hidden;flex-shrink:0">
                    <button id="btn-months" onclick="setTenorUnit('months')"
                            style="padding:11px 14px;font-size:12px;font-weight:700;background:#2563eb;color:white;border:none;cursor:pointer">Months</button>
                    <button id="btn-years" onclick="setTenorUnit('years')"
                            style="padding:11px 14px;font-size:12px;font-weight:700;background:white;color:#374151;border:none;cursor:pointer">Years</button>
                </div>
            </div>
        </div>

        {{-- Interest Rate --}}
        <div style="margin-bottom:18px">
            <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:7px">Interest Rate (%)</label>
            <div style="position:relative">
                <input id="lc-rate" type="number" min="0.1" max="100" step="0.1" value="5"
                       style="width:100%;box-sizing:border-box;padding:11px 36px 11px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:14px;font-weight:700;color:#111827;outline:none"
                       onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#d1d5db'">
                <span style="position:absolute;right:12px;top:50%;transform:translateY(-50%);font-size:13px;font-weight:700;color:#6b7280">%</span>
            </div>
        </div>

        {{-- Rate Type --}}
        <div style="margin-bottom:18px">
            <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:7px">Rate Type</label>
            <div style="display:flex;border:1px solid #d1d5db;border-radius:9px;overflow:hidden">
                <button id="btn-per-month" onclick="setRateType('per_month')"
                        style="flex:1;padding:11px 0;font-size:12px;font-weight:700;background:#2563eb;color:white;border:none;cursor:pointer">Per Month</button>
                <button id="btn-per-annum" onclick="setRateType('per_annum')"
                        style="flex:1;padding:11px 0;font-size:12px;font-weight:700;background:white;color:#374151;border:none;cursor:pointer">Per Annum</button>
            </div>
        </div>

        {{-- Calculation Method --}}
        <div style="margin-bottom:24px">
            <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:7px">Calculation Method</label>
            <select id="lc-method"
                    style="width:100%;box-sizing:border-box;padding:11px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;color:#111827;outline:none;background:white"
                    onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#d1d5db'">
                <option value="reducing">Reducing Balance</option>
                <option value="flat">Flat Rate</option>
            </select>
        </div>

        <button onclick="calculate()"
                style="width:100%;padding:13px;background:#2563eb;color:white;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px"
                onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2"/><line x1="8" y1="6" x2="16" y2="6"/><line x1="8" y1="10" x2="16" y2="10"/><line x1="8" y1="14" x2="16" y2="14"/></svg>
            Calculate Repayment
        </button>

        <p style="font-size:11px;color:#9ca3af;margin-top:12px;text-align:center;line-height:1.5">Results are estimates only. Actual rates set by your lender.</p>
    </div>

    {{-- RIGHT: Results card --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:26px">

        {{-- Placeholder --}}
        <div id="lc-placeholder" style="display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:320px;text-align:center">
            <div style="width:60px;height:60px;border-radius:50%;background:#eff6ff;display:flex;align-items:center;justify-content:center;margin-bottom:16px">
                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2"/><line x1="8" y1="6" x2="16" y2="6"/><line x1="8" y1="10" x2="16" y2="10"/><line x1="8" y1="14" x2="16" y2="14"/></svg>
            </div>
            <p style="font-size:14px;font-weight:700;color:#374151;margin-bottom:6px">Enter loan details and calculate</p>
            <p style="font-size:12px;color:#9ca3af">Your repayment breakdown will appear here</p>
        </div>

        {{-- Results --}}
        <div id="lc-results" style="display:none">

            {{-- Monthly repayment — HERO --}}
            <div style="background:linear-gradient(135deg,#1e40af,#2563eb);border-radius:12px;padding:22px;text-align:center;margin-bottom:18px">
                <p style="font-size:11px;font-weight:700;color:rgba(255,255,255,0.7);text-transform:uppercase;letter-spacing:.08em;margin-bottom:8px">Monthly Repayment</p>
                <p id="res-monthly" style="font-size:36px;font-weight:800;color:white;letter-spacing:-0.5px;line-height:1">—</p>
                <p style="font-size:11px;color:rgba(255,255,255,0.6);margin-top:4px">per month for <span id="res-tenor-label">—</span></p>
            </div>

            {{-- Summary stats --}}
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-bottom:20px">
                <div style="padding:14px;background:#f8fafc;border-radius:10px;border:1px solid #e5e7eb">
                    <p style="font-size:10px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin-bottom:5px">Total Repayment</p>
                    <p id="res-total" style="font-size:14px;font-weight:800;color:#111827">—</p>
                    <p id="res-total-breakdown" style="font-size:10px;color:#9ca3af;margin-top:3px">—</p>
                </div>
                <div style="padding:14px;background:#fef2f2;border-radius:10px;border:1px solid #fecaca">
                    <p style="font-size:10px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin-bottom:5px">Total Interest</p>
                    <p id="res-interest" style="font-size:14px;font-weight:800;color:#dc2626">—</p>
                </div>
                <div style="padding:14px;background:#f8fafc;border-radius:10px;border:1px solid #e5e7eb">
                    <p style="font-size:10px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin-bottom:5px">EAR</p>
                    <p id="res-ear" style="font-size:14px;font-weight:800;color:#111827">—</p>
                </div>
            </div>

            {{-- Amortization table --}}
            <div style="margin-bottom:20px">
                <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px">Amortization Schedule</p>
                <div style="overflow:hidden;border:1px solid #e5e7eb;border-radius:10px">
                    <div style="max-height:300px;overflow-y:auto">
                        <table style="width:100%;border-collapse:collapse;font-size:12px">
                            <thead>
                                <tr>
                                    <th style="background:#f8fafc;padding:10px 12px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;border-bottom:2px solid #e5e7eb;text-align:right;white-space:nowrap;position:sticky;top:0">Mo.</th>
                                    <th style="background:#f8fafc;padding:10px 12px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;border-bottom:2px solid #e5e7eb;text-align:right;white-space:nowrap;position:sticky;top:0">Opening</th>
                                    <th style="background:#f8fafc;padding:10px 12px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;border-bottom:2px solid #e5e7eb;text-align:right;white-space:nowrap;position:sticky;top:0">Principal</th>
                                    <th style="background:#f8fafc;padding:10px 12px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;border-bottom:2px solid #e5e7eb;text-align:right;white-space:nowrap;position:sticky;top:0">Interest</th>
                                    <th style="background:#f8fafc;padding:10px 12px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;border-bottom:2px solid #e5e7eb;text-align:right;white-space:nowrap;position:sticky;top:0">Closing</th>
                                </tr>
                            </thead>
                            <tbody id="amort-tbody"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Apply CTA --}}
            <a href="{{ route('loans.apply') }}"
               style="display:flex;align-items:center;justify-content:center;gap:8px;padding:13px;background:#15803d;color:white;border-radius:10px;font-size:14px;font-weight:700;text-decoration:none"
               onmouseover="this.style.background='#166534'" onmouseout="this.style.background='#15803d'">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Apply for This Loan
            </a>
        </div>
    </div>

</div>

<script>
(function () {
    var tenorUnit = 'months';
    var rateType  = 'per_month';

    window.setTenorUnit = function (unit) {
        tenorUnit = unit;
        document.getElementById('btn-months').style.background = (unit === 'months') ? '#2563eb' : 'white';
        document.getElementById('btn-months').style.color      = (unit === 'months') ? 'white'   : '#374151';
        document.getElementById('btn-years').style.background  = (unit === 'years')  ? '#2563eb' : 'white';
        document.getElementById('btn-years').style.color       = (unit === 'years')  ? 'white'   : '#374151';
    };

    window.setRateType = function (type) {
        rateType = type;
        document.getElementById('btn-per-month').style.background = (type === 'per_month') ? '#2563eb' : 'white';
        document.getElementById('btn-per-month').style.color      = (type === 'per_month') ? 'white'   : '#374151';
        document.getElementById('btn-per-annum').style.background = (type === 'per_annum') ? '#2563eb' : 'white';
        document.getElementById('btn-per-annum').style.color      = (type === 'per_annum') ? 'white'   : '#374151';
    };

    function fmt(n) {
        return 'NGN ' + Number(n.toFixed(2)).toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    window.calculate = function () {
        var P      = parseFloat(document.getElementById('lc-amount').value) || 0;
        var tenor  = parseFloat(document.getElementById('lc-tenor').value)  || 0;
        var rate   = parseFloat(document.getElementById('lc-rate').value)   || 0;
        var method = document.getElementById('lc-method').value;

        if (P <= 0 || tenor <= 0 || rate <= 0) {
            alert('Please enter valid positive values for all fields.');
            return;
        }

        var monthlyRate = (rateType === 'per_month') ? (rate / 100) : (rate / 100) / 12;
        var n           = (tenorUnit === 'years') ? Math.round(tenor * 12) : Math.round(tenor);
        var monthlyPayment, totalInterest, schedule = [];

        if (method === 'reducing') {
            var factor = Math.pow(1 + monthlyRate, n);
            monthlyPayment = P * monthlyRate * factor / (factor - 1);
            var balance = P;
            for (var i = 1; i <= n; i++) {
                var intPart  = balance * monthlyRate;
                var prinPart = monthlyPayment - intPart;
                var closing  = Math.max(0, balance - prinPart);
                schedule.push({ month: i, opening: balance, principal: prinPart, interest: intPart, closing: closing });
                balance = closing;
            }
            totalInterest = monthlyPayment * n - P;
        } else {
            totalInterest  = P * monthlyRate * n;
            monthlyPayment = (P + totalInterest) / n;
            var flatPrin   = P / n;
            var flatInt    = (P * monthlyRate);
            var balance    = P;
            for (var i = 1; i <= n; i++) {
                var closing = Math.max(0, balance - flatPrin);
                schedule.push({ month: i, opening: balance, principal: flatPrin, interest: flatInt, closing: closing });
                balance = closing;
            }
        }

        var totalRepayment = monthlyPayment * n;
        var ear = (Math.pow(1 + monthlyRate, 12) - 1) * 100;

        document.getElementById('res-monthly').textContent         = fmt(monthlyPayment);
        document.getElementById('res-tenor-label').textContent     = n + ' months';
        document.getElementById('res-total').textContent           = fmt(totalRepayment);
        document.getElementById('res-total-breakdown').textContent = 'Principal ' + fmt(P);
        document.getElementById('res-interest').textContent        = fmt(totalInterest);
        document.getElementById('res-ear').textContent             = ear.toFixed(2) + '% p.a.';

        var tbody = document.getElementById('amort-tbody');
        tbody.innerHTML = '';
        schedule.forEach(function (row, idx) {
            var tr = document.createElement('tr');
            tr.style.background = idx % 2 === 0 ? 'white' : '#fafafa';
            tr.innerHTML =
                '<td style="padding:10px 12px;text-align:right;color:#6b7280;font-weight:700;border-bottom:1px solid #f3f4f6">' + row.month + '</td>' +
                '<td style="padding:10px 12px;text-align:right;color:#374151;border-bottom:1px solid #f3f4f6">' + fmt(row.opening) + '</td>' +
                '<td style="padding:10px 12px;text-align:right;color:#15803d;font-weight:600;border-bottom:1px solid #f3f4f6">' + fmt(row.principal) + '</td>' +
                '<td style="padding:10px 12px;text-align:right;color:#dc2626;border-bottom:1px solid #f3f4f6">' + fmt(row.interest) + '</td>' +
                '<td style="padding:10px 12px;text-align:right;color:#111827;font-weight:600;border-bottom:1px solid #f3f4f6">' + fmt(row.closing) + '</td>';
            tbody.appendChild(tr);
        });

        document.getElementById('lc-placeholder').style.display = 'none';
        document.getElementById('lc-results').style.display     = 'block';
    };
}());
</script>

@endsection
