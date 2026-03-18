@extends('layouts.portal')
@section('title', 'Notifications')

@section('content')
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px">
    <div>
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Inbox</p>
        <h1 style="font-size:20px;font-weight:800;color:#111827;margin:0">
            Notifications
            @if($unread > 0)
            <span style="font-size:13px;font-weight:700;background:#2563eb;color:white;padding:2px 10px;border-radius:20px;margin-left:8px;vertical-align:middle">{{ $unread }}</span>
            @endif
        </h1>
    </div>
    @if($unread > 0)
    <form method="POST" action="{{ route('notifications.read-all') }}">
        @csrf
        <button type="submit"
                style="display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:700;color:#2563eb;background:#eff6ff;border:1px solid #bfdbfe;padding:9px 16px;border-radius:9px;cursor:pointer">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            Mark all read
        </button>
    </form>
    @endif
</div>

@if($notifications->isEmpty())
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;padding:72px 24px;text-align:center;box-shadow:0 1px 3px rgba(0,0,0,0.06)">
    <div style="width:60px;height:60px;border-radius:18px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;margin:0 auto 16px auto">
        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.8"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
    </div>
    <p style="font-size:14px;font-weight:700;color:#374151;margin:0 0 6px 0">No notifications yet</p>
    <p style="font-size:13px;color:#9ca3af;margin:0">Transaction alerts and bank updates will appear here.</p>
</div>
@else

@php
$typeConfig = [
    'success' => ['bg'=>'#f0fdf4','iconBg'=>'#dcfce7','stroke'=>'#16a34a','border'=>'#bbf7d0','unreadBg'=>'#f0fdf4','svg'=>'<polyline points="20 6 9 17 4 12"/>'],
    'warning' => ['bg'=>'white','iconBg'=>'#fef9c3','stroke'=>'#d97706','border'=>'#fde68a','unreadBg'=>'#fffbeb','svg'=>'<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>'],
    'alert'   => ['bg'=>'white','iconBg'=>'#fef2f2','stroke'=>'#dc2626','border'=>'#fecaca','unreadBg'=>'#fef2f2','svg'=>'<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>'],
    'info'    => ['bg'=>'white','iconBg'=>'#eff6ff','stroke'=>'#2563eb','border'=>'#bfdbfe','unreadBg'=>'#eff6ff','svg'=>'<circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>'],
    'promo'   => ['bg'=>'white','iconBg'=>'#faf5ff','stroke'=>'#9333ea','border'=>'#e9d5ff','unreadBg'=>'#faf5ff','svg'=>'<path d="M20 12V22H4V12"/><path d="M22 7H2v5h20V7z"/><path d="M12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/>'],
];
@endphp

<div style="display:flex;flex-direction:column;gap:6px">
    @foreach($notifications as $notif)
    @php
    $cfg     = $typeConfig[$notif->type] ?? $typeConfig['info'];
    $isUnread = !$notif->read_at;
    @endphp
    <div style="background:{{ $isUnread ? $cfg['unreadBg'] : 'white' }};border:1px solid {{ $isUnread ? $cfg['border'] : '#f3f4f6' }};border-radius:12px;padding:16px 18px;display:flex;align-items:flex-start;gap:13px">
        {{-- Icon --}}
        <div style="width:40px;height:40px;border-radius:12px;background:{{ $cfg['iconBg'] }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="{{ $cfg['stroke'] }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $cfg['svg'] !!}</svg>
        </div>

        {{-- Content --}}
        <div style="flex:1;min-width:0">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;margin-bottom:3px">
                <p style="font-size:13px;font-weight:{{ $isUnread ? '700' : '600' }};color:#111827;margin:0;line-height:1.3">{{ $notif->title }}</p>
                <div style="display:flex;align-items:center;gap:6px;flex-shrink:0">
                    @if($isUnread)
                    <span style="width:7px;height:7px;border-radius:50%;background:#2563eb;display:inline-block;flex-shrink:0"></span>
                    @endif
                    <p style="font-size:11px;color:#9ca3af;white-space:nowrap;margin:0">{{ $notif->created_at->diffForHumans() }}</p>
                </div>
            </div>
            <p style="font-size:12px;color:#6b7280;line-height:1.55;margin:0 0 10px 0">{{ $notif->body }}</p>
            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
                @if($notif->action_url)
                <a href="{{ $notif->action_url }}"
                   style="font-size:12px;font-weight:700;color:{{ $cfg['stroke'] }};text-decoration:none;display:inline-flex;align-items:center;gap:3px">
                    View details
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
                @endif
                @if($isUnread)
                <form method="POST" action="{{ route('notifications.read', $notif->id) }}" style="display:inline">
                    @csrf
                    <button type="submit" style="font-size:11px;font-weight:600;color:#6b7280;background:none;border:none;cursor:pointer;padding:0">Mark read</button>
                </form>
                @endif
                <form method="POST" action="{{ route('notifications.destroy', $notif->id) }}" style="display:inline">
                    @csrf @method('DELETE')
                    <button type="submit" style="font-size:11px;font-weight:600;color:#d1d5db;background:none;border:none;cursor:pointer;padding:0;display:inline-flex;align-items:center;gap:3px">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/></svg>
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div style="margin-top:16px">
    {{ $notifications->links() }}
</div>
@endif
@endsection
