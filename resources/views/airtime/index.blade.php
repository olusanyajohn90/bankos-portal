@extends('layouts.portal')
@section('title', 'Airtime & Data')

@section('content')
<div style="margin-bottom:28px">
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Payments</p>
    <h1 style="font-size:20px;font-weight:800;color:#111827;margin:0">Airtime &amp; Data</h1>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start">

    {{-- Purchase Form --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:24px">

        {{-- Airtime / Data toggle --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;background:#f3f4f6;border-radius:11px;padding:4px;margin-bottom:22px">
            <button onclick="setType('airtime')" id="btn-airtime"
                    style="padding:10px;border:none;border-radius:9px;font-size:13px;font-weight:700;cursor:pointer;background:#2563eb;color:white;display:flex;align-items:center;justify-content:center;gap:6px">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.42A2 2 0 0 1 3.58 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.5a16 16 0 0 0 6.29 6.29l.91-.91a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                Airtime
            </button>
            <button onclick="setType('data')" id="btn-data"
                    style="padding:10px;border:none;border-radius:9px;font-size:13px;font-weight:600;cursor:pointer;background:transparent;color:#6b7280;display:flex;align-items:center;justify-content:center;gap:6px">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 6l4-3 4 3M1 18l4 3 4-3M1 12h8M15 6l4-3 4 3M15 18l4 3 4-3M15 12h8"/></svg>
                Data
            </button>
        </div>

        <form method="POST" action="{{ route('airtime.store') }}" id="airtime-form">
            @csrf
            <input type="hidden" name="type" id="type-input" value="airtime">

            {{-- Debit Account --}}
            <div style="margin-bottom:16px">
                <label style="display:block;font-size:11px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Debit Account</label>
                <div style="position:relative">
                    <select name="account_id"
                            style="width:100%;padding:10px 36px 10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;font-weight:500;color:#111827;box-sizing:border-box;outline:none;appearance:none;background:white;cursor:pointer" required>
                        @foreach($accounts as $acct)
                        <option value="{{ $acct->id }}">{{ $acct->account_name }} — {{ $acct->account_number }} (₦{{ number_format($acct->balance,0) }})</option>
                        @endforeach
                    </select>
                    <div style="position:absolute;right:12px;top:50%;transform:translateY(-50%);pointer-events:none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </div>
            </div>

            {{-- Network Provider --}}
            <div style="margin-bottom:16px">
                <label style="display:block;font-size:11px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:10px">Network Provider</label>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:8px">
                    @foreach($networks as $key => $net)
                    <label style="cursor:pointer">
                        <input type="radio" name="network" value="{{ $key }}" class="network-radio" style="display:none" {{ $loop->first ? 'checked' : '' }} onchange="networkChanged('{{ $key }}')">
                        <div id="net-{{ $key }}"
                             style="text-align:center;padding:11px 6px;border:2px solid {{ $loop->first ? $net['color'] : '#e5e7eb' }};border-radius:12px;background:{{ $loop->first ? $net['color'].'18' : 'white' }};cursor:pointer;transition:all .12s">
                            <p style="font-size:11px;font-weight:800;color:{{ $net['color'] }};margin:0;letter-spacing:.02em">{{ $net['label'] }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Phone Number --}}
            <div style="margin-bottom:16px">
                <label style="display:block;font-size:11px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Phone Number</label>
                <input type="tel" name="phone" id="phone-input" placeholder="08012345678" maxlength="14"
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:16px;font-weight:700;color:#111827;box-sizing:border-box;outline:none;letter-spacing:2px" required
                       oninput="autoDetectNetwork(this.value)">
                <p id="network-hint" style="font-size:11px;font-weight:600;margin:4px 0 0 0"></p>
            </div>

            {{-- Airtime amount chips --}}
            <div id="airtime-amount-section" style="margin-bottom:18px">
                <label style="display:block;font-size:11px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:10px">Amount (₦)</label>
                <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:10px">
                    @foreach([100, 200, 500, 1000, 2000, 5000] as $amt)
                    <button type="button" onclick="setAmt({{ $amt }})"
                            style="padding:8px 16px;border:1.5px solid #e5e7eb;border-radius:20px;font-size:12px;font-weight:700;cursor:pointer;background:white;color:#374151"
                            class="amt-chip" data-amt="{{ $amt }}">
                        ₦{{ number_format($amt,0) }}
                    </button>
                    @endforeach
                </div>
                <div style="position:relative">
                    <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:13px;font-weight:700;color:#6b7280">₦</span>
                    <input type="number" name="amount" id="amount-input" placeholder="Custom amount" min="50" max="50000"
                           style="width:100%;padding:10px 12px 10px 28px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;color:#111827;box-sizing:border-box;outline:none">
                </div>
            </div>

            {{-- Data plans --}}
            <div id="data-section" style="display:none;margin-bottom:18px">
                <label style="display:block;font-size:11px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:10px">Select Data Plan</label>
                <div id="data-plans-grid" style="display:flex;flex-direction:column;gap:8px"></div>
                <input type="hidden" name="data_plan" id="data-plan-input">
                <input type="hidden" name="amount" id="data-amount-input" value="0">
            </div>

            <button type="submit"
                    style="width:100%;background:#2563eb;color:white;font-size:14px;font-weight:800;padding:13px;border-radius:11px;border:none;cursor:pointer;letter-spacing:.01em">
                Buy Now
            </button>
        </form>
    </div>

    {{-- History --}}
    <div>
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px">Recent Orders</p>
        @if($history->isEmpty())
        <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;padding:48px 24px;text-align:center;box-shadow:0 1px 3px rgba(0,0,0,0.06)">
            <div style="width:52px;height:52px;border-radius:14px;background:#eff6ff;display:flex;align-items:center;justify-content:center;margin:0 auto 12px auto">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M1 6l4-3 4 3M1 18l4 3 4-3M1 12h8M15 6l4-3 4 3M15 18l4 3 4-3M15 12h8"/></svg>
            </div>
            <p style="font-size:13px;font-weight:600;color:#374151;margin:0 0 4px 0">No orders yet</p>
            <p style="font-size:12px;color:#9ca3af;margin:0">Your airtime and data purchases will appear here</p>
        </div>
        @else
        <div style="display:flex;flex-direction:column;gap:8px">
            @foreach($history as $h)
            @php $net = $networks[$h->network] ?? ['label'=>strtoupper($h->network),'color'=>'#6b7280']; @endphp
            <div style="background:white;border:1px solid #e5e7eb;border-radius:12px;padding:14px 16px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 1px 3px rgba(0,0,0,0.04)">
                <div style="display:flex;align-items:center;gap:10px">
                    <div style="width:40px;height:40px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0" style="background:{{ $net['color'] }}18">
                        <div style="width:40px;height:40px;border-radius:12px;background:{{ $net['color'] }}18;display:flex;align-items:center;justify-content:center">
                            @if($h->type === 'data')
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="{{ $net['color'] }}" stroke-width="2"><path d="M1 6l4-3 4 3M1 18l4 3 4-3M1 12h8M15 6l4-3 4 3M15 18l4 3 4-3M15 12h8"/></svg>
                            @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="{{ $net['color'] }}" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.42A2 2 0 0 1 3.58 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.5a16 16 0 0 0 6.29 6.29l.91-.91a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            @endif
                        </div>
                    </div>
                    <div>
                        <p style="font-size:13px;font-weight:700;color:#111827;margin:0">{{ $h->phone }}</p>
                        <p style="font-size:11px;color:#9ca3af;margin:1px 0 0 0">{{ $net['label'] }} · {{ $h->type === 'data' ? $h->data_plan : 'Airtime' }}</p>
                        <p style="font-size:10px;color:#9ca3af;margin:1px 0 0 0">{{ $h->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
                <div style="text-align:right">
                    <p style="font-size:14px;font-weight:800;color:#dc2626;margin:0">−₦{{ number_format($h->amount,0) }}</p>
                    <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;background:{{ $h->status==='completed'?'#f0fdf4':'#fef2f2' }};color:{{ $h->status==='completed'?'#16a34a':'#dc2626' }};display:inline-block;margin-top:4px">{{ strtoupper($h->status) }}</span>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

<script>
const dataPlans    = @json($dataPlans);
const networksData = @json($networks);
let currentType    = 'airtime';
let currentNetwork = '{{ array_key_first($networks) }}';

function setType(type) {
    currentType = type;
    document.getElementById('type-input').value = type;
    const isData = type === 'data';
    document.getElementById('airtime-amount-section').style.display = isData ? 'none' : 'block';
    document.getElementById('data-section').style.display            = isData ? 'block' : 'none';
    const amountInput = document.getElementById('amount-input');
    if(amountInput) amountInput.required = !isData;

    const btnAirtime = document.getElementById('btn-airtime');
    const btnData    = document.getElementById('btn-data');
    btnAirtime.style.background = isData ? 'transparent' : '#2563eb';
    btnAirtime.style.color      = isData ? '#6b7280' : 'white';
    btnData.style.background    = isData ? '#2563eb' : 'transparent';
    btnData.style.color         = isData ? 'white' : '#6b7280';

    if(isData) renderDataPlans();
}

function networkChanged(net) {
    currentNetwork = net;
    Object.keys(networksData).forEach(k => {
        const el    = document.getElementById('net-'+k);
        const color = networksData[k].color;
        el.style.borderColor = k === net ? color : '#e5e7eb';
        el.style.background  = k === net ? color+'18' : 'white';
    });
    if(currentType === 'data') renderDataPlans();
}

function renderDataPlans() {
    const plans = dataPlans[currentNetwork] || [];
    const grid  = document.getElementById('data-plans-grid');
    if(!plans.length) {
        grid.innerHTML = '<p style="font-size:12px;color:#9ca3af;text-align:center;padding:12px">No data plans available for this network.</p>';
        return;
    }
    grid.innerHTML = plans.map((p, i) => `
        <div onclick="selectPlan('${p.plan}', ${p.amount}, ${i})" id="plan-row-${i}"
             style="display:flex;justify-content:space-between;align-items:center;padding:12px 14px;border:2px solid #e5e7eb;border-radius:10px;background:white;cursor:pointer">
            <div>
                <p style="font-size:13px;font-weight:700;color:#111827;margin:0">${p.plan}</p>
                ${p.validity ? `<p style="font-size:11px;color:#9ca3af;margin:1px 0 0 0">${p.validity}</p>` : ''}
            </div>
            <p style="font-size:14px;font-weight:800;color:#2563eb;margin:0">₦${p.amount.toLocaleString()}</p>
        </div>`).join('');
}

let selectedPlanIdx = null;
function selectPlan(plan, amount, idx) {
    document.getElementById('data-plan-input').value  = plan;
    document.getElementById('data-amount-input').value = amount;
    document.querySelectorAll('[id^="plan-row-"]').forEach((el, i) => {
        el.style.borderColor = '#e5e7eb';
        el.style.background  = 'white';
    });
    const row = document.getElementById('plan-row-'+idx);
    if(row) { row.style.borderColor = '#2563eb'; row.style.background = '#eff6ff'; }
    selectedPlanIdx = idx;
}

function setAmt(amt) {
    document.getElementById('amount-input').value = amt;
    document.querySelectorAll('.amt-chip').forEach(b => {
        const sel = parseInt(b.dataset.amt) === amt;
        b.style.background  = sel ? '#eff6ff' : 'white';
        b.style.borderColor = sel ? '#2563eb' : '#e5e7eb';
        b.style.color       = sel ? '#1d4ed8' : '#374151';
    });
}

function autoDetectNetwork(phone) {
    const clean  = phone.replace(/\D/g,'');
    const prefix = clean.substring(0, 4);
    const hint   = document.getElementById('network-hint');
    let detected = null;
    Object.entries(networksData).forEach(([key, net]) => {
        if(net.prefixes && net.prefixes.includes(prefix)) detected = {key, net};
    });
    if(detected) {
        hint.textContent = 'Detected: ' + detected.net.label;
        hint.style.color = detected.net.color;
        const radio = document.querySelector(`input[name="network"][value="${detected.key}"]`);
        if(radio) { radio.checked = true; networkChanged(detected.key); }
    } else {
        hint.textContent = '';
    }
}
</script>
@endsection
