@extends('layouts.portal')
@section('title', 'Request Debit Card')

@section('content')

<div style="display:flex;align-items:center;gap:12px;margin-bottom:28px">
    <a href="{{ route('physical-cards') }}"
       style="display:flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:9px;border:1px solid #e5e7eb;color:#6b7280;text-decoration:none;flex-shrink:0;background:white">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#111827;margin:0 0 3px">Request Debit Card</h1>
        <p style="font-size:13px;color:#6b7280;margin:0">{{ $account->account_name }} &middot; {{ $account->account_number }}</p>
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

<div style="max-width:600px">

    <form method="POST" action="{{ route('physical-cards.store-request') }}" id="card-req-form">
        @csrf
        <input type="hidden" name="account_id" value="{{ $account->id }}">

        {{-- Request Type --}}
        <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:22px;margin-bottom:14px">
            <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin:0 0 16px">Request Type</p>

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px">
                @php
                $requestTypes = [
                    ['value'=>'new',         'label'=>'New Card',      'desc'=>'First card for this account', 'icon'=>'<rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>'],
                    ['value'=>'replacement', 'label'=>'Replacement',   'desc'=>'Replace damaged or worn card','icon'=>'<polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>'],
                    ['value'=>'lost_stolen', 'label'=>'Lost / Stolen', 'desc'=>'Card missing or compromised','icon'=>'<circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>'],
                ];
                @endphp
                @foreach($requestTypes as $rt)
                <label style="cursor:pointer">
                    <input type="radio" name="request_type" value="{{ $rt['value'] }}"
                           {{ old('request_type', 'new') === $rt['value'] ? 'checked' : '' }}
                           style="display:none"
                           onchange="document.querySelectorAll('.rtype-card').forEach(c=>{c.style.borderColor='#e5e7eb';c.style.background='white'});this.closest('label').querySelector('.rtype-card').style.borderColor='#2563eb';this.closest('label').querySelector('.rtype-card').style.background='#eff6ff'">
                    <div class="rtype-card" style="padding:16px 10px;background:{{ old('request_type','new')===$rt['value']?'#eff6ff':'white' }};border:2px solid {{ old('request_type','new')===$rt['value']?'#2563eb':'#e5e7eb' }};border-radius:12px;text-align:center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" style="margin:0 auto 9px;display:block">{!! $rt['icon'] !!}</svg>
                        <p style="font-size:12px;font-weight:700;color:#111827;margin:0 0 3px">{{ $rt['label'] }}</p>
                        <p style="font-size:10px;color:#9ca3af;margin:0;line-height:1.4">{{ $rt['desc'] }}</p>
                    </div>
                </label>
                @endforeach
            </div>
        </div>

        {{-- Collection Method --}}
        <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:22px;margin-bottom:14px">
            <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin:0 0 16px">Collection Method</p>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:18px">
                @php
                $collMethods = [
                    ['value'=>'branch_pickup','label'=>'Branch Pickup','desc'=>'Collect from a branch near you'],
                    ['value'=>'home_delivery','label'=>'Home Delivery','desc'=>'Delivered to your address'],
                ];
                @endphp
                @foreach($collMethods as $cm)
                <label style="cursor:pointer">
                    <input type="radio" name="collection_method" value="{{ $cm['value'] }}"
                           {{ old('collection_method','branch_pickup')===$cm['value'] ? 'checked' : '' }}
                           style="display:none"
                           onchange="toggleCollectionFields(this.value)">
                    <div class="cmethod-card" data-method="{{ $cm['value'] }}"
                         style="padding:16px;background:{{ old('collection_method','branch_pickup')===$cm['value']?'#eff6ff':'white' }};border:2px solid {{ old('collection_method','branch_pickup')===$cm['value']?'#2563eb':'#e5e7eb' }};border-radius:12px">
                        <p style="font-size:13px;font-weight:700;color:#111827;margin:0 0 4px">{{ $cm['label'] }}</p>
                        <p style="font-size:11px;color:#9ca3af;margin:0">{{ $cm['desc'] }}</p>
                    </div>
                </label>
                @endforeach
            </div>

            <div id="branch-field" style="display:{{ old('collection_method','branch_pickup')==='branch_pickup'?'block':'none' }}">
                <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:7px">Branch Name <span style="color:#dc2626">*</span></label>
                <input type="text" name="branch_name" value="{{ old('branch_name') }}"
                       placeholder="e.g. Ikeja Branch, Victoria Island Branch"
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;outline:none;box-sizing:border-box;color:#111827">
            </div>

            <div id="delivery-field" style="display:{{ old('collection_method')==='home_delivery'?'block':'none' }}">
                <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:7px">Delivery Address <span style="color:#dc2626">*</span></label>
                <textarea name="delivery_address" rows="3"
                          placeholder="Full delivery address including city and state..."
                          style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;outline:none;box-sizing:border-box;resize:vertical;color:#111827">{{ old('delivery_address') }}</textarea>
                <p style="font-size:11px;color:#9ca3af;margin:5px 0 0">Delivery typically takes 3–5 business days. A fee may apply.</p>
            </div>
        </div>

        {{-- Additional Info --}}
        <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:22px;margin-bottom:14px">
            <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin:0 0 16px">Additional Information</p>
            <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:7px">Reason / Notes <span style="font-size:11px;font-weight:400;color:#9ca3af">(optional)</span></label>
            <textarea name="reason" rows="3" maxlength="500"
                      placeholder="Any additional details about this request..."
                      style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;outline:none;box-sizing:border-box;resize:vertical;color:#111827">{{ old('reason') }}</textarea>
        </div>

        {{-- Notice --}}
        <div style="display:flex;gap:12px;padding:14px 18px;background:#fffbeb;border:1px solid #fde68a;border-radius:12px;margin-bottom:22px">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#92400e" stroke-width="2" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <p style="font-size:12px;color:#92400e;line-height:1.7;margin:0">Your card request will be reviewed and processed within 3–5 business days. You will be notified of progress via SMS and in-app notification. Card issuance fees may apply per your account tier.</p>
        </div>

        <button type="submit"
                style="width:100%;background:#2563eb;color:white;font-size:13px;font-weight:700;padding:13px;border-radius:10px;border:none;cursor:pointer">
            Submit Card Request
        </button>
    </form>

    {{-- Past Requests for This Account --}}
    @if($existingRequests->isNotEmpty())
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);overflow:hidden;margin-top:24px">
        <div style="padding:16px 22px;border-bottom:1px solid #f3f4f6">
            <p style="font-size:13px;font-weight:700;color:#111827;margin:0">Previous Requests for This Account</p>
        </div>
        <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse;font-size:13px">
                <thead>
                    <tr style="background:#f9fafb">
                        <th style="padding:10px 22px;text-align:left;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em">Reference</th>
                        <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em">Type</th>
                        <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em">Status</th>
                        <th style="padding:10px 16px;text-align:left;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($existingRequests as $req)
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
                    @endphp
                    <tr style="border-top:1px solid #f3f4f6">
                        <td style="padding:13px 22px;font-weight:700;color:#374151;font-family:monospace;font-size:12px">{{ $req->reference }}</td>
                        <td style="padding:13px 16px;color:#374151;font-weight:600">{{ $typeLabels[$req->request_type] ?? $req->request_type }}</td>
                        <td style="padding:13px 16px">
                            <span style="font-size:11px;font-weight:700;padding:4px 10px;border-radius:99px;background:{{ $rs['bg'] }};color:{{ $rs['text'] }};border:1px solid {{ $rs['border'] }};text-transform:uppercase;letter-spacing:.04em">
                                {{ $req->status }}
                            </span>
                        </td>
                        <td style="padding:13px 16px;color:#9ca3af;font-size:12px;white-space:nowrap">{{ \Carbon\Carbon::parse($req->created_at)->format('d M Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>

<script>
function toggleCollectionFields(method) {
    document.getElementById('branch-field').style.display   = method === 'branch_pickup' ? 'block' : 'none';
    document.getElementById('delivery-field').style.display = method === 'home_delivery'  ? 'block' : 'none';
    document.querySelectorAll('.cmethod-card').forEach(function(card) {
        var selected = card.dataset.method === method;
        card.style.borderColor = selected ? '#2563eb' : '#e5e7eb';
        card.style.background  = selected ? '#eff6ff' : 'white';
    });
}
</script>

@endsection
