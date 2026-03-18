<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Complete — bankOS</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f3f6fb; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px 16px; }
        @keyframes popIn { 0%{transform:scale(0.7);opacity:0} 70%{transform:scale(1.08)} 100%{transform:scale(1);opacity:1} }
        .icon-animate { animation: popIn .5s cubic-bezier(.175,.885,.32,1.275) both; }
    </style>
</head>
<body>
<div style="width:100%;max-width:420px;text-align:center">

    {{-- Card --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:48px 32px">

        {{-- Animated checkmark --}}
        <div class="icon-animate" style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#16a34a,#15803d);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;box-shadow:0 8px 24px rgba(21,128,61,0.25)">
            <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
        </div>

        {{-- Heading --}}
        <h1 style="font-size:22px;font-weight:800;color:#111827;margin-bottom:6px">Payment Complete</h1>
        <p style="font-size:13px;color:#9ca3af;margin-bottom:28px">Transaction successfully processed</p>

        {{-- Amount hero --}}
        <div style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1px solid #bbf7d0;border-radius:12px;padding:22px;margin-bottom:20px">
            <p style="font-size:12px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px">Amount Paid</p>
            <p style="font-size:34px;font-weight:800;color:#15803d;letter-spacing:-0.5px;line-height:1">NGN {{ number_format($req->amount, 2) }}</p>
        </div>

        {{-- Details grid --}}
        <div style="display:flex;flex-direction:column;gap:0;border:1px solid #f3f4f6;border-radius:10px;overflow:hidden;margin-bottom:24px;text-align:left">
            @if($req->paid_by_name)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 16px;border-bottom:1px solid #f9fafb">
                <span style="font-size:12px;color:#9ca3af">Paid by</span>
                <span style="font-size:13px;font-weight:700;color:#111827">{{ $req->paid_by_name }}</span>
            </div>
            @endif
            <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 16px;border-bottom:1px solid #f9fafb">
                <span style="font-size:12px;color:#9ca3af">Date &amp; Time</span>
                <span style="font-size:13px;font-weight:600;color:#374151">{{ $req->paid_at?->format('d M Y, H:i') }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 16px">
                <span style="font-size:12px;color:#9ca3af">Reference</span>
                <span style="font-size:12px;font-weight:700;color:#374151;font-family:monospace">{{ $req->reference }}</span>
            </div>
        </div>

        {{-- Status badge --}}
        <div style="display:inline-flex;align-items:center;gap:6px;background:#f0fdf4;border:1px solid #bbf7d0;padding:7px 16px;border-radius:20px;margin-bottom:28px">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            <span style="font-size:12px;font-weight:700;color:#15803d">Verified &amp; Confirmed</span>
        </div>

        {{-- bankOS badge --}}
        <div style="display:flex;align-items:center;justify-content:center;gap:8px">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
            <span style="font-size:12px;font-weight:700;color:#d1d5db">bank<span style="color:#9ca3af">OS</span></span>
        </div>
    </div>

    <p style="font-size:11px;color:#d1d5db;margin-top:16px">&copy; {{ date('Y') }} bankOS &mdash; Secured Payment Platform</p>
</div>
</body>
</html>
