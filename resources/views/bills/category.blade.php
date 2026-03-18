@extends('layouts.portal')
@section('title', $cat['label'])

@section('content')
{{-- Back + Header --}}
<div style="display:flex;align-items:center;gap:12px;margin-bottom:28px">
    <a href="{{ route('bills') }}"
       style="width:36px;height:36px;border-radius:10px;background:white;border:1px solid #e5e7eb;display:flex;align-items:center;justify-content:center;text-decoration:none;flex-shrink:0">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#374151" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div>
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin:0 0 2px 0">Bill Payments</p>
        <h1 style="font-size:20px;font-weight:800;color:#111827;margin:0">{{ $cat['label'] }}</h1>
    </div>
</div>

{{-- Flash messages --}}
@foreach(['success'] as $k)
@if(session($k))
<div style="margin-bottom:16px;padding:12px 16px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;color:#15803d;font-size:13px;font-weight:500;display:flex;align-items:center;gap:8px">
    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
    {{ session($k) }}
</div>
@endif
@endforeach
@if($errors->any())
<div style="margin-bottom:16px;padding:12px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;color:#991b1b;font-size:13px">{{ $errors->first() }}</div>
@endif

<div style="max-width:560px">
<form method="POST" action="{{ route('bills.pay', $category) }}">
    @csrf

    {{-- Payment details card --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:24px;margin-bottom:14px">
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin:0 0 20px 0">Payment Details</p>

        {{-- Provider --}}
        <div style="margin-bottom:16px">
            <label style="display:block;font-size:11px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Provider</label>
            <div style="position:relative">
                <select name="biller"
                        style="width:100%;padding:10px 36px 10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;font-weight:500;color:#111827;box-sizing:border-box;outline:none;appearance:none;background:white;cursor:pointer" required>
                    <option value="">Select a provider</option>
                    @foreach($cat['billers'] as $biller)
                    <option value="{{ $biller }}">{{ $biller }}</option>
                    @endforeach
                </select>
                <div style="position:absolute;right:12px;top:50%;transform:translateY(-50%);pointer-events:none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
            </div>
        </div>

        {{-- Dynamic fields --}}
        @foreach($cat['fields'] as $field => $label)
        <div style="margin-bottom:16px">
            <label style="display:block;font-size:11px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">{{ $label }}</label>
            <input type="text" name="recipient" placeholder="{{ $label }}" maxlength="100"
                   style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;color:#111827;box-sizing:border-box;outline:none" required>
        </div>
        @endforeach

        {{-- Amount --}}
        <div style="margin-bottom:16px">
            <label style="display:block;font-size:11px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:10px">Amount (NGN)</label>
            {{-- Preset chips --}}
            <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:10px">
                @foreach($cat['amounts'] as $amt)
                <button type="button" onclick="setAmount({{ $amt }})"
                        style="font-size:12px;font-weight:700;padding:7px 16px;border-radius:20px;border:1.5px solid #e5e7eb;background:white;cursor:pointer;color:#374151;transition:all .12s"
                        class="amt-chip" data-amount="{{ $amt }}">
                    {{ number_format($amt) }}
                </button>
                @endforeach
            </div>
            <div style="position:relative">
                <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:13px;font-weight:700;color:#6b7280">NGN</span>
                <input type="number" name="amount" id="amount-input" placeholder="Or enter custom amount" min="50" step="0.01"
                       style="width:100%;padding:10px 12px 10px 48px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;color:#111827;box-sizing:border-box;outline:none" required>
            </div>
        </div>

        {{-- Debit account --}}
        <div>
            <label style="display:block;font-size:11px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Debit From</label>
            <div style="position:relative">
                <select name="account_id"
                        style="width:100%;padding:10px 36px 10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;font-weight:500;color:#111827;box-sizing:border-box;outline:none;appearance:none;background:white;cursor:pointer" required>
                    @foreach($accounts as $acc)
                    <option value="{{ $acc->id }}">{{ $acc->account_name }} — NGN {{ number_format($acc->available_balance, 2) }}</option>
                    @endforeach
                </select>
                <div style="position:absolute;right:12px;top:50%;transform:translateY(-50%);pointer-events:none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Submit --}}
    <button type="submit"
            style="width:100%;background:{{ $cat['color'] ?? '#2563eb' }};color:white;font-size:14px;font-weight:800;padding:14px;border-radius:12px;border:none;cursor:pointer;letter-spacing:.01em">
        Pay Now
    </button>
</form>

{{-- Payment History --}}
@if($history->isNotEmpty())
<div style="margin-top:32px">
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px">Recent {{ $cat['label'] }} Payments</p>
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.06)">
        @foreach($history as $bill)
        <div style="padding:14px 20px;border-bottom:1px solid #f3f4f6">
            <div style="display:flex;justify-content:space-between;align-items:flex-start">
                <div>
                    <p style="font-size:13px;font-weight:700;color:#111827;margin:0">{{ $bill->biller }}</p>
                    <p style="font-size:11px;color:#9ca3af;margin:2px 0 0 0">{{ $bill->recipient }}</p>
                    @if($bill->token)
                    <p style="font-size:11px;color:#6b7280;margin:4px 0 0 0">Token: <strong style="font-family:monospace;color:#1d4ed8;letter-spacing:1px">{{ $bill->token }}</strong></p>
                    @endif
                    <p style="font-size:10px;color:#9ca3af;margin:3px 0 0 0">{{ $bill->created_at->format('d M Y, H:i') }} · Ref: {{ $bill->reference }}</p>
                </div>
                <div style="text-align:right;flex-shrink:0;margin-left:12px">
                    <p style="font-size:14px;font-weight:800;color:#dc2626;margin:0">NGN {{ number_format($bill->amount, 2) }}</p>
                    <span style="font-size:10px;font-weight:700;color:#15803d;background:#f0fdf4;padding:2px 8px;border-radius:20px;display:inline-block;margin-top:4px">PAID</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
</div>

<script>
function setAmount(amt) {
    document.getElementById('amount-input').value = amt;
    document.querySelectorAll('.amt-chip').forEach(b => {
        const sel = parseInt(b.dataset.amount) === amt;
        b.style.background    = sel ? '#eff6ff' : 'white';
        b.style.borderColor   = sel ? '#2563eb' : '#e5e7eb';
        b.style.color         = sel ? '#1d4ed8' : '#374151';
    });
}
</script>
@endsection
