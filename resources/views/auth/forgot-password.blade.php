<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — bankOS Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f3f6fb; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px 16px; }
    </style>
</head>
<body>
<div style="width:100%;max-width:420px">

    {{-- Logo --}}
    <div style="text-align:center;margin-bottom:32px">
        <div style="display:inline-flex;width:56px;height:56px;border-radius:16px;background:#2563eb;align-items:center;justify-content:center;margin-bottom:14px;box-shadow:0 4px 14px rgba(37,99,235,0.35)">
            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
        </div>
        <div style="font-size:22px;font-weight:800;color:#111827;letter-spacing:-0.4px">bank<span style="color:#2563eb">OS</span></div>
        <div style="font-size:12px;color:#9ca3af;margin-top:2px;letter-spacing:.02em">Internet Banking Portal</div>
    </div>

    {{-- Card --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:32px">

        {{-- Icon + heading --}}
        <div style="display:flex;align-items:center;gap:14px;margin-bottom:6px">
            <div style="width:44px;height:44px;border-radius:12px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </div>
            <div>
                <h1 style="font-size:18px;font-weight:800;color:#111827">Reset Password</h1>
                <p style="font-size:12px;color:#9ca3af;margin-top:1px">We'll send a secure link to your email</p>
            </div>
        </div>

        <div style="height:1px;background:#f3f4f6;margin:18px 0"></div>

        {{-- Status / Success --}}
        @if(session('status'))
        <div style="margin-bottom:18px;padding:12px 16px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;display:flex;align-items:flex-start;gap:10px">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:1px"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <p style="font-size:13px;color:#15803d;line-height:1.5">{{ session('status') }}</p>
        </div>
        @endif

        {{-- Dev link --}}
        @if(session('dev_reset_url'))
        <div style="margin-bottom:18px;padding:12px 16px;background:#fffbeb;border:1px solid #fde68a;border-radius:10px">
            <p style="font-size:11px;font-weight:700;color:#92400e;margin-bottom:4px">Dev Mode — Reset Link</p>
            <a href="{{ session('dev_reset_url') }}" style="font-size:11px;color:#b45309;word-break:break-all;text-decoration:underline">{{ session('dev_reset_url') }}</a>
        </div>
        @endif

        {{-- Errors --}}
        @if($errors->any())
        <div style="margin-bottom:18px;padding:12px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;display:flex;align-items:flex-start;gap:10px">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <p style="font-size:13px;color:#991b1b">{{ $errors->first() }}</p>
        </div>
        @endif

        <form method="POST" action="{{ route('password.forgot') }}">
            @csrf
            <div style="margin-bottom:18px">
                <label for="email" style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Email Address</label>
                <div style="position:relative">
                    <div style="position:absolute;left:11px;top:50%;transform:translateY(-50%);pointer-events:none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </div>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                           placeholder="you@example.com"
                           style="width:100%;padding:10px 12px 10px 36px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none;transition:border-color .15s"
                           onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#d1d5db'">
                </div>
            </div>

            <button type="submit"
                    style="width:100%;background:#2563eb;color:white;font-size:13px;font-weight:700;padding:11px 20px;border-radius:10px;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:background .15s"
                    onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                Send Reset Link
            </button>
        </form>

        <div style="margin-top:20px;text-align:center">
            <a href="{{ route('login') }}" style="font-size:13px;color:#6b7280;text-decoration:none;display:inline-flex;align-items:center;gap:5px"
               onmouseover="this.style.color='#2563eb'" onmouseout="this.style.color='#6b7280'">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                Back to Sign In
            </a>
        </div>
    </div>

    <p style="text-align:center;font-size:11px;color:#d1d5db;margin-top:20px">
        &copy; {{ date('Y') }} bankOS &mdash; Secured Banking Portal
    </p>
</div>
</body>
</html>
