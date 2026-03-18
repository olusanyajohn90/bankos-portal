@props(['title' => 'Open an Account', 'currentStep' => 1])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Open an Account' }} — {{ config('app.name', 'bankOS') }}</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Inter', sans-serif;
            min-height: 100vh;
            background: #f9fafb;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 40px 16px 60px;
        }
        body::before {
            content: '';
            position: fixed;
            top: -100px;
            left: 50%;
            transform: translateX(-50%);
            width: 700px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(ellipse at center, rgba(37,99,235,0.06) 0%, transparent 70%);
            pointer-events: none;
        }
        .ob-wrap {
            width: 100%;
            max-width: 480px;
            position: relative;
            z-index: 1;
        }
        /* Brand header */
        .ob-brand {
            text-align: center;
            margin-bottom: 28px;
        }
        .ob-brand-icon {
            display: inline-flex;
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            box-shadow: 0 4px 14px rgba(37,99,235,0.28);
        }
        .ob-brand h1 {
            font-size: 24px;
            font-weight: 800;
            color: #111827;
            letter-spacing: -0.4px;
        }
        .ob-brand h1 span { color: #2563eb; }
        /* Progress stepper */
        .ob-stepper {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 28px;
            gap: 0;
        }
        .ob-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }
        .ob-step-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            flex-shrink: 0;
            transition: background .2s, border-color .2s;
        }
        .ob-step-circle.done {
            background: #2563eb;
            color: white;
            border: 2px solid #2563eb;
        }
        .ob-step-circle.active {
            background: #2563eb;
            color: white;
            border: 2px solid #2563eb;
            box-shadow: 0 0 0 4px rgba(37,99,235,0.15);
        }
        .ob-step-circle.upcoming {
            background: white;
            color: #9ca3af;
            border: 2px solid #e5e7eb;
        }
        .ob-step-label {
            font-size: 10px;
            font-weight: 600;
            margin-top: 5px;
            color: #9ca3af;
            white-space: nowrap;
        }
        .ob-step-label.active { color: #2563eb; }
        .ob-step-label.done   { color: #6b7280; }
        .ob-step-connector {
            height: 2px;
            width: 32px;
            background: #e5e7eb;
            flex-shrink: 0;
            margin-bottom: 18px;
        }
        .ob-step-connector.done { background: #2563eb; }
        /* Card */
        .ob-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 36px 32px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }
        .ob-card h2 {
            font-size: 18px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 6px;
        }
        .ob-card .ob-subtitle {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 26px;
            line-height: 1.5;
        }
        /* Form elements */
        .ob-field { margin-bottom: 18px; }
        .ob-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #374151;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        .ob-input {
            width: 100%;
            padding: 11px 13px;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            font-size: 13px;
            color: #111827;
            background: white;
            transition: border-color .15s, box-shadow .15s;
        }
        .ob-input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
        }
        .ob-input.has-error { border-color: #ef4444; }
        .ob-error {
            font-size: 12px;
            color: #dc2626;
            margin-top: 5px;
        }
        /* Buttons */
        .ob-btn {
            width: 100%;
            padding: 13px 20px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: .02em;
            transition: background .15s;
        }
        .ob-btn-primary {
            background: #2563eb;
            color: white;
            box-shadow: 0 2px 8px rgba(37,99,235,0.22);
        }
        .ob-btn-primary:hover { background: #1d4ed8; }
        .ob-btn-primary:disabled {
            background: #93c5fd;
            cursor: not-allowed;
            box-shadow: none;
        }
        .ob-btn-success {
            background: #16a34a;
            color: white;
            box-shadow: 0 2px 8px rgba(22,163,74,0.22);
        }
        .ob-btn-success:hover { background: #15803d; }
        /* Links */
        .ob-link {
            color: #2563eb;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
        }
        .ob-link:hover { color: #1d4ed8; }
        .ob-link-muted {
            color: #6b7280;
            text-decoration: none;
            font-size: 13px;
        }
        .ob-link-muted:hover { color: #374151; }
        /* Alert */
        .ob-alert-error {
            margin-bottom: 18px;
            padding: 12px 14px;
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 9px;
            font-size: 13px;
            color: #dc2626;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }
        .ob-alert-info {
            padding: 12px 14px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 9px;
            font-size: 12px;
            color: #1e40af;
            display: flex;
            align-items: flex-start;
            gap: 8px;
            line-height: 1.5;
        }
        /* Footer */
        .ob-footer {
            text-align: center;
            margin-top: 20px;
        }
        .ob-footer p {
            font-size: 11px;
            color: #d1d5db;
        }
    </style>
</head>
<body>
<div class="ob-wrap">

    {{-- Brand --}}
    <div class="ob-brand">
        <div>
            <div class="ob-brand-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
            </div>
        </div>
        <h1>bank<span>OS</span></h1>
    </div>

    {{-- Progress stepper --}}
    @php
        $steps  = ['Info', 'Password', 'Account', 'BVN', 'ID', 'Review'];
        $active = $currentStep ?? 1;
    @endphp
    <div class="ob-stepper">
        @foreach($steps as $i => $label)
            @php $num = $i + 1; @endphp
            @if($i > 0)
                <div class="ob-step-connector {{ $num <= $active ? 'done' : '' }}"></div>
            @endif
            <div class="ob-step">
                <div class="ob-step-circle {{ $num < $active ? 'done' : ($num === $active ? 'active' : 'upcoming') }}">
                    @if($num < $active)
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                    @else
                        {{ $num }}
                    @endif
                </div>
                <span class="ob-step-label {{ $num < $active ? 'done' : ($num === $active ? 'active' : '') }}">{{ $label }}</span>
            </div>
        @endforeach
    </div>

    {{-- Page content --}}
    {{ $slot }}

    {{-- Footer --}}
    <div class="ob-footer">
        <p>&copy; {{ date('Y') }} {{ config('app.name', 'bankOS') }}. All rights reserved.</p>
    </div>

</div>
</body>
</html>
