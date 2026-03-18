@extends('layouts.portal')
@section('title', 'Group Savings (Ajo / Thrift)')

@section('content')
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px">
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#111827;margin:0 0 4px">Group Savings</h1>
        <p style="font-size:13px;color:#6b7280;margin:0">Ajo &middot; Thrift &middot; Esusu — rotating savings with your community</p>
    </div>
    <a href="{{ route('savings-groups.create') }}"
       style="background:#2563eb;color:white;font-size:13px;font-weight:700;padding:11px 20px;border-radius:10px;text-decoration:none;display:inline-flex;align-items:center;gap:7px;border:none">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Create Group
    </a>
</div>

@if(session('success'))
<div style="margin-bottom:18px;padding:13px 16px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;color:#15803d;font-size:13px;font-weight:500">
    {{ session('success') }}
</div>
@endif

@if($errors->any())
<div style="margin-bottom:18px;padding:13px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;color:#991b1b;font-size:13px;font-weight:500">
    {{ $errors->first() }}
</div>
@endif

{{-- My Groups --}}
<div style="margin-bottom:32px">
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:14px">My Groups</p>

    @if($myGroups->isEmpty())
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:40px;text-align:center">
        <div style="width:48px;height:48px;border-radius:12px;background:#f3f4f6;display:grid;place-items:center;margin:0 auto 14px">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <p style="font-size:14px;font-weight:700;color:#374151;margin-bottom:5px">You haven't joined any group yet</p>
        <p style="font-size:12px;color:#9ca3af">Create a new group or join an available one below.</p>
    </div>
    @else
    <div style="display:flex;flex-direction:column;gap:10px">
        @foreach($myGroups as $group)
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
        <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:18px 20px">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px">
                <div style="flex:1;min-width:0">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;flex-wrap:wrap">
                        <p style="font-size:15px;font-weight:700;color:#111827;margin:0">{{ $group->name }}</p>
                        <span style="font-size:10px;font-weight:700;padding:3px 9px;border-radius:99px;background:{{ $sc['bg'] }};color:{{ $sc['text'] }};border:1px solid {{ $sc['border'] }};letter-spacing:.02em">
                            {{ ucfirst($group->status) }}
                        </span>
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:16px;margin-bottom:12px">
                        <div>
                            <p style="font-size:10px;color:#9ca3af;margin:0 0 2px;text-transform:uppercase;letter-spacing:.04em">Contribution</p>
                            <p style="font-size:13px;font-weight:700;color:#111827;margin:0">NGN {{ number_format($group->contribution_amount, 2) }} <span style="font-weight:400;color:#6b7280">/ {{ $group->frequency }}</span></p>
                        </div>
                        <div>
                            <p style="font-size:10px;color:#9ca3af;margin:0 0 2px;text-transform:uppercase;letter-spacing:.04em">Members</p>
                            <p style="font-size:13px;font-weight:700;color:#111827;margin:0">{{ $group->member_count }}<span style="font-weight:400;color:#6b7280">/{{ $group->max_members }}</span></p>
                        </div>
                        <div>
                            <p style="font-size:10px;color:#9ca3af;margin:0 0 2px;text-transform:uppercase;letter-spacing:.04em">Cycle</p>
                            <p style="font-size:13px;font-weight:700;color:#111827;margin:0">{{ $group->current_cycle }}<span style="font-weight:400;color:#6b7280">/{{ $group->total_cycles }}</span></p>
                        </div>
                        @if($group->next_collection_date)
                        <div>
                            <p style="font-size:10px;color:#9ca3af;margin:0 0 2px;text-transform:uppercase;letter-spacing:.04em">Next Date</p>
                            <p style="font-size:13px;font-weight:700;color:#111827;margin:0">{{ \Carbon\Carbon::parse($group->next_collection_date)->format('d M Y') }}</p>
                        </div>
                        @endif
                    </div>
                    <div style="height:4px;background:#f3f4f6;border-radius:99px;overflow:hidden;max-width:320px">
                        <div style="height:100%;width:{{ $cyclePct }}%;background:#2563eb;border-radius:99px"></div>
                    </div>
                </div>
                <a href="{{ route('savings-groups.show', $group->id) }}"
                   style="font-size:12px;font-weight:700;color:#2563eb;text-decoration:none;padding:8px 16px;border:1px solid #bfdbfe;border-radius:9px;background:#eff6ff;white-space:nowrap;flex-shrink:0">
                    View
                </a>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- Available Groups --}}
<div>
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:14px">Join a Group</p>

    @if($availableGroups->isEmpty())
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:40px;text-align:center">
        <p style="font-size:14px;font-weight:700;color:#374151;margin-bottom:5px">No groups available to join</p>
        <p style="font-size:12px;color:#9ca3af">All groups are either full or you've already joined them. Create your own!</p>
    </div>
    @else
    <div style="display:flex;flex-direction:column;gap:10px">
        @foreach($availableGroups as $group)
        <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:18px 20px;display:flex;align-items:center;justify-content:space-between;gap:16px">
            <div style="flex:1;min-width:0">
                <p style="font-size:15px;font-weight:700;color:#111827;margin:0 0 5px">{{ $group->name }}</p>
                @if($group->description)
                <p style="font-size:12px;color:#6b7280;margin:0 0 10px">{{ Str::limit($group->description, 80) }}</p>
                @endif
                <div style="display:flex;flex-wrap:wrap;gap:14px">
                    <span style="font-size:12px;color:#6b7280">NGN {{ number_format($group->contribution_amount, 2) }} / {{ $group->frequency }}</span>
                    <span style="font-size:12px;color:#6b7280">{{ $group->member_count }}/{{ $group->max_members }} members</span>
                    <span style="font-size:12px;color:#15803d;font-weight:700">{{ $group->max_members - $group->member_count }} spot(s) left</span>
                </div>
            </div>
            <form method="POST" action="{{ route('savings-groups.join', $group->id) }}" style="flex-shrink:0">
                @csrf
                <button type="submit"
                        style="background:#2563eb;color:white;font-size:13px;font-weight:700;padding:10px 20px;border-radius:10px;border:none;cursor:pointer">
                    Join
                </button>
            </form>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
