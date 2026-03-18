<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Device Verification — bankOS Portal</title>
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
        /* Individual OTP digit boxes */
        .otp-container {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 28px;
        }
        .otp-box {
            width: 52px;
            height: 60px;
            border: 1.5px solid #d1d5db;
            border-radius: 12px;
            font-size: 26px;
            font-weight: 700;
            color: #111827;
            text-align: center;
            background: white;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', monospace;
        }
        .otp-box:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
        }
        .otp-box.filled {
            border-color: #2563eb;
            background: #eff6ff;
            color: #1d4ed8;
        }
        .btn-primary:hover { background: #1d4ed8 !important; }
        .btn-resend:hover { background: #f9fafb !important; }
        .back-link:hover { color: #374151 !important; }
    </style>
</head>
<body>
    <div style="width:100%;max-width:440px;position:relative;z-index:1">

        {{-- Logo / brand header --}}
        <div style="text-align:center;margin-bottom:30px">
            <div style="display:inline-flex;width:58px;height:58px;border-radius:16px;background:linear-gradient(135deg,#1e40af 0%,#2563eb 100%);align-items:center;justify-content:center;margin-bottom:16px;box-shadow:0 4px 14px rgba(37,99,235,0.3)">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <polyline points="9 12 11 14 15 10"/>
                </svg>
            </div>
            <h1 style="font-size:26px;font-weight:800;color:#111827;letter-spacing:-0.5px">
                bank<span style="color:#2563eb">OS</span>
            </h1>
            <p style="font-size:13px;color:#9ca3af;margin-top:4px;font-weight:500">New Device Detected</p>
        </div>

        {{-- Card --}}
        <div style="background:white;border:1px solid #e5e7eb;border-radius:18px;padding:36px 32px;box-shadow:0 4px 24px rgba(0,0,0,0.06)">

            {{-- Icon badge --}}
            <div style="display:flex;justify-content:center;margin-bottom:20px">
                <div style="width:56px;height:56px;border-radius:50%;background:#fffbeb;display:flex;align-items:center;justify-content:center;border:1px solid #fde68a">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="1.8">
                        <rect x="2" y="7" width="20" height="15" rx="2"/>
                        <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                        <line x1="12" y1="12" x2="12" y2="16"/>
                        <circle cx="12" cy="17" r="0.5" fill="#d97706"/>
                    </svg>
                </div>
            </div>

            <h2 style="font-size:18px;font-weight:700;color:#111827;margin-bottom:8px;text-align:center">Verify Your Device</h2>
            <p style="font-size:13px;color:#6b7280;margin-bottom:28px;line-height:1.6;text-align:center">
                We sent a 6-digit code to your email address. Enter it below to verify this device and complete sign-in.
            </p>

            {{-- Resent success notice --}}
            @if(session('resent'))
            <div style="margin-bottom:18px;padding:12px 14px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:9px;font-size:13px;color:#15803d;display:flex;align-items:flex-start;gap:8px">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="2.5" style="flex-shrink:0;margin-top:1px"><polyline points="20 6 9 17 4 12"/></svg>
                A new verification code has been sent to your email.
            </div>
            @endif

            {{-- Error notice --}}
            @if($errors->any())
            <div style="margin-bottom:18px;padding:12px 14px;background:#fef2f2;border:1px solid #fecaca;border-radius:9px;font-size:13px;color:#dc2626;display:flex;align-items:flex-start;gap:8px">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ $errors->first() }}
            </div>
            @endif

            {{-- OTP form --}}
            <form method="POST" action="{{ route('login.device-verify.submit') }}" id="deviceVerifyForm">
                @csrf
                <input type="hidden" name="code" id="hiddenCode">

                {{-- OTP digit boxes --}}
                <div class="otp-container">
                    <input type="text" class="otp-box" maxlength="1" inputmode="numeric" pattern="[0-9]" data-index="0" autocomplete="off">
                    <input type="text" class="otp-box" maxlength="1" inputmode="numeric" pattern="[0-9]" data-index="1" autocomplete="off">
                    <input type="text" class="otp-box" maxlength="1" inputmode="numeric" pattern="[0-9]" data-index="2" autocomplete="off">
                    <input type="text" class="otp-box" maxlength="1" inputmode="numeric" pattern="[0-9]" data-index="3" autocomplete="off">
                    <input type="text" class="otp-box" maxlength="1" inputmode="numeric" pattern="[0-9]" data-index="4" autocomplete="off">
                    <input type="text" class="otp-box" maxlength="1" inputmode="numeric" pattern="[0-9]" data-index="5" autocomplete="off">
                </div>

                <button type="submit" class="btn-primary"
                        style="width:100%;background:#2563eb;color:white;font-size:13px;font-weight:700;padding:13px 20px;border-radius:10px;border:none;cursor:pointer;letter-spacing:.02em;transition:background .15s;box-shadow:0 2px 8px rgba(37,99,235,0.25)">
                    Verify Device &amp; Sign In
                </button>
            </form>

            {{-- Divider --}}
            <div style="display:flex;align-items:center;gap:12px;margin:20px 0">
                <div style="flex:1;height:1px;background:#f3f4f6"></div>
                <span style="font-size:11px;color:#d1d5db;font-weight:500">OR</span>
                <div style="flex:1;height:1px;background:#f3f4f6"></div>
            </div>

            {{-- Resend code --}}
            <form method="POST" action="{{ route('login.device-verify.resend') }}">
                @csrf
                <button type="submit" class="btn-resend"
                        style="width:100%;background:white;color:#374151;font-size:13px;font-weight:600;padding:11px 20px;border-radius:10px;border:1px solid #e5e7eb;cursor:pointer;transition:background .15s">
                    Resend Code to Email
                </button>
            </form>

            <div style="margin-top:18px;text-align:center">
                <a href="{{ route('login') }}" class="back-link" style="font-size:13px;color:#9ca3af;text-decoration:none;transition:color .15s">
                    &larr; Back to sign in
                </a>
            </div>
        </div>

        {{-- Security notice --}}
        <div style="display:flex;align-items:center;justify-content:center;gap:6px;margin-top:20px;padding:12px 16px;background:white;border:1px solid #f3f4f6;border-radius:10px;box-shadow:0 1px 3px rgba(0,0,0,0.04)">
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

    <script>
    (function () {
        const boxes = document.querySelectorAll('.otp-box');
        const hiddenCode = document.getElementById('hiddenCode');
        const form = document.getElementById('deviceVerifyForm');

        function getCode() {
            return Array.from(boxes).map(b => b.value).join('');
        }

        boxes.forEach((box, i) => {
            box.addEventListener('input', function () {
                this.value = this.value.replace(/\D/g, '').slice(-1);
                this.classList.toggle('filled', this.value !== '');
                hiddenCode.value = getCode();
                if (this.value && i < boxes.length - 1) {
                    boxes[i + 1].focus();
                }
                if (getCode().length === 6) {
                    setTimeout(() => form.submit(), 120);
                }
            });

            box.addEventListener('keydown', function (e) {
                if (e.key === 'Backspace' && !this.value && i > 0) {
                    boxes[i - 1].focus();
                    boxes[i - 1].value = '';
                    boxes[i - 1].classList.remove('filled');
                    hiddenCode.value = getCode();
                }
            });

            box.addEventListener('paste', function (e) {
                e.preventDefault();
                const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
                pasted.split('').forEach((ch, idx) => {
                    if (boxes[idx]) {
                        boxes[idx].value = ch;
                        boxes[idx].classList.add('filled');
                    }
                });
                hiddenCode.value = getCode();
                const next = Math.min(pasted.length, boxes.length - 1);
                boxes[next].focus();
                if (pasted.length === 6) {
                    setTimeout(() => form.submit(), 120);
                }
            });
        });

        if (boxes[0]) boxes[0].focus();
    })();
    </script>
</body>
</html>
