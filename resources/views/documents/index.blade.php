@extends('layouts.portal')
@section('title', 'Document Centre')

@section('content')

{{-- Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px">
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#111827">Document Centre</h1>
        <p style="font-size:12px;color:#9ca3af;margin-top:3px">Request and download official bank documents</p>
    </div>
</div>

@if(session('success'))
<div style="margin-bottom:18px;padding:12px 16px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;display:flex;align-items:center;gap:10px">
    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
    <p style="font-size:13px;color:#15803d">{{ session('success') }}</p>
</div>
@endif

{{-- Document type cards --}}
<div style="margin-bottom:28px">
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:14px">Available Documents</p>

    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px">
        @foreach($docTypes as $type => $info)
        @php
        $icons = [
            'confirmation_letter' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="15" y2="17"/>',
            'reference_letter'    => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>',
            'loan_clearance'      => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
        ];
        $iconPath = $icons[$type] ?? '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>';
        @endphp
        <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:22px;display:flex;flex-direction:column">
            {{-- Icon --}}
            <div style="width:46px;height:46px;border-radius:12px;background:#eff6ff;display:flex;align-items:center;justify-content:center;margin-bottom:14px">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $iconPath !!}</svg>
            </div>

            {{-- Title + desc --}}
            <p style="font-size:14px;font-weight:800;color:#111827;margin-bottom:6px">{{ $info['label'] }}</p>
            <p style="font-size:12px;color:#6b7280;line-height:1.6;flex:1;margin-bottom:14px">{{ $info['desc'] }}</p>

            {{-- Fee badge --}}
            @if($info['fee'] > 0)
            <div style="display:flex;align-items:center;gap:6px;margin-bottom:14px">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                <span style="font-size:12px;font-weight:700;color:#d97706">NGN {{ number_format($info['fee'], 2) }} fee</span>
            </div>
            @else
            <div style="display:flex;align-items:center;gap:6px;margin-bottom:14px">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <span style="font-size:12px;font-weight:700;color:#16a34a">Free</span>
            </div>
            @endif

            {{-- Request button --}}
            <a href="{{ route('documents.request', $type) }}"
               style="display:flex;align-items:center;justify-content:center;gap:7px;background:#2563eb;color:white;font-size:13px;font-weight:700;padding:10px;border-radius:9px;text-decoration:none"
               onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Request
            </a>
        </div>
        @endforeach
    </div>
</div>

{{-- Document history --}}
<div>
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:14px">Recent Requests</p>

    @if($documents->isEmpty())
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:48px 24px;text-align:center">
        <div style="width:52px;height:52px;border-radius:50%;background:#f3f4f6;display:flex;align-items:center;justify-content:center;margin:0 auto 14px">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        </div>
        <p style="font-size:13px;font-weight:600;color:#374151;margin-bottom:4px">No requests yet</p>
        <p style="font-size:12px;color:#9ca3af">Your requested documents will appear here</p>
    </div>
    @else
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);overflow:hidden">
        <table style="width:100%;border-collapse:collapse">
            <thead>
                <tr>
                    <th style="background:#f8fafc;padding:10px 18px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;border-bottom:2px solid #e5e7eb;text-align:left">Document</th>
                    <th style="background:#f8fafc;padding:10px 18px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;border-bottom:2px solid #e5e7eb;text-align:left">Reference</th>
                    <th style="background:#f8fafc;padding:10px 18px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;border-bottom:2px solid #e5e7eb;text-align:left">Date</th>
                    <th style="background:#f8fafc;padding:10px 18px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;border-bottom:2px solid #e5e7eb;text-align:right">Fee</th>
                    <th style="background:#f8fafc;padding:10px 18px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;border-bottom:2px solid #e5e7eb;text-align:center">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($documents as $doc)
                <tr onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background='white'">
                    <td style="padding:12px 18px;font-size:13px;color:#374151;border-bottom:1px solid #f3f4f6;font-weight:600">{{ $doc->label }}</td>
                    <td style="padding:12px 18px;font-size:12px;color:#6b7280;border-bottom:1px solid #f3f4f6;font-family:monospace">{{ $doc->reference }}</td>
                    <td style="padding:12px 18px;font-size:12px;color:#6b7280;border-bottom:1px solid #f3f4f6">{{ $doc->created_at->format('d M Y') }}</td>
                    <td style="padding:12px 18px;font-size:12px;border-bottom:1px solid #f3f4f6;text-align:right">
                        @if($doc->fee > 0)
                        <span style="color:#dc2626;font-weight:600">NGN {{ number_format($doc->fee, 2) }}</span>
                        @else
                        <span style="color:#16a34a;font-weight:600">Free</span>
                        @endif
                    </td>
                    <td style="padding:12px 18px;border-bottom:1px solid #f3f4f6;text-align:center">
                        <a href="{{ route('documents.download', $doc->id) }}"
                           style="display:inline-flex;align-items:center;gap:5px;font-size:12px;font-weight:700;color:#2563eb;text-decoration:none;padding:6px 14px;border:1px solid #bfdbfe;border-radius:7px;background:#eff6ff">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            Download
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
