@extends('layouts.portal')
@section('title', 'Referral Programme')

@section('content')

@php $referralLink = url('/register?ref=' . $customer->referral_code); @endphp

<div style="margin-bottom:28px">
    <h1 style="font-size:20px;font-weight:800;color:#111827;margin:0 0 4px">Referral Programme</h1>
    <p style="font-size:13px;color:#6b7280;margin:0">Earn NGN 500 for every friend you refer who opens an account</p>
</div>

{{-- Hero Banner --}}
<div style="background:linear-gradient(135deg,#5b21b6 0%,#7c3aed 60%,#8b5cf6 100%);border-radius:16px;padding:28px;color:white;margin-bottom:18px;position:relative;overflow:hidden">
    <div style="position:absolute;right:-30px;top:-30px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,0.06)"></div>
    <div style="position:absolute;right:60px;bottom:-50px;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,0.04)"></div>
    <div style="position:relative">
        <div style="margin-bottom:20px">
            <p style="font-size:13px;color:rgba(255,255,255,0.75);margin:0 0 6px;text-transform:uppercase;letter-spacing:.05em;font-weight:600">Referral Reward</p>
            <p style="font-size:36px;font-weight:900;margin:0 0 4px;line-height:1">NGN 500</p>
            <p style="font-size:13px;color:rgba(255,255,255,0.7);margin:0">for every friend who opens and activates an account</p>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px">
            <div style="background:rgba(255,255,255,0.12);border-radius:12px;padding:14px 16px">
                <p style="font-size:24px;font-weight:800;margin:0 0 3px">{{ $referrals->count() }}</p>
                <p style="font-size:11px;color:rgba(255,255,255,0.65);margin:0">Total Referrals</p>
            </div>
            <div style="background:rgba(255,255,255,0.12);border-radius:12px;padding:14px 16px">
                <p style="font-size:24px;font-weight:800;margin:0 0 3px">{{ $pendingCount }}</p>
                <p style="font-size:11px;color:rgba(255,255,255,0.65);margin:0">Pending</p>
            </div>
            <div style="background:rgba(255,255,255,0.12);border-radius:12px;padding:14px 16px">
                <p style="font-size:20px;font-weight:800;margin:0 0 3px">NGN {{ number_format($totalEarned, 0) }}</p>
                <p style="font-size:11px;color:rgba(255,255,255,0.65);margin:0">Total Earned</p>
            </div>
        </div>
    </div>
</div>

{{-- Referral Link --}}
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:22px;margin-bottom:14px">
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin:0 0 14px">Your Referral Link</p>

    <div style="display:flex;gap:10px;align-items:stretch;margin-bottom:16px">
        <div style="flex:1;background:#f9fafb;border:1px solid #e5e7eb;border-radius:9px;padding:11px 14px;font-size:12px;color:#374151;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-family:monospace">
            {{ $referralLink }}
        </div>
        <button onclick="navigator.clipboard.writeText('{{ $referralLink }}').then(function(){var b=this;b.textContent='Copied!';setTimeout(function(){b.textContent='Copy Link'},2000)}.bind(this))"
                style="padding:11px 20px;background:#2563eb;color:white;border:none;border-radius:9px;font-size:13px;font-weight:700;cursor:pointer;white-space:nowrap;flex-shrink:0">
            Copy Link
        </button>
    </div>

    <div style="display:flex;gap:10px;align-items:stretch">
        <div style="flex:1;background:#f3f4f6;border-radius:10px;padding:14px 16px">
            <p style="font-size:11px;color:#6b7280;margin:0 0 5px;font-weight:600;text-transform:uppercase;letter-spacing:.04em">Your Referral Code</p>
            <p style="font-size:22px;font-weight:900;color:#111827;letter-spacing:5px;margin:0;font-family:monospace">{{ $customer->referral_code }}</p>
        </div>
        <button onclick="if(navigator.share){navigator.share({title:'Join my bank!',text:'Open an account using my referral code and we both earn NGN 500!',url:'{{ $referralLink }}'})}else{navigator.clipboard.writeText('{{ $referralLink }}')}"
                style="padding:14px 20px;background:#f3f4f6;color:#374151;border:none;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;flex-shrink:0;display:flex;align-items:center;gap:7px">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
            Share
        </button>
    </div>
</div>

{{-- How It Works --}}
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:22px;margin-bottom:14px">
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin:0 0 16px">How It Works</p>
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px">
        @foreach([
            ['1','Share your link or code','Send your referral link or code to friends and family'],
            ['2','Friend opens an account','They sign up and activate their new account'],
            ['3','You both earn NGN 500','Reward is credited automatically once account is active'],
        ] as $step)
        <div style="text-align:center;padding:18px 14px;background:#f9fafb;border-radius:12px">
            <div style="width:30px;height:30px;border-radius:50%;background:#2563eb;color:white;font-size:12px;font-weight:800;display:grid;place-items:center;margin:0 auto 12px">{{ $step[0] }}</div>
            <p style="font-size:12px;font-weight:700;color:#111827;margin:0 0 5px">{{ $step[1] }}</p>
            <p style="font-size:11px;color:#9ca3af;line-height:1.5;margin:0">{{ $step[2] }}</p>
        </div>
        @endforeach
    </div>
</div>

{{-- Referral History --}}
@if($referrals->isNotEmpty())
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:22px">
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin:0 0 16px">Referral History</p>
    <div>
        @foreach($referrals as $ref)
        @php
            $sc = [
                'pending'    => ['color'=>'#d97706','bg'=>'#fffbeb'],
                'registered' => ['color'=>'#2563eb','bg'=>'#eff6ff'],
                'activated'  => ['color'=>'#059669','bg'=>'#ecfdf5'],
                'rewarded'   => ['color'=>'#15803d','bg'=>'#f0fdf4'],
            ][$ref->status] ?? ['color'=>'#6b7280','bg'=>'#f9fafb'];
        @endphp
        <div style="display:flex;justify-content:space-between;align-items:center;padding:13px 0;border-bottom:1px solid #f3f4f6">
            <div>
                <p style="font-size:13px;font-weight:700;color:#111827;margin:0 0 3px">{{ $ref->referee_name ?? $ref->referee_email ?? 'Anonymous Referee' }}</p>
                <p style="font-size:11px;color:#9ca3af;margin:0">{{ $ref->created_at->format('d M Y') }}</p>
            </div>
            <div style="display:flex;align-items:center;gap:10px">
                @if($ref->status === 'rewarded')
                <p style="font-size:13px;font-weight:800;color:#15803d;margin:0">+NGN {{ number_format($ref->reward_amount, 0) }}</p>
                @endif
                <span style="font-size:10px;font-weight:800;padding:4px 10px;border-radius:99px;background:{{ $sc['bg'] }};color:{{ $sc['color'] }};letter-spacing:.04em">{{ strtoupper($ref->status) }}</span>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

@endsection
