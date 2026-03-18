<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — bankOS Portal</title>
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
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <div>
                <h1 style="font-size:18px;font-weight:800;color:#111827">Set New Password</h1>
                <p style="font-size:12px;color:#9ca3af;margin-top:1px">Choose a strong password for your account</p>
            </div>
        </div>

        <div style="height:1px;background:#f3f4f6;margin:18px 0"></div>

        {{-- Errors --}}
        @if($errors->any())
        <div style="margin-bottom:18px;padding:12px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px">
            @foreach($errors->all() as $error)
            <p style="font-size:13px;color:#991b1b;margin-bottom:3px">{{ $error }}</p>
            @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('password.reset') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            {{-- New password --}}
            <div style="margin-bottom:16px">
                <label for="password" style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">New Password</label>
                <div style="position:relative">
                    <div style="position:absolute;left:11px;top:50%;transform:translateY(-50%);pointer-events:none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </div>
                    <input type="password" id="password" name="password" required autofocus minlength="8"
                           placeholder="Minimum 8 characters"
                           style="width:100%;padding:10px 40px 10px 36px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none"
                           onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#d1d5db'">
                    <button type="button" onclick="toggleVis('password','eye1')" style="position:absolute;right:11px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;padding:2px">
                        <svg id="eye1" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                {{-- Password strength bar --}}
                <div id="strength-bar" style="height:3px;border-radius:2px;background:#e5e7eb;margin-top:6px;overflow:hidden">
                    <div id="strength-fill" style="height:100%;width:0;border-radius:2px;transition:width .3s,background .3s"></div>
                </div>
                <p id="strength-label" style="font-size:10px;color:#9ca3af;margin-top:3px"></p>
            </div>

            {{-- Confirm password --}}
            <div style="margin-bottom:22px">
                <label for="password_confirmation" style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Confirm New Password</label>
                <div style="position:relative">
                    <div style="position:absolute;left:11px;top:50%;transform:translateY(-50%);pointer-events:none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </div>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           placeholder="Re-enter your password"
                           style="width:100%;padding:10px 40px 10px 36px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none"
                           onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#d1d5db'">
                    <button type="button" onclick="toggleVis('password_confirmation','eye2')" style="position:absolute;right:11px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;padding:2px">
                        <svg id="eye2" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
            </div>

            <button type="submit"
                    style="width:100%;background:#2563eb;color:white;font-size:13px;font-weight:700;padding:11px 20px;border-radius:10px;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px"
                    onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Reset Password
            </button>
        </form>
    </div>

    <p style="text-align:center;font-size:11px;color:#d1d5db;margin-top:20px">
        &copy; {{ date('Y') }} bankOS &mdash; Secured Banking Portal
    </p>
</div>

<script>
function toggleVis(fieldId, eyeId) {
    var f = document.getElementById(fieldId);
    f.type = f.type === 'password' ? 'text' : 'password';
}
var pwField = document.getElementById('password');
var fillBar  = document.getElementById('strength-fill');
var label    = document.getElementById('strength-label');
pwField.addEventListener('input', function() {
    var v = this.value;
    var score = 0;
    if (v.length >= 8)  score++;
    if (/[A-Z]/.test(v)) score++;
    if (/[0-9]/.test(v)) score++;
    if (/[^A-Za-z0-9]/.test(v)) score++;
    var pct   = ['0%','25%','50%','75%','100%'][score];
    var color = ['#e5e7eb','#dc2626','#d97706','#eab308','#16a34a'][score];
    var text  = ['','Weak','Fair','Good','Strong'][score];
    fillBar.style.width = pct;
    fillBar.style.background = color;
    label.textContent = text;
    label.style.color = color;
});
</script>
</body>
</html>
