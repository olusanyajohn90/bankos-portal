<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Expired — bankOS</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f3f6fb; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px 16px; }
    </style>
</head>
<body>
<div style="width:100%;max-width:420px;text-align:center">

    {{-- Card --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:48px 32px">

        {{-- Icon --}}
        <div style="width:72px;height:72px;border-radius:50%;background:#fef2f2;border:2px solid #fecaca;display:flex;align-items:center;justify-content:center;margin:0 auto 20px">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
            </svg>
        </div>

        {{-- Heading --}}
        <h1 style="font-size:20px;font-weight:800;color:#111827;margin-bottom:10px">Request Expired</h1>

        {{-- Message --}}
        <p style="font-size:14px;color:#6b7280;line-height:1.65;margin-bottom:24px">
            This payment request has expired or been cancelled and is no longer active.
            Please contact the sender to generate a new payment request.
        </p>

        {{-- Divider --}}
        <div style="height:1px;background:#f3f4f6;margin-bottom:24px"></div>

        {{-- What to do --}}
        <div style="background:#f8fafc;border-radius:10px;padding:16px;text-align:left;margin-bottom:24px">
            <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px">What to do next</p>
            <div style="display:flex;flex-direction:column;gap:10px">
                <div style="display:flex;align-items:flex-start;gap:10px">
                    <div style="width:22px;height:22px;border-radius:50%;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px">
                        <span style="font-size:11px;font-weight:800;color:#2563eb">1</span>
                    </div>
                    <p style="font-size:13px;color:#374151;line-height:1.5">Contact the person who sent you this payment request</p>
                </div>
                <div style="display:flex;align-items:flex-start;gap:10px">
                    <div style="width:22px;height:22px;border-radius:50%;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px">
                        <span style="font-size:11px;font-weight:800;color:#2563eb">2</span>
                    </div>
                    <p style="font-size:13px;color:#374151;line-height:1.5">Ask them to send a new payment request link</p>
                </div>
            </div>
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
