@extends('layouts.portal')
@section('title', 'Loan Applications')

@section('content')

{{-- Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px">
    <div style="display:flex;align-items:center;gap:12px">
        <a href="{{ route('loans') }}" style="width:34px;height:34px;border-radius:9px;border:1px solid #e5e7eb;background:white;display:flex;align-items:center;justify-content:center;color:#6b7280;flex-shrink:0;text-decoration:none">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        </a>
        <div>
            <h1 style="font-size:20px;font-weight:800;color:#111827">My Applications</h1>
            <p style="font-size:12px;color:#9ca3af;margin-top:2px">Track your loan application status</p>
        </div>
    </div>
    <a href="{{ route('loans.apply') }}"
       style="background:#2563eb;color:white;font-size:13px;font-weight:700;padding:11px 20px;border-radius:10px;text-decoration:none;display:flex;align-items:center;gap:7px"
       onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Apply Now
    </a>
</div>

@if($applications->isEmpty())
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:64px 24px;text-align:center">
    <div style="width:60px;height:60px;border-radius:50%;background:#f3f4f6;display:flex;align-items:center;justify-content:center;margin:0 auto 16px">
        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
    </div>
    <p style="font-size:15px;font-weight:700;color:#374151;margin-bottom:6px">No applications yet</p>
    <p style="font-size:13px;color:#9ca3af;margin-bottom:20px">Apply for a loan and track your progress here.</p>
    <a href="{{ route('loans.apply') }}"
       style="display:inline-flex;align-items:center;gap:6px;background:#2563eb;color:white;font-size:13px;font-weight:700;padding:11px 20px;border-radius:10px;text-decoration:none">
        Apply for a Loan
    </a>
</div>
@else
<div style="display:flex;flex-direction:column;gap:12px">
    @foreach($applications as $app)
    @php $sc = \App\Models\LoanApplication::$statusColors[$app->status] ?? ['#6b7280','#f9fafb','#e5e7eb']; @endphp
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:20px">

        {{-- Top row --}}
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:14px">
            <div>
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px">
                    <p style="font-size:15px;font-weight:800;color:#111827">{{ \App\Models\LoanApplication::$types[$app->loan_type]['label'] ?? ucfirst($app->loan_type) }}</p>
                    <span style="font-size:10px;font-weight:700;padding:3px 10px;border-radius:20px;background:{{ $sc[1] }};color:{{ $sc[0] }};border:1px solid {{ $sc[2] }}">{{ strtoupper(str_replace('_',' ',$app->status)) }}</span>
                </div>
                <p style="font-size:12px;color:#9ca3af">
                    Ref: <span style="font-family:monospace;font-weight:600;color:#6b7280">{{ $app->reference }}</span>
                    &middot; {{ $app->created_at->format('d M Y') }}
                </p>
            </div>
            <div style="text-align:right">
                <p style="font-size:18px;font-weight:800;color:#111827">NGN {{ number_format($app->requested_amount, 0) }}</p>
                <p style="font-size:12px;color:#9ca3af">{{ $app->requested_tenor_months }} months</p>
            </div>
        </div>

        {{-- Purpose --}}
        @if($app->purpose)
        <p style="font-size:12px;color:#6b7280;margin-bottom:12px;line-height:1.5">{{ Str::limit($app->purpose, 100) }}</p>
        @endif

        {{-- Status-specific actions/notes --}}
        @if(in_array($app->status, ['submitted','under_review']))
        <div style="display:flex;align-items:center;gap:10px;padding-top:12px;border-top:1px solid #f3f4f6">
            <div style="display:flex;align-items:center;gap:6px;flex:1">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <span style="font-size:12px;color:#d97706;font-weight:600">Under Review</span>
            </div>
            <form method="POST" action="{{ route('loans.applications.cancel', $app->id) }}" onsubmit="return confirm('Cancel this application?')">
                @csrf @method('DELETE')
                <button type="submit" style="font-size:12px;color:#dc2626;background:none;border:1px solid #fecaca;border-radius:7px;cursor:pointer;padding:5px 12px">Cancel Application</button>
            </form>
        </div>
        @elseif($app->status === 'approved' && $app->officer_notes)
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:9px;padding:12px 14px;display:flex;align-items:flex-start;gap:8px;margin-top:10px">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:1px"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <p style="font-size:12px;color:#15803d"><strong>Bank Note:</strong> {{ $app->officer_notes }}</p>
        </div>
        @elseif($app->status === 'rejected' && $app->officer_notes)
        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:9px;padding:12px 14px;display:flex;align-items:flex-start;gap:8px;margin-top:10px">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            <p style="font-size:12px;color:#991b1b"><strong>Reason:</strong> {{ $app->officer_notes }}</p>
        </div>
        @endif
    </div>
    @endforeach
</div>
@endif

@endsection
