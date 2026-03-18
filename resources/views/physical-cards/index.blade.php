@extends('layouts.portal')
@section('title', 'Debit Cards')

@section('content')

<div style="display:flex;align-items:center;gap:12px;margin-bottom:28px">
    <a href="{{ route('dashboard') }}"
       style="display:flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:9px;border:1px solid #e5e7eb;color:#6b7280;text-decoration:none;flex-shrink:0;background:white">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#111827;margin:0 0 3px">Debit Cards</h1>
        <p style="font-size:13px;color:#6b7280;margin:0">Manage your physical debit cards</p>
    </div>
</div>

@if(session('error'))
<div style="display:flex;align-items:flex-start;gap:10px;padding:13px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;font-size:13px;color:#991b1b;margin-bottom:18px;font-weight:500">
    <svg style="flex-shrink:0;margin-top:1px" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <span>{{ session('error') }}</span>
</div>
@endif

@if($errors->any())
<div style="display:flex;align-items:flex-start;gap:10px;padding:13px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;font-size:13px;color:#991b1b;margin-bottom:18px;font-weight:500">
    <svg style="flex-shrink:0;margin-top:1px" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <span>{{ $errors->first() }}</span>
</div>
@endif

@forelse($accounts as $account)
@php
    $card = $cards->get($account->id) ?? null;
    $hasPending = isset($pendingRequests[$account->id]);
@endphp

<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);margin-bottom:18px;overflow:hidden">

    {{-- Account Header --}}
    <div style="padding:16px 22px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between">
        <div>
            <p style="font-size:14px;font-weight:700;color:#111827;margin:0 0 2px">{{ $account->account_name }}</p>
            <p style="font-size:12px;color:#9ca3af;margin:0">{{ $account->account_number }} &middot; {{ strtoupper($account->account_type ?? 'Account') }}</p>
        </div>
        @if($card)
        @php
            $statusColors = [
                'active'    => ['bg'=>'#f0fdf4','text'=>'#15803d','border'=>'#bbf7d0'],
                'blocked'   => ['bg'=>'#fef2f2','text'=>'#991b1b','border'=>'#fecaca'],
                'expired'   => ['bg'=>'#f9fafb','text'=>'#6b7280','border'=>'#e5e7eb'],
                'cancelled' => ['bg'=>'#f9fafb','text'=>'#6b7280','border'=>'#e5e7eb'],
            ];
            $sc = $statusColors[$card->status] ?? $statusColors['active'];
        @endphp
        <span style="font-size:10px;font-weight:800;padding:4px 11px;border-radius:99px;background:{{ $sc['bg'] }};color:{{ $sc['text'] }};border:1px solid {{ $sc['border'] }};text-transform:uppercase;letter-spacing:.05em">
            {{ $card->status }}
        </span>
        @endif
    </div>

    <div style="padding:22px">

        @if($card)
        {{-- Card Visual --}}
        <div style="background:linear-gradient(135deg,#0f172a 0%,#1e3a8a 50%,#1d4ed8 100%);border-radius:16px;padding:24px 26px;color:white;position:relative;overflow:hidden;margin-bottom:18px;max-width:420px">
            <div style="position:absolute;right:-30px;top:-30px;width:150px;height:150px;border-radius:50%;background:rgba(255,255,255,0.05)"></div>
            <div style="position:absolute;right:30px;bottom:-50px;width:190px;height:190px;border-radius:50%;background:rgba(255,255,255,0.03)"></div>

            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:30px;position:relative">
                <div>
                    <p style="font-size:10px;color:rgba(255,255,255,0.55);letter-spacing:.1em;text-transform:uppercase;margin:0 0 5px">Debit Card</p>
                    @if($card->is_blocked)
                    <span style="font-size:10px;background:rgba(220,38,38,0.3);border:1px solid rgba(220,38,38,0.5);padding:3px 10px;border-radius:99px;font-weight:800;letter-spacing:.05em">BLOCKED</span>
                    @elseif($card->status === 'expired')
                    <span style="font-size:10px;background:rgba(255,255,255,0.15);padding:3px 10px;border-radius:99px;font-weight:700">EXPIRED</span>
                    @else
                    <span style="font-size:10px;background:rgba(34,197,94,0.25);border:1px solid rgba(34,197,94,0.4);padding:3px 10px;border-radius:99px;font-weight:800;letter-spacing:.05em">ACTIVE</span>
                    @endif
                </div>
                @if($card->card_scheme === 'visa')
                <span style="font-size:20px;font-weight:900;font-style:italic;color:white;letter-spacing:-1px">VISA</span>
                @elseif($card->card_scheme === 'mastercard')
                <div style="display:flex;align-items:center">
                    <div style="width:24px;height:24px;border-radius:50%;background:#eb001b;opacity:0.9"></div>
                    <div style="width:24px;height:24px;border-radius:50%;background:#f79e1b;opacity:0.9;margin-left:-10px"></div>
                </div>
                @else
                <span style="font-size:14px;font-weight:800;color:#00a651;letter-spacing:.04em">VERVE</span>
                @endif
            </div>

            <p style="font-family:monospace;font-size:20px;letter-spacing:6px;font-weight:600;margin:0 0 22px;color:rgba(255,255,255,0.9);position:relative">
                **** **** **** {{ $card->card_last4 ?? '****' }}
            </p>

            <div style="display:flex;justify-content:space-between;align-items:flex-end;position:relative">
                <div>
                    <p style="font-size:9px;color:rgba(255,255,255,0.5);margin:0 0 3px;letter-spacing:.07em">ACCOUNT</p>
                    <p style="font-size:12px;font-weight:700;margin:0">{{ $account->account_number }}</p>
                </div>
                @if($card->expires_at)
                <div style="text-align:right">
                    <p style="font-size:9px;color:rgba(255,255,255,0.5);margin:0 0 3px;letter-spacing:.07em">EXPIRES</p>
                    <p style="font-size:12px;font-weight:700;margin:0">{{ \Carbon\Carbon::parse($card->expires_at)->format('m/Y') }}</p>
                </div>
                @endif
            </div>
        </div>

        @if(!$card->is_blocked && $card->status === 'active')

        <div style="display:flex;gap:8px;margin-bottom:14px">
            <button type="button"
                    onclick="var p=document.getElementById('block-form-{{ $card->id }}');p.style.display=p.style.display==='none'?'block':'none'"
                    style="font-size:12px;font-weight:700;color:#dc2626;background:#fef2f2;border:1px solid #fecaca;padding:8px 18px;border-radius:9px;cursor:pointer">
                Block Card
            </button>
            <a href="{{ route('physical-cards.request', $account->id) }}"
               style="display:inline-block;font-size:12px;font-weight:700;color:#2563eb;background:#eff6ff;border:1px solid #bfdbfe;padding:8px 18px;border-radius:9px;text-decoration:none">
                Request Replacement
            </a>
        </div>

        <div id="block-form-{{ $card->id }}" style="display:none;background:#fff8f8;border:1px solid #fecaca;border-radius:12px;padding:18px;margin-bottom:4px">
            <p style="font-size:13px;font-weight:700;color:#991b1b;margin:0 0 12px">Block this card</p>
            <form method="POST" action="{{ route('physical-cards.block', $card->id) }}">
                @csrf
                <div style="margin-bottom:12px">
                    <label style="display:block;font-size:12px;font-weight:700;color:#6b7280;margin-bottom:6px">Reason for blocking <span style="color:#dc2626">*</span></label>
                    <textarea name="reason" rows="2" maxlength="300" required
                              placeholder="e.g. Lost card, suspicious activity..."
                              style="width:100%;padding:10px 12px;border:1px solid #fca5a5;border-radius:9px;font-size:13px;outline:none;box-sizing:border-box;resize:vertical;background:white;color:#111827"></textarea>
                </div>
                <div style="display:flex;gap:8px">
                    <button type="submit"
                            style="font-size:12px;font-weight:700;color:white;background:#dc2626;border:none;padding:9px 20px;border-radius:9px;cursor:pointer">
                        Confirm Block
                    </button>
                    <button type="button"
                            onclick="document.getElementById('block-form-{{ $card->id }}').style.display='none'"
                            style="font-size:12px;font-weight:600;color:#6b7280;background:white;border:1px solid #e5e7eb;padding:9px 18px;border-radius:9px;cursor:pointer">
                        Cancel
                    </button>
                </div>
            </form>
        </div>

        @elseif($card->is_blocked)

        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:16px;margin-bottom:4px">
            <p style="font-size:13px;font-weight:700;color:#991b1b;margin:0 0 4px">This card is blocked</p>
            @if($card->blocked_reason)
            <p style="font-size:12px;color:#b91c1c;margin:0 0 4px">Reason: {{ $card->blocked_reason }}</p>
            @endif
            @if($card->blocked_at)
            <p style="font-size:11px;color:#f87171;margin:0 0 12px">Blocked on {{ \Carbon\Carbon::parse($card->blocked_at)->format('d M Y, H:i') }}</p>
            @endif
            <form method="POST" action="{{ route('physical-cards.unblock', $card->id) }}" style="display:inline">
                @csrf
                <button type="submit"
                        style="font-size:12px;font-weight:700;color:#15803d;background:#f0fdf4;border:1px solid #bbf7d0;padding:8px 18px;border-radius:9px;cursor:pointer">
                    Unblock Card
                </button>
            </form>
        </div>

        @endif

        @else
        {{-- No Card --}}
        <div style="display:flex;align-items:center;gap:16px;padding:18px;background:#f9fafb;border:1px dashed #d1d5db;border-radius:12px">
            <div style="width:46px;height:46px;border-radius:12px;background:#e5e7eb;display:grid;place-items:center;flex-shrink:0">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            </div>
            <div style="flex:1">
                <p style="font-size:13px;font-weight:700;color:#374151;margin:0 0 3px">No debit card linked</p>
                <p style="font-size:12px;color:#9ca3af;margin:0">Request a card for this account to make ATM withdrawals and POS payments.</p>
            </div>
            @if(!$hasPending)
            <a href="{{ route('physical-cards.request', $account->id) }}"
               style="font-size:12px;font-weight:700;color:white;background:#2563eb;padding:9px 18px;border-radius:9px;text-decoration:none;white-space:nowrap;flex-shrink:0">
                Request Card
            </a>
            @else
            <span style="font-size:11px;font-weight:700;color:#92400e;background:#fffbeb;border:1px solid #fde68a;padding:5px 12px;border-radius:99px;flex-shrink:0">Request Pending</span>
            @endif
        </div>
        @endif

    </div>
</div>
@empty
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:56px;text-align:center">
    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="1.5" style="margin:0 auto 12px;display:block"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
    <p style="font-size:15px;font-weight:700;color:#374151;margin:0 0 5px">No accounts found</p>
    <p style="font-size:12px;color:#9ca3af;margin:0">Open an account to request a debit card.</p>
</div>
@endforelse

{{-- Recent Requests History --}}
@if($recentRequests->isNotEmpty())
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);overflow:hidden;margin-top:4px">
    <div style="padding:18px 22px;border-bottom:1px solid #f3f4f6">
        <p style="font-size:14px;font-weight:700;color:#111827;margin:0 0 2px">Card Requests</p>
        <p style="font-size:12px;color:#9ca3af;margin:0">History of all card requests</p>
    </div>
    <div style="overflow-x:auto">
        <table style="width:100%;border-collapse:collapse;font-size:13px">
            <thead>
                <tr style="background:#f9fafb">
                    <th style="padding:10px 22px;text-align:left;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;white-space:nowrap">Reference</th>
                    <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;white-space:nowrap">Type</th>
                    <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;white-space:nowrap">Collection</th>
                    <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;white-space:nowrap">Status</th>
                    <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;white-space:nowrap">Date</th>
                    <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentRequests as $req)
                @php
                    $reqStatusMap = [
                        'pending'    => ['bg'=>'#fffbeb','text'=>'#92400e','border'=>'#fde68a'],
                        'processing' => ['bg'=>'#eff6ff','text'=>'#1d4ed8','border'=>'#bfdbfe'],
                        'dispatched' => ['bg'=>'#f0fdf4','text'=>'#15803d','border'=>'#bbf7d0'],
                        'delivered'  => ['bg'=>'#f0fdf4','text'=>'#15803d','border'=>'#bbf7d0'],
                        'cancelled'  => ['bg'=>'#f9fafb','text'=>'#6b7280','border'=>'#e5e7eb'],
                    ];
                    $rs = $reqStatusMap[$req->status] ?? $reqStatusMap['pending'];
                    $typeLabels = ['new'=>'New Card','replacement'=>'Replacement','lost_stolen'=>'Lost / Stolen'];
                    $collLabels = ['branch_pickup'=>'Branch Pickup','home_delivery'=>'Home Delivery'];
                @endphp
                <tr style="border-top:1px solid #f3f4f6">
                    <td style="padding:13px 22px;font-weight:700;color:#374151;font-family:monospace;font-size:12px">{{ $req->reference }}</td>
                    <td style="padding:13px 16px;color:#374151;font-weight:600">{{ $typeLabels[$req->request_type] ?? $req->request_type }}</td>
                    <td style="padding:13px 16px;color:#6b7280">{{ $collLabels[$req->collection_method] ?? $req->collection_method }}</td>
                    <td style="padding:13px 16px">
                        <span style="font-size:11px;font-weight:700;padding:4px 10px;border-radius:99px;background:{{ $rs['bg'] }};color:{{ $rs['text'] }};border:1px solid {{ $rs['border'] }};text-transform:uppercase;letter-spacing:.04em">
                            {{ $req->status }}
                        </span>
                    </td>
                    <td style="padding:13px 16px;color:#9ca3af;font-size:12px;white-space:nowrap">{{ \Carbon\Carbon::parse($req->created_at)->format('d M Y') }}</td>
                    <td style="padding:13px 16px">
                        @if($req->status === 'pending')
                        <form method="POST" action="{{ route('physical-cards.cancel-request', $req->id) }}" onsubmit="return confirm('Cancel this request?')">
                            @csrf
                            <button type="submit" style="font-size:11px;font-weight:700;color:#dc2626;background:none;border:none;cursor:pointer;padding:0">Cancel</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection
