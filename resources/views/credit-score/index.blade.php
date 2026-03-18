@extends('layouts.portal')
@section('title', 'Credit Score')

@section('content')

<div style="margin-bottom:28px">
    <h1 style="font-size:20px;font-weight:800;color:#111827;margin:0 0 4px">Credit Score</h1>
    <p style="font-size:13px;color:#6b7280;margin:0">Your internal bankOS creditworthiness score &middot; Updated every 24 hours</p>
</div>

@php
$gc   = $latest->grade_color;
$score = $latest->score;
$pct  = (($score - 300) / 550) * 100;
@endphp

{{-- Score Dial Card --}}
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:32px 28px;margin-bottom:16px;text-align:center">

    {{-- Semi-circle gauge --}}
    <div style="position:relative;width:200px;height:100px;margin:0 auto 24px;overflow:hidden">
        <svg width="200" height="100" viewBox="0 0 200 100">
            {{-- Track --}}
            <path d="M 12 100 A 88 88 0 0 1 188 100" fill="none" stroke="#f3f4f6" stroke-width="16" stroke-linecap="round"/>
            {{-- Fill --}}
            <path d="M 12 100 A 88 88 0 0 1 188 100" fill="none" stroke="{{ $gc[0] }}" stroke-width="16" stroke-linecap="round"
                  stroke-dasharray="{{ 276.5 * $pct / 100 }} 276.5"/>
        </svg>
        <div style="position:absolute;bottom:0;left:0;right:0;text-align:center">
            <p style="font-size:42px;font-weight:900;color:#111827;line-height:1;margin:0">{{ $score }}</p>
            <p style="font-size:13px;font-weight:800;color:{{ $gc[0] }};margin:3px 0 0;text-transform:uppercase;letter-spacing:.04em">{{ $gc[2] }}</p>
        </div>
    </div>

    {{-- Score bands --}}
    <div style="display:flex;justify-content:center;gap:6px;margin-bottom:18px">
        @foreach([['300','Poor','#dc2626'],['580','Fair','#d97706'],['670','Good','#2563eb'],['750','Excellent','#15803d']] as $band)
        @php $active = $score >= (int)$band[0]; @endphp
        <div style="text-align:center;padding:5px 10px;border-radius:8px;background:{{ $active ? $band[2] : '#f3f4f6' }};min-width:56px">
            <p style="font-size:10px;font-weight:700;color:{{ $active ? 'white' : '#9ca3af' }};margin:0">{{ $band[1] }}</p>
            <p style="font-size:9px;color:{{ $active ? 'rgba(255,255,255,0.8)' : '#9ca3af' }};margin:0">{{ $band[0] }}+</p>
        </div>
        @endforeach
    </div>

    <p style="font-size:12px;color:#9ca3af;margin:0">Last updated {{ $latest->created_at->diffForHumans() }}</p>
</div>

{{-- Score Breakdown --}}
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:22px;margin-bottom:16px">
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin:0 0 18px">Score Breakdown</p>
    @php
    $components = [
        ['label'=>'Payment History',    'score'=>$latest->payment_history_score, 'max'=>350, 'weight'=>'35%', 'desc'=>'On-time loan repayments'],
        ['label'=>'Credit Utilization', 'score'=>$latest->utilization_score,     'max'=>300, 'weight'=>'30%', 'desc'=>'Outstanding vs original loan amount'],
        ['label'=>'Account Age',        'score'=>$latest->account_age_score,      'max'=>150, 'weight'=>'15%', 'desc'=>'Length of banking relationship'],
        ['label'=>'Account Mix',        'score'=>$latest->account_mix_score,      'max'=>100, 'weight'=>'10%', 'desc'=>'Variety of products used'],
        ['label'=>'Activity',           'score'=>$latest->activity_score,         'max'=>100, 'weight'=>'10%', 'desc'=>'Recent transaction activity'],
    ];
    @endphp
    @foreach($components as $comp)
    @php
        $compPct = $comp['max'] > 0 ? ($comp['score'] / $comp['max']) * 100 : 0;
        $barColor = $compPct >= 75 ? '#15803d' : ($compPct >= 50 ? '#2563eb' : ($compPct >= 25 ? '#d97706' : '#dc2626'));
    @endphp
    <div style="margin-bottom:16px">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
            <div>
                <span style="font-size:13px;font-weight:700;color:#111827">{{ $comp['label'] }}</span>
                <span style="font-size:11px;color:#9ca3af;margin-left:6px;font-weight:500">{{ $comp['weight'] }}</span>
            </div>
            <div style="text-align:right">
                <span style="font-size:13px;font-weight:800;color:#111827">{{ $comp['score'] }}</span>
                <span style="font-size:11px;color:#9ca3af">/{{ $comp['max'] }}</span>
            </div>
        </div>
        <div style="height:7px;background:#f3f4f6;border-radius:99px;overflow:hidden;margin-bottom:4px">
            <div style="height:100%;width:{{ $compPct }}%;background:{{ $barColor }};border-radius:99px"></div>
        </div>
        <p style="font-size:11px;color:#9ca3af;margin:0">{{ $comp['desc'] }}</p>
    </div>
    @endforeach
</div>

{{-- Insights & Tips --}}
@if($latest->factors && count($latest->factors) > 0)
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:22px;margin-bottom:16px">
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin:0 0 14px">Insights &amp; Tips</p>
    <div style="display:flex;flex-direction:column;gap:8px">
        @foreach($latest->factors as $f)
        @php
            $fType = $f['type'] ?? 'neutral';
            $fStyle = [
                'positive' => ['bg'=>'#f0fdf4','border'=>'#bbf7d0','color'=>'#166534','dot'=>'#16a34a'],
                'negative' => ['bg'=>'#fef2f2','border'=>'#fecaca','color'=>'#991b1b','dot'=>'#dc2626'],
                'neutral'  => ['bg'=>'#eff6ff','border'=>'#bfdbfe','color'=>'#1d4ed8','dot'=>'#2563eb'],
            ][$fType] ?? ['bg'=>'#f9fafb','border'=>'#e5e7eb','color'=>'#374151','dot'=>'#6b7280'];
        @endphp
        <div style="display:flex;align-items:flex-start;gap:10px;padding:12px 14px;background:{{ $fStyle['bg'] }};border:1px solid {{ $fStyle['border'] }};border-radius:10px">
            <div style="width:7px;height:7px;border-radius:50%;background:{{ $fStyle['dot'] }};flex-shrink:0;margin-top:4px"></div>
            <p style="font-size:12px;color:{{ $fStyle['color'] }};line-height:1.5;margin:0">{{ $f['text'] }}</p>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Score History --}}
@if($history->count() > 1)
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:22px">
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin:0 0 16px">Score History</p>
    <div style="display:flex;align-items:flex-end;gap:6px;height:72px">
        @foreach($history->reverse() as $h)
        @php
            $ht = max(12, (($h->score - 300) / 550) * 72);
            $isLatest = $h->id === $latest->id;
        @endphp
        <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:5px" title="{{ $h->score }} — {{ $h->created_at->format('d M Y') }}">
            <div style="width:100%;background:{{ $isLatest ? '#2563eb' : '#bfdbfe' }};border-radius:4px 4px 0 0;height:{{ $ht }}px"></div>
            <p style="font-size:9px;color:#9ca3af;writing-mode:vertical-rl;transform:rotate(180deg);line-height:1;margin:0">{{ $h->created_at->format('d M') }}</p>
        </div>
        @endforeach
    </div>
</div>
@endif

@endsection
