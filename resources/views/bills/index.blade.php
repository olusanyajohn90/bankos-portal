@extends('layouts.portal')
@section('title', 'Bill Payments')

@section('content')
<div style="margin-bottom:28px">
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Payments</p>
    <h1 style="font-size:20px;font-weight:800;color:#111827;margin:0">Bill Payments</h1>
</div>

@if(session('success'))
<div style="margin-bottom:16px;padding:12px 16px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;color:#15803d;font-size:13px;font-weight:500;display:flex;align-items:center;gap:8px">
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
    {{ session('success') }}
</div>
@endif

{{-- Category section label --}}
<p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:14px">Select Category</p>

{{-- Category Grid --}}
@php
$catIcons = [
    'electricity' => ['bg'=>'#fef9c3','stroke'=>'#ca8a04','svg'=>'<path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>'],
    'airtime'     => ['bg'=>'#eff6ff','stroke'=>'#2563eb','svg'=>'<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.42A2 2 0 0 1 3.58 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.5a16 16 0 0 0 6.29 6.29l.91-.91a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>'],
    'data'        => ['bg'=>'#f0fdf4','stroke'=>'#16a34a','svg'=>'<path d="M1 6l4-3 4 3M1 18l4 3 4-3M1 12h8M15 6l4-3 4 3M15 18l4 3 4-3M15 12h8"/>'],
    'cable'       => ['bg'=>'#fdf4ff','stroke'=>'#9333ea','svg'=>'<rect x="2" y="7" width="20" height="15" rx="2" ry="2"/><polyline points="17 2 12 7 7 2"/>'],
    'water'       => ['bg'=>'#eff6ff','stroke'=>'#0284c7','svg'=>'<path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/>'],
    'internet'    => ['bg'=>'#f0fdf4','stroke'=>'#059669','svg'=>'<circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>'],
    'insurance'   => ['bg'=>'#fef9c3','stroke'=>'#d97706','svg'=>'<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>'],
    'education'   => ['bg'=>'#fdf4ff','stroke'=>'#7c3aed','svg'=>'<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>'],
    'transport'   => ['bg'=>'#fff7ed','stroke'=>'#ea580c','svg'=>'<rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>'],
];
@endphp
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:12px;margin-bottom:32px">
    @foreach($categories as $key => $cat)
    @php $ci = $catIcons[$key] ?? ['bg'=>'#f3f4f6','stroke'=>'#6b7280','svg'=>'<circle cx="12" cy="12" r="10"/>']; @endphp
    <a href="{{ route('bills.category', $key) }}"
       style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:20px 12px;text-decoration:none;text-align:center;display:block;transition:border-color .15s"
       onmouseover="this.style.borderColor='#2563eb';this.style.boxShadow='0 2px 8px rgba(37,99,235,0.12)'"
       onmouseout="this.style.borderColor='#e5e7eb';this.style.boxShadow='0 1px 3px rgba(0,0,0,0.06)'">
        <div style="width:48px;height:48px;border-radius:14px;background:{{ $ci['bg'] }};display:flex;align-items:center;justify-content:center;margin:0 auto 12px auto">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="{{ $ci['stroke'] }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $ci['svg'] !!}</svg>
        </div>
        <p style="font-size:12px;font-weight:700;color:#111827;margin:0 0 3px 0;line-height:1.3">{{ $cat['label'] }}</p>
        <p style="font-size:10px;color:#9ca3af;margin:0">{{ count($cat['billers']) }} biller{{ count($cat['billers']) !== 1 ? 's' : '' }}</p>
    </a>
    @endforeach
</div>

{{-- Recent Bills --}}
@if($recent->isNotEmpty())
<p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px">Recent Payments</p>
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);overflow:hidden">
    @foreach($recent as $bill)
    @php $ci = $catIcons[$bill->category] ?? ['bg'=>'#f3f4f6','stroke'=>'#6b7280','svg'=>'<circle cx="12" cy="12" r="10"/>']; @endphp
    <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid #f3f4f6">
        <div style="display:flex;align-items:center;gap:12px">
            <div style="width:40px;height:40px;border-radius:12px;background:{{ $ci['bg'] }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="{{ $ci['stroke'] }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $ci['svg'] !!}</svg>
            </div>
            <div>
                <p style="font-size:13px;font-weight:600;color:#111827;margin:0">{{ $bill->biller }}</p>
                <p style="font-size:11px;color:#9ca3af;margin:2px 0 0 0">{{ $bill->recipient }} · {{ $bill->created_at->format('d M Y, H:i') }}</p>
            </div>
        </div>
        <div style="text-align:right">
            <p style="font-size:13px;font-weight:800;color:#dc2626;margin:0">−NGN {{ number_format($bill->amount, 2) }}</p>
            <p style="font-size:10px;font-weight:600;color:#15803d;margin:2px 0 0 0;text-transform:uppercase;letter-spacing:.03em">Paid</p>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection
