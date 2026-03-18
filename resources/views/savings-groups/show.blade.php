@extends('layouts.portal')
@section('title', $group->name)

@section('content')
<div style="display:flex;align-items:center;gap:12px;margin-bottom:28px">
    <a href="{{ route('savings-groups') }}"
       style="display:flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:9px;border:1px solid #e5e7eb;color:#6b7280;text-decoration:none;flex-shrink:0;background:white">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#111827;margin:0 0 3px">{{ $group->name }}</h1>
        <p style="font-size:13px;color:#6b7280;margin:0">Group Savings</p>
    </div>
</div>

@foreach(['success','error'] as $key)
@if(session($key))
<div style="margin-bottom:16px;padding:13px 16px;background:{{ $key==='success'?'#f0fdf4':'#fef2f2' }};border:1px solid {{ $key==='success'?'#bbf7d0':'#fecaca' }};border-radius:10px;color:{{ $key==='success'?'#15803d':'#991b1b' }};font-size:13px;font-weight:500">
    {{ session($key) }}
</div>
@endif
@endforeach

@if($errors->any())
<div style="margin-bottom:16px;padding:13px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;color:#991b1b;font-size:13px;font-weight:500">
    {{ $errors->first() }}
</div>
@endif

{{-- Group Hero Card --}}
@php
    $statusColors = [
        'forming'   => ['bg'=>'#eff6ff','text'=>'#1d4ed8','border'=>'#bfdbfe'],
        'active'    => ['bg'=>'#f0fdf4','text'=>'#15803d','border'=>'#bbf7d0'],
        'completed' => ['bg'=>'#f9fafb','text'=>'#374151','border'=>'#e5e7eb'],
        'cancelled' => ['bg'=>'#fef2f2','text'=>'#991b1b','border'=>'#fecaca'],
    ];
    $sc = $statusColors[$group->status] ?? $statusColors['forming'];
    $cyclePct = $group->total_cycles > 0 ? round(($group->current_cycle / $group->total_cycles) * 100) : 0;
@endphp

<div style="background:linear-gradient(135deg,#1e3a8a 0%,#1d4ed8 60%,#2563eb 100%);border-radius:16px;padding:26px 28px;color:white;margin-bottom:20px;position:relative;overflow:hidden">
    <div style="position:absolute;right:-30px;top:-30px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,0.05)"></div>
    <div style="position:absolute;right:60px;bottom:-50px;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,0.03)"></div>

    <div style="position:relative">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px">
            <div>
                <p style="font-size:11px;color:rgba(191,219,254,0.8);text-transform:uppercase;letter-spacing:.06em;margin:0 0 6px">Contribution Per Cycle</p>
                <p style="font-size:30px;font-weight:800;margin:0;line-height:1">NGN {{ number_format($group->contribution_amount, 2) }}</p>
                <p style="font-size:13px;color:rgba(191,219,254,0.75);margin:4px 0 0">per member &middot; {{ $group->frequency }}</p>
            </div>
            <span style="font-size:11px;font-weight:700;padding:5px 12px;border-radius:99px;background:rgba(255,255,255,0.15);color:white;letter-spacing:.03em;flex-shrink:0">
                {{ ucfirst($group->status) }}
            </span>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-bottom:18px">
            <div style="background:rgba(255,255,255,0.1);border-radius:10px;padding:13px;text-align:center">
                <p style="font-size:10px;color:rgba(191,219,254,0.7);margin:0 0 4px;text-transform:uppercase;letter-spacing:.04em">Cycle</p>
                <p style="font-size:20px;font-weight:800;margin:0">{{ $group->current_cycle }}/{{ $group->total_cycles }}</p>
            </div>
            <div style="background:rgba(255,255,255,0.1);border-radius:10px;padding:13px;text-align:center">
                <p style="font-size:10px;color:rgba(191,219,254,0.7);margin:0 0 4px;text-transform:uppercase;letter-spacing:.04em">Members</p>
                <p style="font-size:20px;font-weight:800;margin:0">{{ $members->count() }}/{{ $group->max_members }}</p>
            </div>
            <div style="background:rgba(255,255,255,0.1);border-radius:10px;padding:13px;text-align:center">
                <p style="font-size:10px;color:rgba(191,219,254,0.7);margin:0 0 4px;text-transform:uppercase;letter-spacing:.04em">Pot Size</p>
                <p style="font-size:16px;font-weight:800;margin:0">NGN {{ number_format($group->contribution_amount * $members->count(), 0) }}</p>
            </div>
        </div>

        <div style="height:4px;background:rgba(255,255,255,0.15);border-radius:99px;overflow:hidden;margin-bottom:8px">
            <div style="height:100%;width:{{ $cyclePct }}%;background:rgba(255,255,255,0.7);border-radius:99px"></div>
        </div>

        @if($group->next_collection_date)
        <p style="font-size:12px;color:rgba(191,219,254,0.8);margin:0">
            Next collection: <strong>{{ \Carbon\Carbon::parse($group->next_collection_date)->format('d F Y') }}</strong>
        </p>
        @endif

        @if($group->description)
        <p style="font-size:12px;color:rgba(191,219,254,0.65);margin:8px 0 0;font-style:italic">{{ $group->description }}</p>
        @endif
    </div>
</div>

{{-- Members Table --}}
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);overflow:hidden;margin-bottom:18px">
    <div style="padding:16px 20px;border-bottom:1px solid #f3f4f6">
        <p style="font-size:13px;font-weight:700;color:#111827;margin:0">Members</p>
    </div>
    @if($members->isEmpty())
    <p style="text-align:center;padding:28px;color:#9ca3af;font-size:13px;margin:0">No members yet.</p>
    @else
    <div style="overflow-x:auto">
        <table style="width:100%;border-collapse:collapse">
            <thead>
                <tr style="background:#f9fafb">
                    <th style="padding:10px 20px;font-size:11px;font-weight:700;color:#6b7280;text-align:left;text-transform:uppercase;letter-spacing:.05em">#</th>
                    <th style="padding:10px 16px;font-size:11px;font-weight:700;color:#6b7280;text-align:left;text-transform:uppercase;letter-spacing:.05em">Name</th>
                    <th style="padding:10px 16px;font-size:11px;font-weight:700;color:#6b7280;text-align:center;text-transform:uppercase;letter-spacing:.05em">Position</th>
                    <th style="padding:10px 16px;font-size:11px;font-weight:700;color:#6b7280;text-align:center;text-transform:uppercase;letter-spacing:.05em">Status</th>
                    <th style="padding:10px 16px;font-size:11px;font-weight:700;color:#6b7280;text-align:center;text-transform:uppercase;letter-spacing:.05em">Contributions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($members as $i => $member)
                <tr style="border-top:1px solid #f3f4f6">
                    <td style="padding:13px 20px;font-size:13px;color:#9ca3af;font-weight:600">{{ $i + 1 }}</td>
                    <td style="padding:13px 16px">
                        <p style="font-size:13px;font-weight:700;color:#111827;margin:0 0 2px">
                            {{ $member->first_name }} {{ $member->last_name }}
                            @if($myMembership && $member->customer_id === $myMembership->customer_id)
                            <span style="font-size:10px;color:#2563eb;font-weight:700;margin-left:4px">You</span>
                            @endif
                        </p>
                        <p style="font-size:11px;color:#9ca3af;margin:0">{{ $member->email }}</p>
                    </td>
                    <td style="padding:13px 16px;text-align:center;font-size:14px;color:#374151;font-weight:700">
                        {{ $member->payout_position ?? '—' }}
                    </td>
                    <td style="padding:13px 16px;text-align:center">
                        @php
                            $msc = [
                                'active'    => ['#f0fdf4','#15803d'],
                                'defaulted' => ['#fef2f2','#991b1b'],
                                'withdrawn' => ['#f9fafb','#6b7280'],
                            ];
                            [$mbg, $mtxt] = $msc[$member->status] ?? ['#f9fafb','#6b7280'];
                        @endphp
                        <span style="font-size:11px;font-weight:700;padding:3px 9px;border-radius:99px;background:{{ $mbg }};color:{{ $mtxt }}">
                            {{ ucfirst($member->status) }}
                        </span>
                    </td>
                    <td style="padding:13px 16px;text-align:center;font-size:14px;font-weight:700;color:#374151">
                        {{ $member->contributions_count }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- Contribute Section --}}
@if($myMembership && $myMembership->status === 'active' && $group->status === 'active')
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:22px;margin-bottom:18px">
    <div style="margin-bottom:18px">
        <p style="font-size:13px;font-weight:700;color:#111827;margin:0 0 4px">Pay This Cycle</p>
        <p style="font-size:12px;color:#6b7280;margin:0">Cycle {{ $group->current_cycle }} &mdash; NGN {{ number_format($group->contribution_amount, 2) }} due</p>
    </div>

    <form method="POST" action="{{ route('savings-groups.contribute', $group->id) }}">
        @csrf
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px">
            <div>
                <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:7px">
                    Debit Account <span style="color:#dc2626">*</span>
                </label>
                <select name="account_id" required
                        style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;outline:none;background:white;box-sizing:border-box;color:#111827">
                    <option value="">— Select account —</option>
                    @foreach($accounts as $acc)
                    <option value="{{ $acc->id }}" {{ $myMembership->account_id === $acc->id ? 'selected' : '' }}>
                        {{ $acc->account_number }} — {{ $acc->account_name }}
                        (NGN {{ number_format($acc->available_balance, 2) }})
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:7px">
                    Transaction PIN <span style="color:#dc2626">*</span>
                </label>
                <input type="password" name="pin" maxlength="4" pattern="\d{4}" required
                       placeholder="••••" inputmode="numeric"
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:22px;letter-spacing:10px;outline:none;box-sizing:border-box;text-align:center;color:#111827">
            </div>
        </div>

        <button type="submit"
                style="width:100%;background:#15803d;color:white;font-size:13px;font-weight:700;padding:12px;border-radius:10px;border:none;cursor:pointer">
            Pay NGN {{ number_format($group->contribution_amount, 2) }} for Cycle {{ $group->current_cycle }}
        </button>
    </form>
</div>
@endif

{{-- Leave group --}}
@if($myMembership && $group->status === 'forming')
<div style="margin-bottom:18px">
    <form method="POST" action="{{ route('savings-groups.leave', $group->id) }}"
          onsubmit="return confirm('Are you sure you want to leave this group?')">
        @csrf @method('DELETE')
        <button type="submit"
                style="background:white;border:1px solid #fecaca;color:#dc2626;font-size:12px;font-weight:700;padding:9px 18px;border-radius:9px;cursor:pointer">
            Leave Group
        </button>
    </form>
</div>
@endif

{{-- Contribution History --}}
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);overflow:hidden">
    <div style="padding:16px 20px;border-bottom:1px solid #f3f4f6">
        <p style="font-size:13px;font-weight:700;color:#111827;margin:0">Contribution History</p>
    </div>

    @if($contributions->isEmpty())
    <p style="text-align:center;padding:36px;color:#9ca3af;font-size:13px;margin:0">No contributions yet.</p>
    @else
    <div style="overflow-x:auto">
        <table style="width:100%;border-collapse:collapse">
            <thead>
                <tr style="background:#f9fafb">
                    <th style="padding:10px 20px;font-size:11px;font-weight:700;color:#6b7280;text-align:left;text-transform:uppercase;letter-spacing:.05em">Member</th>
                    <th style="padding:10px 16px;font-size:11px;font-weight:700;color:#6b7280;text-align:center;text-transform:uppercase;letter-spacing:.05em">Cycle</th>
                    <th style="padding:10px 16px;font-size:11px;font-weight:700;color:#6b7280;text-align:right;text-transform:uppercase;letter-spacing:.05em">Amount</th>
                    <th style="padding:10px 16px;font-size:11px;font-weight:700;color:#6b7280;text-align:center;text-transform:uppercase;letter-spacing:.05em">Status</th>
                    <th style="padding:10px 16px;font-size:11px;font-weight:700;color:#6b7280;text-align:left;text-transform:uppercase;letter-spacing:.05em">Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contributions as $contrib)
                <tr style="border-top:1px solid #f3f4f6">
                    <td style="padding:13px 20px">
                        <p style="font-size:13px;color:#111827;font-weight:600;margin:0 0 2px">{{ $contrib->first_name }} {{ $contrib->last_name }}</p>
                        <p style="font-size:11px;color:#9ca3af;font-family:monospace;margin:0">{{ $contrib->reference }}</p>
                    </td>
                    <td style="padding:13px 16px;text-align:center;font-size:14px;font-weight:700;color:#374151">{{ $contrib->cycle_number }}</td>
                    <td style="padding:13px 16px;text-align:right;font-size:13px;font-weight:800;color:#111827">
                        NGN {{ number_format($contrib->amount, 2) }}
                    </td>
                    <td style="padding:13px 16px;text-align:center">
                        @php
                            $csc = [
                                'paid'    => ['#f0fdf4','#15803d'],
                                'pending' => ['#fffbeb','#92400e'],
                                'failed'  => ['#fef2f2','#991b1b'],
                            ];
                            [$cbg,$ctxt] = $csc[$contrib->status] ?? ['#f9fafb','#6b7280'];
                        @endphp
                        <span style="font-size:11px;font-weight:700;padding:3px 9px;border-radius:99px;background:{{ $cbg }};color:{{ $ctxt }}">
                            {{ ucfirst($contrib->status) }}
                        </span>
                    </td>
                    <td style="padding:13px 16px;font-size:12px;color:#6b7280">
                        {{ $contrib->paid_at ? \Carbon\Carbon::parse($contrib->paid_at)->format('d M Y, H:i') : '—' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
