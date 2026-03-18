@extends('layouts.portal')
@section('title', 'QR Payment')

@section('content')

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<div style="display:flex;align-items:center;gap:12px;margin-bottom:32px">
    <a href="{{ route('dashboard') }}"
       style="display:flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:9px;border:1px solid #e5e7eb;color:#6b7280;text-decoration:none;flex-shrink:0;background:white">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#111827;margin:0 0 3px">QR Payment</h1>
        <p style="font-size:13px;color:#6b7280;margin:0">Share your QR code to receive payments instantly</p>
    </div>
</div>

@if($accounts->isEmpty())
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:72px 24px;text-align:center">
    <div style="width:56px;height:56px;border-radius:50%;background:#f3f4f6;display:grid;place-items:center;margin:0 auto 16px">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="4" height="4"/></svg>
    </div>
    <p style="font-size:15px;font-weight:700;color:#374151;margin:0 0 5px">No active accounts</p>
    <p style="font-size:13px;color:#9ca3af;margin:0">You don't have any active accounts to generate a QR code for.</p>
</div>
@else

@php
$tenant = auth('customer')->user()->tenant;
$qrAccountsJson = $accounts->map(fn($a) => [
    'id'             => $a->id,
    'account_number' => $a->account_number,
    'account_name'   => $a->account_name,
    'account_type'   => $a->type ?? 'Savings',
    'bank_name'      => $tenant?->name ?? config('app.name', 'bankOS'),
])->values()->toJson();
@endphp
<script>
var QR_ACCOUNTS = {!! $qrAccountsJson !!};
</script>

{{-- Account Selector --}}
<div style="max-width:520px;margin:0 auto 28px">
    <label style="display:block;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px">Select Account</label>
    <select id="qr-account-select" onchange="switchAccount(this.value)"
            style="width:100%;padding:12px 14px;border:1px solid #d1d5db;border-radius:10px;font-size:14px;color:#111827;background:white;outline:none;box-shadow:0 1px 3px rgba(0,0,0,0.06)">
        @foreach($accounts as $acct)
        <option value="{{ $acct->id }}">{{ $acct->account_name }} — {{ $acct->account_number }}</option>
        @endforeach
    </select>
</div>

{{-- QR Card --}}
<div style="max-width:400px;margin:0 auto">
    <div style="background:white;border:1px solid #e5e7eb;border-radius:20px;box-shadow:0 4px 24px rgba(0,0,0,0.08);padding:36px 32px;text-align:center">

        <p id="qr-bank-name" style="font-size:11px;font-weight:800;color:#2563eb;text-transform:uppercase;letter-spacing:.12em;margin:0 0 24px">—</p>

        <div style="display:flex;justify-content:center;margin-bottom:24px">
            <div style="padding:16px;border:1px solid #e5e7eb;border-radius:14px;background:white;display:inline-block;box-shadow:0 2px 8px rgba(0,0,0,0.04)">
                <div id="qr-code-el" style="width:230px;height:230px;display:flex;align-items:center;justify-content:center">
                    <span style="font-size:12px;color:#9ca3af">Generating...</span>
                </div>
            </div>
        </div>

        <p id="qr-acct-name" style="font-size:17px;font-weight:800;color:#111827;margin:0 0 6px">—</p>
        <p id="qr-acct-number" style="font-size:22px;font-weight:800;color:#111827;font-family:monospace;letter-spacing:.18em;margin:0 0 5px">—</p>
        <p id="qr-acct-type" style="font-size:12px;color:#9ca3af;margin:0 0 28px">—</p>

        <div style="display:flex;gap:10px">
            <button onclick="downloadQr()"
                    style="flex:1;padding:12px 0;background:#2563eb;color:white;border:none;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:7px">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="8 17 12 21 16 17"/><line x1="12" y1="12" x2="12" y2="21"/><path d="M20.88 18.09A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.29"/></svg>
                Download
            </button>
            <button onclick="copyAccountNumber()" id="copy-btn"
                    style="flex:1;padding:12px 0;background:#f3f4f6;color:#374151;border:none;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:7px">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                Copy Number
            </button>
        </div>
    </div>

    <div style="margin-top:16px;padding:14px 18px;background:#fffbeb;border:1px solid #fde68a;border-radius:12px;display:flex;gap:10px;align-items:flex-start">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" style="flex-shrink:0;margin-top:1px"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        <p style="font-size:12px;color:#92400e;margin:0;line-height:1.6">
            Anyone who scans this QR code can see your account details to send you money. Do not share in untrusted environments.
        </p>
    </div>
</div>

@endif

<script>
(function () {
    if (typeof QR_ACCOUNTS === 'undefined' || !QR_ACCOUNTS.length) return;

    var accounts   = QR_ACCOUNTS;
    var currentId  = accounts[0].id;
    var qrInstance = null;

    function getAccount(id) {
        return accounts.find(function (a) { return a.id === id; }) || accounts[0];
    }

    function buildQrContent(acct) {
        return 'BANKOS|' + acct.account_number + '|' + acct.account_name + '|' + acct.bank_name;
    }

    function renderQr(acct) {
        var el = document.getElementById('qr-code-el');
        el.innerHTML = '';
        qrInstance = new QRCode(el, {
            text:         buildQrContent(acct),
            width:        230,
            height:       230,
            colorDark:    '#111827',
            colorLight:   '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });
        document.getElementById('qr-bank-name').textContent   = acct.bank_name;
        document.getElementById('qr-acct-name').textContent   = acct.account_name;
        document.getElementById('qr-acct-number').textContent = acct.account_number;
        document.getElementById('qr-acct-type').textContent   = acct.account_type + ' Account';
        currentId = acct.id;
    }

    window.switchAccount = function (id) { renderQr(getAccount(id)); };

    window.downloadQr = function () {
        var wrap   = document.getElementById('qr-code-el');
        var canvas = wrap.querySelector('canvas');
        if (!canvas) { alert('QR code not ready yet. Please wait a moment.'); return; }
        var acct = getAccount(currentId);
        var link = document.createElement('a');
        link.download = 'qr-' + acct.account_number + '.png';
        link.href     = canvas.toDataURL('image/png');
        link.click();
    };

    window.copyAccountNumber = function () {
        var acct = getAccount(currentId);
        var btn  = document.getElementById('copy-btn');
        var resetBtn = function () {
            btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg> Copy Number';
        };
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(acct.account_number).then(function () {
                btn.textContent = 'Copied!';
                setTimeout(resetBtn, 2000);
            });
        } else {
            var ta = document.createElement('textarea');
            ta.value = acct.account_number;
            ta.style.cssText = 'position:fixed;opacity:0';
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
            btn.textContent = 'Copied!';
            setTimeout(resetBtn, 2000);
        }
    };

    renderQr(accounts[0]);
}());
</script>

@endsection
