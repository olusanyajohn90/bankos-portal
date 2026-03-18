@extends('layouts.portal')
@section('title', 'Exchange Rates')

@section('content')

{{-- Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px">
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#111827">Exchange Rates</h1>
        <p style="font-size:12px;color:#9ca3af;margin-top:3px">NGN indicative rates &mdash; updated periodically</p>
    </div>
    <div style="display:flex;align-items:center;gap:6px;padding:7px 14px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:20px">
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        <span style="font-size:11px;font-weight:700;color:#15803d">Live Rates</span>
    </div>
</div>

{{-- Currency converter --}}
<div style="background:linear-gradient(135deg,#1e40af,#2563eb);border-radius:14px;padding:24px;color:white;margin-bottom:20px;position:relative;overflow:hidden">
    <div style="position:absolute;top:-30px;right:-30px;width:140px;height:140px;border-radius:50%;background:rgba(255,255,255,0.06)"></div>
    <p style="font-size:11px;font-weight:700;color:rgba(255,255,255,0.65);text-transform:uppercase;letter-spacing:.08em;margin-bottom:16px">Currency Converter</p>
    <div style="display:grid;grid-template-columns:1fr 48px 1fr;gap:12px;align-items:end">
        <div>
            <label style="font-size:11px;font-weight:600;color:rgba(255,255,255,0.65);display:block;margin-bottom:6px">Amount (NGN)</label>
            <div style="position:relative">
                <span style="position:absolute;left:11px;top:50%;transform:translateY(-50%);font-size:12px;font-weight:700;color:rgba(255,255,255,0.6)">NGN</span>
                <input type="number" id="ngn-amount" step="100" oninput="convert()" placeholder="100,000"
                       style="width:100%;padding:10px 12px 10px 44px;border:none;border-radius:9px;font-size:15px;font-weight:700;outline:none;background:rgba(255,255,255,0.15);color:white;box-sizing:border-box">
            </div>
        </div>
        <div style="text-align:center;padding-bottom:10px">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.5)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
        </div>
        <div>
            <label style="font-size:11px;font-weight:600;color:rgba(255,255,255,0.65);display:block;margin-bottom:6px">Converted Amount</label>
            <div style="display:grid;grid-template-columns:1fr auto;gap:6px">
                <input type="text" id="converted" readonly placeholder="—"
                       style="width:100%;padding:10px 12px;border:none;border-radius:9px;font-size:15px;font-weight:700;background:rgba(255,255,255,0.15);color:white;box-sizing:border-box;outline:none">
                <select id="currency-select" onchange="convert()"
                        style="padding:10px 8px;border:none;border-radius:9px;background:rgba(255,255,255,0.2);color:white;font-size:13px;font-weight:600;outline:none;cursor:pointer">
                    @foreach($rates as $code => $rate)
                    <option value="{{ $rate['mid'] }}">{{ $code }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <p style="font-size:11px;color:rgba(255,255,255,0.45);margin-top:12px">Rates shown are indicative only. Final rates may vary at time of transaction.</p>
</div>

{{-- Rates table --}}
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);overflow:hidden">
    <table style="width:100%;border-collapse:collapse">
        <thead>
            <tr>
                <th style="background:#f8fafc;padding:12px 18px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;border-bottom:2px solid #e5e7eb;text-align:left">Currency</th>
                <th style="background:#f8fafc;padding:12px 18px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;border-bottom:2px solid #e5e7eb;text-align:right">Buy (NGN)</th>
                <th style="background:#f8fafc;padding:12px 18px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;border-bottom:2px solid #e5e7eb;text-align:right">Sell (NGN)</th>
                <th style="background:#f8fafc;padding:12px 18px;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;border-bottom:2px solid #e5e7eb;text-align:right">Mid Rate</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rates as $code => $rate)
            <tr onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='white'">
                <td style="padding:14px 18px;font-size:13px;color:#374151;border-bottom:1px solid #f3f4f6">
                    <div style="display:flex;align-items:center;gap:12px">
                        <div style="width:38px;height:38px;border-radius:10px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                        </div>
                        <div>
                            <p style="font-size:13px;font-weight:800;color:#111827">{{ $code }}</p>
                            <p style="font-size:11px;color:#9ca3af">{{ $rate['name'] ?? $code }}</p>
                        </div>
                    </div>
                </td>
                <td style="padding:14px 18px;font-size:13px;color:#374151;border-bottom:1px solid #f3f4f6;text-align:right;font-weight:600">{{ number_format($rate['buy'], 2) }}</td>
                <td style="padding:14px 18px;font-size:13px;color:#374151;border-bottom:1px solid #f3f4f6;text-align:right;font-weight:600">{{ number_format($rate['sell'], 2) }}</td>
                <td style="padding:14px 18px;font-size:13px;border-bottom:1px solid #f3f4f6;text-align:right;font-weight:800;color:#2563eb">{{ number_format($rate['mid'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<p style="font-size:11px;color:#9ca3af;margin-top:12px;text-align:center;line-height:1.5">
    All rates shown are NGN per 1 unit of foreign currency. Buy = bank buys from you &bull; Sell = bank sells to you.
</p>

<script>
const rates = @json($rates);
function convert() {
    const ngn = parseFloat(document.getElementById('ngn-amount').value) || 0;
    const mid = parseFloat(document.getElementById('currency-select').value) || 1;
    const result = ngn / mid;
    document.getElementById('converted').value = result > 0
        ? result.toLocaleString('en-NG', { minimumFractionDigits: 4, maximumFractionDigits: 4 })
        : '';
}
</script>
@endsection
