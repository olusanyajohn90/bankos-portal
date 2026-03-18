@extends('layouts.portal')
@section('title', $docType['label'])

@section('content')

<div style="display:flex;align-items:center;gap:12px;margin-bottom:28px">
    <a href="{{ route('documents') }}" style="width:34px;height:34px;border-radius:9px;border:1px solid #e5e7eb;background:white;display:flex;align-items:center;justify-content:center;color:#6b7280;flex-shrink:0;text-decoration:none">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#111827">{{ $docType['label'] }}</h1>
        <p style="font-size:12px;color:#9ca3af;margin-top:2px">{{ $docType['desc'] }}</p>
    </div>
</div>

@if($errors->any())
<div style="margin-bottom:16px;padding:12px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;display:flex;align-items:center;gap:10px">
    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <p style="font-size:13px;color:#991b1b">{{ $errors->first() }}</p>
</div>
@endif

<div style="max-width:520px">

    {{-- Document summary card --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:22px;margin-bottom:14px">
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:18px">
            <div style="width:50px;height:50px;border-radius:13px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="15" y2="17"/></svg>
            </div>
            <div>
                <p style="font-size:15px;font-weight:800;color:#111827;margin-bottom:3px">{{ $docType['label'] }}</p>
                <p style="font-size:12px;color:#6b7280;line-height:1.5">{{ $docType['desc'] }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('documents.generate', $type) }}">
            @csrf

            {{-- Account selector --}}
            <div style="margin-bottom:16px">
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:7px">Select Account <span style="color:#dc2626">*</span></label>
                <select name="account_id" required
                        style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none;background:white"
                        onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#d1d5db'">
                    @foreach($accounts as $acc)
                    <option value="{{ $acc->id }}">{{ $acc->account_name }} &mdash; {{ $acc->account_number }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Fee / free notice --}}
            @if($docType['fee'] > 0)
            <div style="padding:14px 16px;background:#fffbeb;border:1px solid #fde68a;border-radius:10px;display:flex;align-items:flex-start;gap:10px;margin-bottom:18px">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <div>
                    <p style="font-size:12px;font-weight:700;color:#92400e;margin-bottom:2px">Document Fee</p>
                    <p style="font-size:12px;color:#b45309;line-height:1.5">A fee of <strong>NGN {{ number_format($docType['fee'], 2) }}</strong> will be deducted from your selected account upon generation.</p>
                </div>
            </div>
            @else
            <div style="padding:14px 16px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;display:flex;align-items:center;gap:10px;margin-bottom:18px">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <p style="font-size:12px;color:#15803d;font-weight:600">This document is <strong>free</strong> to generate.</p>
            </div>
            @endif

            {{-- Delivery note --}}
            <div style="padding:12px 14px;background:#f8fafc;border-radius:9px;display:flex;align-items:center;gap:10px;margin-bottom:20px">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                <p style="font-size:12px;color:#6b7280">A copy will also be emailed to your registered email address.</p>
            </div>

            <button type="submit"
                    style="width:100%;background:#2563eb;color:white;font-size:13px;font-weight:700;padding:12px 20px;border-radius:10px;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px"
                    onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Generate &amp; Download PDF
            </button>
        </form>
    </div>

</div>
@endsection
