@extends('layouts.portal')
@section('title', 'Notification Preferences')

@section('content')

{{-- Header --}}
<div style="display:flex;align-items:center;gap:12px;margin-bottom:28px">
    <a href="{{ route('security') }}" style="width:34px;height:34px;border-radius:9px;border:1px solid #e5e7eb;background:white;display:flex;align-items:center;justify-content:center;color:#6b7280;flex-shrink:0;text-decoration:none">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#111827">Notification Preferences</h1>
        <p style="font-size:12px;color:#9ca3af;margin-top:2px">Control which alerts and updates you receive</p>
    </div>
</div>

@if(session('success'))
<div style="margin-bottom:16px;padding:12px 16px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;display:flex;align-items:center;gap:10px">
    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
    <p style="font-size:13px;color:#15803d">{{ session('success') }}</p>
</div>
@endif

<form method="POST" action="{{ route('notifications.preferences.update') }}" style="max-width:580px">
    @csrf

    {{-- Transaction alerts --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);margin-bottom:14px;overflow:hidden">
        <div style="padding:14px 20px;border-bottom:1px solid #f3f4f6;background:#f8fafc">
            <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em">Transaction Alerts</p>
        </div>
        <div style="padding:0 20px">

            @php
            $rows = [
                ['debit_alert',     $prefs->debit_alert,      'Debit Alert',        'Notified when money leaves your account',
                 '<path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>'],
                ['credit_alert',    $prefs->credit_alert,     'Credit Alert',       'Notified when money is received',
                 '<path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>'],
                ['large_txn_alert', $prefs->large_txn_alert,  'Large Transaction',  'Notified for transactions above threshold',
                 '<path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>'],
                ['low_balance_alert',$prefs->low_balance_alert,'Low Balance Alert',  'Notified when balance drops below threshold',
                 '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>'],
            ];
            @endphp

            @foreach($rows as [$name, $val, $label, $desc, $svgPath])
            <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 0;border-bottom:1px solid #f9fafb">
                <div style="display:flex;align-items:center;gap:14px;flex:1;min-width:0">
                    <div style="width:38px;height:38px;border-radius:10px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $svgPath !!}</svg>
                    </div>
                    <div>
                        <p style="font-size:13px;font-weight:700;color:#111827">{{ $label }}</p>
                        <p style="font-size:11px;color:#9ca3af;margin-top:2px">{{ $desc }}</p>
                    </div>
                </div>
                <label style="position:relative;display:inline-block;width:44px;height:24px;flex-shrink:0;margin-left:20px;cursor:pointer">
                    <input type="checkbox" name="{{ $name }}" value="1" {{ $val ? 'checked' : '' }}
                           onchange="this.nextElementSibling.style.background=this.checked?'#2563eb':'#d1d5db'; this.nextElementSibling.querySelector('span').style.transform=this.checked?'translateX(20px)':'translateX(0)'"
                           style="opacity:0;width:0;height:0;position:absolute">
                    <span style="position:absolute;top:0;left:0;right:0;bottom:0;background:{{ $val ? '#2563eb' : '#d1d5db' }};border-radius:24px;transition:background .2s">
                        <span style="position:absolute;width:18px;height:18px;top:3px;left:3px;background:white;border-radius:50%;transition:transform .2s;transform:{{ $val ? 'translateX(20px)' : 'translateX(0)' }};box-shadow:0 1px 3px rgba(0,0,0,0.15)"></span>
                    </span>
                </label>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Account & security alerts --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);margin-bottom:14px;overflow:hidden">
        <div style="padding:14px 20px;border-bottom:1px solid #f3f4f6;background:#f8fafc">
            <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em">Account &amp; Security</p>
        </div>
        <div style="padding:0 20px">

            @php
            $rows2 = [
                ['login_alert',     $prefs->login_alert,     'Login Alert',             'Notified every time your account is accessed',
                 '<rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>'],
                ['loan_reminder',   $prefs->loan_reminder,   'Loan Repayment Reminder', 'Alert 3 days before your loan due date',
                 '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>'],
                ['statement_ready', $prefs->statement_ready, 'Statement Ready',         'Notified when your official statement is ready',
                 '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>'],
                ['monthly_summary', $prefs->monthly_summary, 'Monthly Summary',         'Receive your monthly financial recap',
                 '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>'],
                ['weekly_statements', $prefs->weekly_statements ?? true, 'Weekly Statement', 'Automated 7-day account activity summary every Monday',
                 '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>'],
            ];
            @endphp

            @foreach($rows2 as [$name, $val, $label, $desc, $svgPath])
            <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 0;border-bottom:1px solid #f9fafb">
                <div style="display:flex;align-items:center;gap:14px;flex:1;min-width:0">
                    <div style="width:38px;height:38px;border-radius:10px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $svgPath !!}</svg>
                    </div>
                    <div>
                        <p style="font-size:13px;font-weight:700;color:#111827">{{ $label }}</p>
                        <p style="font-size:11px;color:#9ca3af;margin-top:2px">{{ $desc }}</p>
                    </div>
                </div>
                <label style="position:relative;display:inline-block;width:44px;height:24px;flex-shrink:0;margin-left:20px;cursor:pointer">
                    <input type="checkbox" name="{{ $name }}" value="1" {{ $val ? 'checked' : '' }}
                           onchange="this.nextElementSibling.style.background=this.checked?'#2563eb':'#d1d5db'; this.nextElementSibling.querySelector('span').style.transform=this.checked?'translateX(20px)':'translateX(0)'"
                           style="opacity:0;width:0;height:0;position:absolute">
                    <span style="position:absolute;top:0;left:0;right:0;bottom:0;background:{{ $val ? '#2563eb' : '#d1d5db' }};border-radius:24px;transition:background .2s">
                        <span style="position:absolute;width:18px;height:18px;top:3px;left:3px;background:white;border-radius:50%;transition:transform .2s;transform:{{ $val ? 'translateX(20px)' : 'translateX(0)' }};box-shadow:0 1px 3px rgba(0,0,0,0.15)"></span>
                    </span>
                </label>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Alert thresholds --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:20px;margin-bottom:20px">
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:16px">Alert Thresholds</p>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Low Balance Threshold (NGN)</label>
                <div style="position:relative">
                    <span style="position:absolute;left:11px;top:50%;transform:translateY(-50%);font-size:12px;font-weight:700;color:#9ca3af">NGN</span>
                    <input type="number" name="low_balance_threshold" value="{{ $prefs->low_balance_threshold ?? 1000 }}" min="0" step="100"
                           style="width:100%;padding:10px 12px 10px 42px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none"
                           onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#d1d5db'">
                </div>
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Large Transaction Threshold (NGN)</label>
                <div style="position:relative">
                    <span style="position:absolute;left:11px;top:50%;transform:translateY(-50%);font-size:12px;font-weight:700;color:#9ca3af">NGN</span>
                    <input type="number" name="large_txn_threshold" value="{{ $prefs->large_txn_threshold ?? 50000 }}" min="0" step="1000"
                           style="width:100%;padding:10px 12px 10px 42px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none"
                           onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#d1d5db'">
                </div>
            </div>
        </div>
    </div>

    <button type="submit"
            style="width:100%;background:#2563eb;color:white;font-size:13px;font-weight:700;padding:12px 20px;border-radius:10px;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px"
            onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        Save Preferences
    </button>
</form>
@endsection
