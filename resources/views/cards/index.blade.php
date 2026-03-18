@extends('layouts.portal')
@section('title', 'Virtual Cards')

@section('content')
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px">
    <div>
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Payments</p>
        <h1 style="font-size:20px;font-weight:800;color:#111827;margin:0">Virtual Cards</h1>
    </div>
</div>

{{-- Flash messages --}}
@foreach(['success','error','pin_success'] as $k)
@if(session($k))
<div style="margin-bottom:16px;padding:12px 16px;background:{{ $k==='error'?'#fef2f2':'#f0fdf4' }};border:1px solid {{ $k==='error'?'#fecaca':'#bbf7d0' }};border-radius:10px;color:{{ $k==='error'?'#991b1b':'#15803d' }};font-size:13px;font-weight:500;display:flex;align-items:center;gap:8px">
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="{{ $k==='error'?'#991b1b':'#15803d' }}" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
    {{ session($k) }}
</div>
@endif
@endforeach
@if($errors->any())
<div style="margin-bottom:16px;padding:12px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;color:#991b1b;font-size:13px">{{ $errors->first() }}</div>
@endif

{{-- Create new card --}}
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:22px;margin-bottom:24px">
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:16px">Create New Virtual Card</p>
    <form method="POST" action="{{ route('cards.store') }}" style="display:flex;gap:12px;align-items:flex-end">
        @csrf
        <div style="flex:1">
            <label style="display:block;font-size:11px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Link to Account</label>
            <div style="position:relative">
                <select name="account_id"
                        style="width:100%;padding:10px 36px 10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;font-weight:500;color:#111827;box-sizing:border-box;outline:none;appearance:none;background:white;cursor:pointer">
                    @foreach($accounts as $acc)
                    <option value="{{ $acc->id }}">{{ $acc->account_name }} ({{ $acc->account_number }})</option>
                    @endforeach
                </select>
                <div style="position:absolute;right:12px;top:50%;transform:translateY(-50%);pointer-events:none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
            </div>
        </div>
        <button type="submit" style="background:#2563eb;color:white;font-size:13px;font-weight:700;padding:11px 22px;border-radius:10px;border:none;cursor:pointer;white-space:nowrap;display:flex;align-items:center;gap:6px">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Create Card
        </button>
    </form>
    <p style="font-size:11px;color:#9ca3af;margin:10px 0 0 0">Virtual cards are free. Max 3 cards per account. Use for online payments — no physical card needed.</p>
</div>

{{-- Empty state --}}
@if($cards->isEmpty())
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;padding:60px 24px;text-align:center;box-shadow:0 1px 3px rgba(0,0,0,0.06)">
    <div style="width:64px;height:64px;border-radius:18px;background:linear-gradient(135deg,#1e3a8a,#2563eb);display:flex;align-items:center;justify-content:center;margin:0 auto 16px auto">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
    </div>
    <p style="font-size:14px;font-weight:700;color:#374151;margin:0 0 6px 0">No virtual cards yet</p>
    <p style="font-size:12px;color:#9ca3af;margin:0;max-width:320px;margin-left:auto;margin-right:auto">Create a virtual card to make secure online payments without exposing your main account.</p>
</div>

@else

{{-- Card list --}}
<div style="display:flex;flex-direction:column;gap:20px">
@foreach($cards as $card)
@php
$isFrozen   = $card->isFrozen();
$isVisa     = $card->card_type === 'visa';
$gradient   = $isFrozen
    ? 'linear-gradient(135deg,#374151 0%,#6b7280 100%)'
    : ($isVisa ? 'linear-gradient(135deg,#0f172a 0%,#1e3a8a 50%,#2563eb 100%)'
               : 'linear-gradient(135deg,#450a0a 0%,#7f1d1d 50%,#dc2626 100%)');
@endphp
<div style="border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.12)">

    {{-- Card face --}}
    <div style="background:{{ $gradient }};padding:26px 28px;color:white;position:relative;overflow:hidden;min-height:180px">
        {{-- Decorative circles --}}
        <div style="position:absolute;right:-36px;top:-36px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,0.06)"></div>
        <div style="position:absolute;right:40px;bottom:-50px;width:140px;height:140px;border-radius:50%;background:rgba(255,255,255,0.04)"></div>
        <div style="position:absolute;left:-20px;bottom:-20px;width:100px;height:100px;border-radius:50%;background:rgba(255,255,255,0.03)"></div>

        {{-- Top row --}}
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;position:relative">
            <div>
                <p style="font-size:9px;color:rgba(255,255,255,0.6);letter-spacing:.12em;text-transform:uppercase;margin:0 0 5px 0">Virtual Card</p>
                @if($isFrozen)
                <span style="font-size:9px;font-weight:700;background:rgba(255,255,255,0.18);padding:3px 10px;border-radius:20px;letter-spacing:.06em;display:inline-flex;align-items:center;gap:4px">
                    <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v20M2 12h20M4.93 4.93l14.14 14.14M19.07 4.93 4.93 19.07"/></svg>
                    FROZEN
                </span>
                @else
                <span style="font-size:9px;font-weight:700;background:rgba(255,255,255,0.18);padding:3px 10px;border-radius:20px;letter-spacing:.06em;display:inline-flex;align-items:center;gap:4px">
                    <span style="width:6px;height:6px;border-radius:50%;background:#86efac;display:inline-block"></span>
                    ACTIVE
                </span>
                @endif
            </div>
            {{-- Card brand logo placeholder --}}
            <div style="text-align:right">
                @if($isVisa)
                <span style="font-size:20px;font-weight:900;letter-spacing:-1px;font-style:italic;color:rgba(255,255,255,0.9);font-family:serif">VISA</span>
                @else
                <div style="display:flex;gap:-6px">
                    <div style="width:28px;height:28px;border-radius:50%;background:#eb001b;opacity:0.9"></div>
                    <div style="width:28px;height:28px;border-radius:50%;background:#f79e1b;opacity:0.9;margin-left:-10px"></div>
                </div>
                @endif
            </div>
        </div>

        {{-- Card chip --}}
        <div style="width:36px;height:28px;border-radius:5px;background:rgba(255,255,255,0.2);border:1px solid rgba(255,255,255,0.3);margin-bottom:16px;position:relative">
            <div style="position:absolute;top:50%;left:0;right:0;height:1px;background:rgba(255,255,255,0.3)"></div>
            <div style="position:absolute;left:50%;top:0;bottom:0;width:1px;background:rgba(255,255,255,0.3)"></div>
        </div>

        {{-- Card number --}}
        <p style="font-family:monospace;font-size:17px;letter-spacing:4px;font-weight:600;margin:0 0 18px 0;color:rgba(255,255,255,0.95)">{{ $card->card_number_masked }}</p>

        {{-- Bottom row --}}
        <div style="display:flex;justify-content:space-between;align-items:flex-end;position:relative">
            <div>
                <p style="font-size:8px;color:rgba(255,255,255,0.5);margin:0 0 2px 0;text-transform:uppercase;letter-spacing:.1em">Card Holder</p>
                <p style="font-size:13px;font-weight:700;margin:0;letter-spacing:.02em">{{ $card->card_name }}</p>
            </div>
            <div style="text-align:center">
                <p style="font-size:8px;color:rgba(255,255,255,0.5);margin:0 0 2px 0;text-transform:uppercase;letter-spacing:.1em">Expires</p>
                <p style="font-size:13px;font-weight:700;margin:0;font-family:monospace">{{ $card->expiry_month }}/{{ $card->expiry_year }}</p>
            </div>
            <div style="text-align:right">
                <p style="font-size:8px;color:rgba(255,255,255,0.5);margin:0 0 2px 0;text-transform:uppercase;letter-spacing:.1em">CVV</p>
                <p id="cvv-{{ $card->id }}" style="font-size:13px;font-weight:700;margin:0;font-family:monospace;cursor:pointer" onclick="toggleCvv({{ $card->id }}, '{{ $card->cvv ?? '•••' }}')" title="Click to reveal">•••</p>
            </div>
        </div>
    </div>

    {{-- Card actions bar --}}
    <div style="background:white;border:1px solid #e5e7eb;border-top:none;padding:14px 20px;display:flex;align-items:center;justify-content:space-between;gap:10px">
        <div style="display:flex;gap:8px;flex-wrap:wrap">
            {{-- Freeze / Unfreeze --}}
            @if($isFrozen)
            <form method="POST" action="{{ route('cards.unfreeze', $card->id) }}">
                @csrf
                <button type="submit"
                        style="display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:700;color:#16a34a;background:#f0fdf4;border:1px solid #bbf7d0;padding:8px 16px;border-radius:9px;cursor:pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                    Unfreeze
                </button>
            </form>
            @else
            <form method="POST" action="{{ route('cards.freeze', $card->id) }}">
                @csrf
                <button type="submit"
                        style="display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:700;color:#2563eb;background:#eff6ff;border:1px solid #bfdbfe;padding:8px 16px;border-radius:9px;cursor:pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2v20M2 12h20M4.93 4.93l14.14 14.14M19.07 4.93 4.93 19.07"/></svg>
                    Freeze
                </button>
            </form>
            @endif

            {{-- Set Limit --}}
            <button onclick="togglePanel('limit-{{ $card->id }}')"
                    style="display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:700;color:#6b7280;background:#f9fafb;border:1px solid #e5e7eb;padding:8px 16px;border-radius:9px;cursor:pointer">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Set Limit
            </button>

            {{-- Manage PIN --}}
            <button onclick="togglePanel('pin-panel-{{ $card->id }}')"
                    style="display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:700;color:#374151;background:#f9fafb;border:1px solid #e5e7eb;padding:8px 16px;border-radius:9px;cursor:pointer">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                PIN
                @if($card->pin_locked_until && now()->lt($card->pin_locked_until))
                    <span style="font-size:9px;font-weight:800;color:#991b1b;background:#fef2f2;padding:1px 5px;border-radius:8px">LOCKED</span>
                @elseif($card->pin_set_at)
                    <span style="font-size:9px;font-weight:800;color:#15803d;background:#f0fdf4;padding:1px 5px;border-radius:8px">SET</span>
                @else
                    <span style="font-size:9px;font-weight:800;color:#92400e;background:#fffbeb;padding:1px 5px;border-radius:8px">NOT SET</span>
                @endif
            </button>
        </div>

        <div style="display:flex;align-items:center;gap:12px">
            @if($card->spending_limit)
            <p style="font-size:11px;color:#6b7280;margin:0">Limit: NGN {{ number_format($card->spending_limit, 2) }}</p>
            @endif
            <form method="POST" action="{{ route('cards.destroy', $card->id) }}" onsubmit="return confirm('Cancel this card? This cannot be undone.')">
                @csrf @method('DELETE')
                <button type="submit"
                        style="font-size:11px;font-weight:600;color:#dc2626;background:none;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:4px">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                    Cancel
                </button>
            </form>
        </div>
    </div>

    {{-- Spending limit panel --}}
    <div id="limit-{{ $card->id }}" style="display:none;background:#f9fafb;border:1px solid #e5e7eb;border-top:none;padding:16px 20px">
        <form method="POST" action="{{ route('cards.limit', $card->id) }}" style="display:flex;gap:8px;align-items:center">
            @csrf
            <div style="position:relative;flex:1">
                <span style="position:absolute;left:10px;top:50%;transform:translateY(-50%);font-size:12px;font-weight:700;color:#6b7280">NGN</span>
                <input type="number" name="limit" placeholder="Monthly limit (0 = no limit)" min="0" step="0.01"
                       value="{{ $card->spending_limit ?? '' }}"
                       style="width:100%;padding:9px 12px 9px 44px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none">
            </div>
            <button type="submit" style="background:#2563eb;color:white;font-size:12px;font-weight:700;padding:9px 18px;border-radius:9px;border:none;cursor:pointer">Save</button>
            <button type="button" onclick="togglePanel('limit-{{ $card->id }}')" style="font-size:12px;color:#9ca3af;background:none;border:none;cursor:pointer;padding:4px">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </form>
    </div>

    {{-- PIN management panel --}}
    <div id="pin-panel-{{ $card->id }}" style="display:none;background:white;border:1px solid #e5e7eb;border-top:none;border-radius:0 0 14px 14px;padding:22px 24px">
        @if($card->pin_locked_until && now()->lt($card->pin_locked_until))
        <div style="padding:14px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;font-size:13px;color:#991b1b;display:flex;align-items:flex-start;gap:8px">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <div>
                <strong>PIN Locked</strong> — Your card PIN has been locked until
                {{ \Carbon\Carbon::parse($card->pin_locked_until)->format('d M Y, H:i') }} due to too many incorrect attempts.
            </div>
        </div>

        @elseif(!$card->pin_set_at)
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin:0 0 16px 0">Set Card PIN</p>
        <form method="POST" action="{{ route('cards.pin.set', $card->id) }}">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px">
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">New PIN</label>
                    <input type="password" name="pin" inputmode="numeric" maxlength="4" placeholder="• • • •"
                           style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:9px;font-size:18px;text-align:center;letter-spacing:8px;font-family:monospace;box-sizing:border-box;outline:none"
                           autocomplete="new-password">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Confirm PIN</label>
                    <input type="password" name="pin_confirmation" inputmode="numeric" maxlength="4" placeholder="• • • •"
                           style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:9px;font-size:18px;text-align:center;letter-spacing:8px;font-family:monospace;box-sizing:border-box;outline:none"
                           autocomplete="new-password">
                </div>
            </div>
            <div style="margin-bottom:14px">
                <label style="display:block;font-size:11px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Portal PIN (to authorise)</label>
                <input type="password" name="portal_pin" placeholder="Your transaction PIN"
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none"
                       autocomplete="current-password">
            </div>
            <button type="submit" style="background:#2563eb;color:white;font-size:13px;font-weight:700;padding:11px 22px;border-radius:10px;border:none;cursor:pointer">Set PIN</button>
        </form>

        @else
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin:0 0 4px 0">Change Card PIN</p>
        <p style="font-size:11px;color:#9ca3af;margin:0 0 16px 0">PIN last set: {{ \Carbon\Carbon::parse($card->pin_set_at)->format('d M Y') }}</p>
        <form method="POST" action="{{ route('cards.pin.change', $card->id) }}">
            @csrf
            <div style="margin-bottom:12px">
                <label style="display:block;font-size:11px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Current PIN</label>
                <input type="password" name="current_pin" inputmode="numeric" maxlength="4" placeholder="• • • •"
                       style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:9px;font-size:18px;text-align:center;letter-spacing:8px;font-family:monospace;box-sizing:border-box;outline:none"
                       autocomplete="current-password">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px">
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">New PIN</label>
                    <input type="password" name="new_pin" inputmode="numeric" maxlength="4" placeholder="• • • •"
                           style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:9px;font-size:18px;text-align:center;letter-spacing:8px;font-family:monospace;box-sizing:border-box;outline:none"
                           autocomplete="new-password">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Confirm New PIN</label>
                    <input type="password" name="new_pin_confirmation" inputmode="numeric" maxlength="4" placeholder="• • • •"
                           style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:9px;font-size:18px;text-align:center;letter-spacing:8px;font-family:monospace;box-sizing:border-box;outline:none"
                           autocomplete="new-password">
                </div>
            </div>
            <button type="submit" style="background:#2563eb;color:white;font-size:13px;font-weight:700;padding:11px 22px;border-radius:10px;border:none;cursor:pointer">Change PIN</button>
        </form>
        @endif
    </div>

</div>{{-- end card wrapper --}}
@endforeach
</div>
@endif

<script>
function togglePanel(id) {
    const el = document.getElementById(id);
    if(el) el.style.display = el.style.display === 'none' ? 'block' : 'none';
}

const cvvStore = {};
function toggleCvv(cardId, cvvVal) {
    const el = document.getElementById('cvv-' + cardId);
    if(!el) return;
    if(cvvStore[cardId]) {
        el.textContent = '•••';
        cvvStore[cardId] = false;
    } else {
        el.textContent = cvvVal;
        cvvStore[cardId] = true;
        // Auto-hide after 5 seconds
        setTimeout(() => {
            el.textContent = '•••';
            cvvStore[cardId] = false;
        }, 5000);
    }
}
</script>
@endsection
