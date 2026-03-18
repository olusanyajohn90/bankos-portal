<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — bankOS Portal</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Inter', sans-serif;
            min-height: 100vh;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
            position: relative;
            overflow-x: hidden;
        }
        /* Subtle radial background decoration */
        body::before {
            content: '';
            position: fixed;
            top: -120px;
            left: 50%;
            transform: translateX(-50%);
            width: 700px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(ellipse at center, rgba(37,99,235,0.07) 0%, transparent 70%);
            pointer-events: none;
        }
        input:focus {
            outline: none;
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
        }
        .btn-primary:hover { background: #1d4ed8 !important; }
        .link-blue:hover { color: #1d4ed8 !important; }
        .back-link:hover { color: #374151 !important; }
    </style>
</head>
<body>
    <div style="width:100%;max-width:440px;position:relative;z-index:1">

        {{-- Logo / brand header --}}
        <div style="text-align:center;margin-bottom:30px">
            <div style="display:inline-flex;width:58px;height:58px;border-radius:16px;background:linear-gradient(135deg,#1e40af 0%,#2563eb 100%);align-items:center;justify-content:center;margin-bottom:16px;box-shadow:0 4px 14px rgba(37,99,235,0.3)">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
            </div>
            <h1 style="font-size:26px;font-weight:800;color:#111827;letter-spacing:-0.5px">
                bank<span style="color:#2563eb">OS</span>
            </h1>
            <p style="font-size:13px;color:#9ca3af;margin-top:4px;font-weight:500">Secure Internet Banking</p>
        </div>

        {{-- Card --}}
        <div style="background:white;border:1px solid #e5e7eb;border-radius:18px;padding:36px 32px;box-shadow:0 4px 24px rgba(0,0,0,0.06)">

            <h2 style="font-size:18px;font-weight:700;color:#111827;margin-bottom:6px">Welcome back</h2>
            <p style="font-size:13px;color:#6b7280;margin-bottom:28px;line-height:1.5">Sign in to access your accounts and services.</p>

            {{-- Success notice --}}
            @if(session('success'))
            <div style="margin-bottom:18px;padding:12px 14px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:9px;font-size:13px;color:#15803d;display:flex;align-items:flex-start;gap:8px">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="2.5" style="flex-shrink:0;margin-top:1px"><polyline points="20 6 9 17 4 12"/></svg>
                {{ session('success') }}
            </div>
            @endif

            {{-- Error notice --}}
            @if($errors->any())
            <div style="margin-bottom:18px;padding:12px 14px;background:#fef2f2;border:1px solid #fecaca;border-radius:9px;font-size:13px;color:#dc2626;display:flex;align-items:flex-start;gap:8px">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <div style="margin-bottom:18px">
                    <label for="email" style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:6px;text-transform:uppercase;letter-spacing:.04em">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           required autofocus autocomplete="email"
                           placeholder="you@example.com"
                           style="width:100%;padding:11px 13px;border:1px solid #d1d5db;border-radius:10px;font-size:13px;color:#111827;background:white;transition:border-color .15s,box-shadow .15s">
                </div>

                {{-- Password --}}
                <div style="margin-bottom:8px">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
                        <label for="password" style="font-size:12px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.04em">Password / PIN</label>
                        <a href="{{ route('password.forgot') }}" class="link-blue" style="font-size:12px;color:#2563eb;text-decoration:none;font-weight:600;transition:color .15s">Forgot PIN?</a>
                    </div>
                    <input type="password" id="password" name="password"
                           required autocomplete="current-password"
                           placeholder="Enter your password"
                           style="width:100%;padding:11px 13px;border:1px solid #d1d5db;border-radius:10px;font-size:13px;color:#111827;background:white;transition:border-color .15s,box-shadow .15s">
                </div>

                {{-- Remember me --}}
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:24px;margin-top:14px">
                    <input type="checkbox" id="remember" name="remember"
                           style="width:15px;height:15px;accent-color:#2563eb;cursor:pointer;border-radius:4px">
                    <label for="remember" style="font-size:13px;color:#6b7280;cursor:pointer;user-select:none">Keep me signed in</label>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn-primary"
                        style="width:100%;background:#2563eb;color:white;font-size:13px;font-weight:700;padding:13px 20px;border-radius:10px;border:none;cursor:pointer;letter-spacing:.02em;transition:background .15s;box-shadow:0 2px 8px rgba(37,99,235,0.25)">
                    Sign In to bankOS
                </button>
            </form>
        </div>

        {{-- New customer link --}}
        <p style="text-align:center;font-size:13px;color:#6b7280;margin-top:18px">
            New customer?
            <a href="{{ route('onboarding.start') }}" style="color:#2563eb;text-decoration:none;font-weight:600">Create account</a>
        </p>

        {{-- Security notice --}}
        <div style="display:flex;align-items:center;justify-content:center;gap:6px;margin-top:12px;padding:12px 16px;background:white;border:1px solid #f3f4f6;border-radius:10px;box-shadow:0 1px 3px rgba(0,0,0,0.04)">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
            <span style="font-size:11px;color:#9ca3af">256-bit SSL encrypted &middot; NDIC insured &middot; CBN licensed</span>
        </div>

        <p style="text-align:center;font-size:11px;color:#d1d5db;margin-top:16px">
            &copy; {{ date('Y') }} bankOS. All rights reserved.
        </p>
    </div>
</body>
</html>
