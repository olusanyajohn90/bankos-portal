@extends('layouts.portal')
@section('title', 'Loan Top-Up Request')

@section('content')

<div style="display:flex;align-items:center;gap:12px;margin-bottom:28px">
    <a href="{{ route('loans.show', $loan->id) }}" style="width:34px;height:34px;border-radius:9px;border:1px solid #e5e7eb;background:white;display:flex;align-items:center;justify-content:center;color:#6b7280;flex-shrink:0;text-decoration:none">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#111827">Request Loan Top-Up</h1>
        <p style="font-size:12px;color:#9ca3af;margin-top:2px">Loan #{{ $loan->loan_number ?? substr($loan->id, 0, 8) }}</p>
    </div>
</div>

<div style="max-width:620px">

    @if(session('success'))
    <div style="margin-bottom:16px;padding:12px 16px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;display:flex;align-items:center;gap:10px">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <p style="font-size:13px;color:#15803d">{{ session('success') }}</p>
    </div>
    @endif
    @if($errors->any())
    <div style="margin-bottom:16px;padding:12px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px">
        @foreach($errors->all() as $err)
        <p style="font-size:13px;color:#991b1b;margin-bottom:2px">{{ $err }}</p>
        @endforeach
    </div>
    @endif

    {{-- Current loan summary --}}
    <div style="background:linear-gradient(135deg,#1e3a8a,#2563eb);border-radius:14px;padding:22px 24px;color:white;margin-bottom:16px;position:relative;overflow:hidden">
        <div style="position:absolute;top:-25px;right:-25px;width:110px;height:110px;border-radius:50%;background:rgba(255,255,255,0.06)"></div>
        <p style="font-size:10px;font-weight:700;color:rgba(255,255,255,0.6);text-transform:uppercase;letter-spacing:.08em;margin-bottom:14px">Current Loan Summary</p>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px">
            <div>
                <p style="font-size:10px;color:rgba(255,255,255,0.55);margin-bottom:4px;text-transform:uppercase;letter-spacing:.04em">Loan Number</p>
                <p style="font-size:14px;font-weight:800;font-family:monospace">{{ $loan->loan_number ?? '—' }}</p>
            </div>
            <div>
                <p style="font-size:10px;color:rgba(255,255,255,0.55);margin-bottom:4px;text-transform:uppercase;letter-spacing:.04em">Outstanding Balance</p>
                <p style="font-size:14px;font-weight:800">NGN {{ number_format((float)$loan->outstanding_balance, 2) }}</p>
            </div>
            <div>
                <p style="font-size:10px;color:rgba(255,255,255,0.55);margin-bottom:4px;text-transform:uppercase;letter-spacing:.04em">Original Principal</p>
                <p style="font-size:14px;font-weight:800">NGN {{ number_format((float)$loan->principal_amount, 2) }}</p>
            </div>
        </div>
    </div>

    {{-- Top-up form --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:24px;margin-bottom:14px">
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:20px">Top-Up Request Details</p>

        <form method="POST" action="{{ route('loans.topup.store', $loan->id) }}">
            @csrf

            <div style="margin-bottom:16px">
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:7px">Additional Amount (NGN) <span style="color:#dc2626">*</span></label>
                <div style="position:relative">
                    <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#6b7280;font-size:14px;font-weight:700">NGN</span>
                    <input type="number" name="amount" id="topup-amount"
                           value="{{ old('amount') }}"
                           min="1000" step="0.01" placeholder="0.00" required
                           style="width:100%;padding:12px 12px 12px 50px;border:1px solid #d1d5db;border-radius:9px;font-size:15px;font-weight:700;box-sizing:border-box;outline:none;color:#111827"
                           onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#d1d5db'">
                </div>
                <p style="font-size:11px;color:#9ca3af;margin-top:5px">Minimum top-up: NGN 1,000.00</p>
            </div>

            <div style="margin-bottom:20px">
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:7px">Purpose <span style="font-size:11px;font-weight:400;color:#9ca3af">(optional)</span></label>
                <textarea name="purpose" rows="3" maxlength="500"
                          placeholder="Briefly describe why you need the additional funds..."
                          style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none;resize:vertical;line-height:1.5"
                          onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#d1d5db'">{{ old('purpose') }}</textarea>
                <p style="font-size:11px;color:#9ca3af;margin-top:4px">Max 500 characters</p>
            </div>

            {{-- Important notice --}}
            <div style="margin-bottom:20px;padding:14px 16px;background:#fffbeb;border:1px solid #fde68a;border-radius:10px;display:flex;align-items:flex-start;gap:10px">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <div>
                    <p style="font-size:12px;font-weight:700;color:#92400e;margin-bottom:3px">Important Notice</p>
                    <p style="font-size:12px;color:#b45309;line-height:1.6">Top-up requests are subject to review and approval by our credit team. Funds will be disbursed to your linked account upon approval. Processing takes up to 2 business days.</p>
                </div>
            </div>

            <button type="submit"
                    style="width:100%;background:#2563eb;color:white;font-size:13px;font-weight:700;padding:12px 20px;border-radius:10px;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px"
                    onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                Submit Top-Up Request
            </button>
        </form>
    </div>

    {{-- Past top-up requests --}}
    @if($pastRequests->count() > 0)
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:24px">
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:16px">Previous Top-Up Requests</p>
        <div>
            @foreach($pastRequests as $req)
            @php
                $reqBg = match($req->status) {
                    'approved'     => '#f0fdf4',
                    'rejected'     => '#fef2f2',
                    'under_review' => '#eff6ff',
                    default        => '#fffbeb',
                };
                $reqColor = match($req->status) {
                    'approved'     => '#15803d',
                    'rejected'     => '#991b1b',
                    'under_review' => '#1d4ed8',
                    default        => '#92400e',
                };
                $reqBorder = match($req->status) {
                    'approved'     => '#bbf7d0',
                    'rejected'     => '#fecaca',
                    'under_review' => '#bfdbfe',
                    default        => '#fde68a',
                };
            @endphp
            <div style="display:flex;align-items:flex-start;justify-content:space-between;padding:14px 0;border-bottom:1px solid #f9fafb">
                <div>
                    <p style="font-size:14px;font-weight:700;color:#111827">NGN {{ number_format((float)$req->requested_amount, 2) }}</p>
                    @if($req->purpose)
                    <p style="font-size:12px;color:#9ca3af;margin-top:2px">{{ $req->purpose }}</p>
                    @endif
                    <p style="font-size:11px;color:#d1d5db;margin-top:2px">{{ \Carbon\Carbon::parse($req->created_at)->format('d M Y') }}</p>
                </div>
                <div style="text-align:right">
                    <span style="display:inline-block;font-size:10px;font-weight:700;padding:3px 10px;border-radius:20px;background:{{ $reqBg }};color:{{ $reqColor }};border:1px solid {{ $reqBorder }}">
                        {{ strtoupper(str_replace('_', ' ', $req->status)) }}
                    </span>
                    @if($req->admin_notes)
                    <p style="font-size:11px;color:#6b7280;margin-top:4px;max-width:180px;text-align:right;line-height:1.4">{{ $req->admin_notes }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
