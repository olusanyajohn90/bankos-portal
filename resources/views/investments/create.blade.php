@extends('layouts.portal')
@section('title', 'New Investment')

@section('content')

{{-- Back + title --}}
<div style="display:flex;align-items:center;gap:12px;margin-bottom:28px">
    <a href="{{ route('investments') }}"
       style="width:36px;height:36px;border-radius:10px;background:white;border:1px solid #e5e7eb;display:flex;align-items:center;justify-content:center;color:#6b7280;text-decoration:none;flex-shrink:0">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#111827;margin-bottom:2px">New Fixed Deposit</h1>
        <p style="font-size:13px;color:#6b7280">Earn guaranteed interest on your savings</p>
    </div>
</div>

<div style="max-width:580px">
    @if($errors->any())
    <div style="margin-bottom:18px;padding:12px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;color:#991b1b;font-size:13px;font-weight:600">{{ $errors->first() }}</div>
    @endif

    {{-- Duration / product selector --}}
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px">Select Duration</p>
    <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:8px;margin-bottom:24px">
        @foreach($durations as $days => $dur)
        <label style="cursor:pointer;text-align:center">
            <input type="radio" name="_dur_display" value="{{ $days }}" style="display:none" {{ $days==90?'checked':'' }}
                   onchange="document.getElementById('duration_days').value=this.value;document.querySelectorAll('.dur-card').forEach(c=>{c.style.background='white';c.style.borderColor='#e5e7eb';c.style.color='#374151'});this.closest('label').querySelector('.dur-card').style.background='#15803d';this.closest('label').querySelector('.dur-card').style.borderColor='#15803d';this.closest('label').querySelector('.dur-card').style.color='white';calcPreview()">
            <div class="dur-card"
                 style="padding:14px 6px;border:2px solid {{ $days==90?'#15803d':'#e5e7eb' }};border-radius:12px;background:{{ $days==90?'#15803d':'white' }};color:{{ $days==90?'white':'#374151' }};transition:.15s">
                <p style="font-size:11px;font-weight:700;margin-bottom:3px">{{ $dur['label'] }}</p>
                <p style="font-size:16px;font-weight:800;margin-bottom:1px">{{ $dur['rate'] }}%</p>
                <p style="font-size:10px;opacity:.75">p.a.</p>
            </div>
        </label>
        @endforeach
    </div>

    <form method="POST" action="{{ route('investments.store') }}">
        @csrf
        <input type="hidden" name="duration_days" id="duration_days" value="90">

        {{-- Investment details card --}}
        <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:24px;margin-bottom:14px">
            <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:20px">Investment Details</p>

            {{-- Investment name --}}
            <div style="margin-bottom:18px">
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Investment Name <span style="color:#dc2626">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}"
                       placeholder='e.g. "Emergency Fund", "School Fees Save"'
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;outline:none;box-sizing:border-box" required>
            </div>

            {{-- Amount --}}
            <div style="margin-bottom:18px">
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Amount (NGN) <span style="color:#dc2626">*</span></label>
                <input type="number" name="principal" id="principal" min="5000" step="1000" placeholder="e.g. 100,000"
                       style="width:100%;padding:14px 16px;border:1px solid #d1d5db;border-radius:9px;font-size:22px;font-weight:800;color:#111827;outline:none;box-sizing:border-box;letter-spacing:-0.5px"
                       required oninput="calcPreview()">
                <p style="font-size:11px;color:#9ca3af;margin-top:5px">Minimum: NGN 5,000</p>
            </div>

            {{-- Funding account --}}
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Funding Account <span style="color:#dc2626">*</span></label>
                <select name="account_id" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;outline:none;background:white;box-sizing:border-box" required>
                    @foreach($accounts as $acct)
                    <option value="{{ $acct->id }}">{{ $acct->account_name }} — NGN {{ number_format($acct->available_balance, 2) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Live investment preview card --}}
        <div id="preview-card" style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:14px;padding:20px;margin-bottom:14px;display:none">
            <p style="font-size:11px;font-weight:700;color:#166534;text-transform:uppercase;letter-spacing:.06em;margin-bottom:14px">Investment Preview</p>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:12px">
                <div>
                    <p style="font-size:11px;font-weight:700;color:#16a34a;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Principal</p>
                    <p id="p-principal" style="font-size:16px;font-weight:800;color:#166534">—</p>
                </div>
                <div>
                    <p style="font-size:11px;font-weight:700;color:#16a34a;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Interest</p>
                    <p id="p-interest" style="font-size:16px;font-weight:800;color:#166534">—</p>
                </div>
                <div>
                    <p style="font-size:11px;font-weight:700;color:#16a34a;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">At Maturity</p>
                    <p id="p-maturity" style="font-size:16px;font-weight:800;color:#166534">—</p>
                </div>
            </div>
            <div style="border-top:1px solid #bbf7d0;padding-top:10px">
                <p id="p-date" style="font-size:13px;font-weight:600;color:#15803d">—</p>
            </div>
        </div>

        {{-- Warning notice --}}
        <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:12px;padding:14px 16px;margin-bottom:20px">
            <p style="font-size:12px;color:#92400e;line-height:1.6">Early liquidation incurs a 10% penalty on accrued interest. Your principal is always returned in full.</p>
        </div>

        <button type="submit"
                style="width:100%;background:#15803d;color:white;font-size:13px;font-weight:700;padding:14px 20px;border-radius:10px;border:none;cursor:pointer">
            Create Investment
        </button>
    </form>
</div>

<script>
const durations = @json($durations);
function calcPreview() {
    const principal = parseFloat(document.getElementById('principal').value) || 0;
    const days = parseInt(document.getElementById('duration_days').value) || 90;
    const card = document.getElementById('preview-card');
    if (principal < 5000) { card.style.display = 'none'; return; }
    const dur = durations[days];
    if (!dur) return;
    const rate = dur.rate;
    const interest = principal * (rate / 100) * (days / 365);
    const maturity = principal + interest;
    const mDate = new Date();
    mDate.setDate(mDate.getDate() + days);
    document.getElementById('p-principal').textContent = 'NGN ' + principal.toLocaleString();
    document.getElementById('p-interest').textContent  = 'NGN ' + interest.toLocaleString('en-NG', {minimumFractionDigits:2, maximumFractionDigits:2});
    document.getElementById('p-maturity').textContent  = 'NGN ' + maturity.toLocaleString('en-NG', {minimumFractionDigits:2, maximumFractionDigits:2});
    document.getElementById('p-date').textContent      = 'Matures on ' + mDate.toLocaleDateString('en-NG', {day:'numeric', month:'long', year:'numeric'});
    card.style.display = 'block';
}
</script>
@endsection
