<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay NGN {{ number_format($req->amount, 2) }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box }
        body {
            font-family:-apple-system,BlinkMacSystemFont,'Segoe UI','Inter',sans-serif;
            background:#f1f5f9;
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:24px 16px;
        }
    </style>
</head>
<body>

<div style="width:100%;max-width:420px">

    {{-- Brand mark --}}
    <div style="text-align:center;margin-bottom:24px">
        <div style="width:52px;height:52px;border-radius:14px;background:#2563eb;display:inline-flex;align-items:center;justify-content:center;margin-bottom:10px;box-shadow:0 4px 14px rgba(37,99,235,0.35)">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
        </div>
        <p style="font-size:13px;font-weight:600;color:#6b7280">Secure payment request</p>
    </div>

    {{-- Main card --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,0.07);overflow:hidden;margin-bottom:14px">

        {{-- Amount hero --}}
        <div style="background:linear-gradient(135deg,#1d4ed8,#2563eb);padding:32px 28px;text-align:center">
            <p style="font-size:12px;font-weight:600;color:rgba(255,255,255,0.7);text-transform:uppercase;letter-spacing:.08em;margin-bottom:10px">Amount Requested</p>
            <p style="font-size:40px;font-weight:800;color:white;letter-spacing:-.5px">NGN {{ number_format($req->amount, 2) }}</p>
            @if($req->description)
            <p style="font-size:13px;color:rgba(255,255,255,0.8);margin-top:10px;font-style:italic">"{{ $req->description }}"</p>
            @endif
        </div>

        {{-- Details --}}
        <div style="padding:22px 24px">
            <div style="margin-bottom:20px">
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f3f4f6">
                    <span style="font-size:12px;color:#9ca3af;font-weight:500">Requested by</span>
                    <span style="font-size:12px;font-weight:700;color:#111827">{{ $req->customer->first_name ?? '' }} {{ $req->customer->last_name ?? '' }}</span>
                </div>
                @if($req->recipient_name)
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f3f4f6">
                    <span style="font-size:12px;color:#9ca3af;font-weight:500">Intended for</span>
                    <span style="font-size:12px;font-weight:700;color:#111827">{{ $req->recipient_name }}</span>
                </div>
                @endif
                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f3f4f6">
                    <span style="font-size:12px;color:#9ca3af;font-weight:500">Reference</span>
                    <span style="font-size:11px;font-weight:700;color:#374151;font-family:monospace">{{ $req->reference }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:10px 0">
                    <span style="font-size:12px;color:#9ca3af;font-weight:500">Expires</span>
                    <span style="font-size:12px;color:#374151;font-weight:600">{{ $req->expires_at?->format('d M Y') ?? 'No expiry' }}</span>
                </div>
            </div>

            @if($errors->any())
            <div style="margin-bottom:16px;padding:12px 14px;background:#fef2f2;border:1px solid #fecaca;border-radius:9px;color:#991b1b;font-size:13px;font-weight:500">
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('pay-request.public.pay', $req->reference) }}">
                @csrf
                <div style="margin-bottom:16px">
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Your Full Name</label>
                    <input type="text" name="payer_name" value="{{ old('payer_name') }}"
                           placeholder="Enter your full name" required
                           style="width:100%;padding:11px 14px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none">
                </div>
                <button type="submit"
                        style="width:100%;background:#2563eb;color:white;font-size:14px;font-weight:700;padding:14px;border-radius:10px;border:none;cursor:pointer;box-shadow:0 2px 8px rgba(37,99,235,0.3)">
                    Pay NGN {{ number_format($req->amount, 2) }}
                </button>
            </form>
        </div>
    </div>

    {{-- Trust footer --}}
    <p style="text-align:center;font-size:11px;color:#9ca3af;line-height:1.7">
        Secured by bankOS &nbsp;·&nbsp; End-to-end encrypted &nbsp;·&nbsp; Ref: {{ $req->reference }}
    </p>

</div>

</body>
</html>
