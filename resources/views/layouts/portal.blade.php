<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name', 'BankOS Portal'))</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2563eb">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .portal-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 9999;
            background: #fff;
            border-top: 1px solid #e5e7eb;
        }
        .portal-bottom-nav-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
        }
        .portal-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 240px;
            z-index: 40;
            background: #fff;
            border-right: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
        }
        .portal-page-main {
            padding: 20px 16px 96px;
        }
        .portal-more-drawer {
            position: fixed;
            inset: 0;
            z-index: 9998;
            display: none;
        }
        .portal-more-drawer.open {
            display: block;
        }
        .portal-more-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.4);
        }
        .portal-more-sheet {
            position: absolute;
            bottom: 64px;
            left: 0;
            right: 0;
            background: white;
            border-radius: 20px 20px 0 0;
            padding: 20px 20px 24px;
            max-height: 80vh;
            overflow-y: auto;
        }
        @media (max-width: 1023px) {
            .portal-sidebar { display: none; }
        }
        @media (min-width: 1024px) {
            .portal-bottom-nav { display: none; }
            .portal-main { margin-left: 240px; }
            .portal-mobile-header { display: none; }
            .portal-page-main { padding: 32px 32px 32px; }
        }
        /* Dark mode */
        body.dark-mode {
            filter: invert(1) hue-rotate(180deg);
        }
        body.dark-mode img,
        body.dark-mode video,
        body.dark-mode canvas {
            filter: invert(1) hue-rotate(180deg);
        }
        /* Chatbot widget */
        #chatbot-bubble {
            position: fixed;
            bottom: 80px;
            right: 16px;
            z-index: 9999;
        }
        @media (min-width: 1024px) {
            #chatbot-bubble { bottom: 24px; right: 24px; }
        }
    </style>
</head>
<body class="h-full bg-gray-50" id="portal-body">
<script>
    if(localStorage.getItem('darkMode')==='1') document.getElementById('portal-body').classList.add('dark-mode');
</script>

{{-- ── Load tenant ── --}}
@php
    $tenant    = auth('customer')->check() ? auth('customer')->user()->tenant : null;
    $bankName  = $tenant?->name ?? config('app.name', 'bankOS Portal');
    $isActive  = fn($r) => request()->routeIs($r);
    $navColor  = fn($r) => request()->routeIs($r) ? '#2563eb' : '#9ca3af';
    $unreadCount = auth('customer')->check()
        ? \App\Models\PortalNotification::unreadCount(auth('customer')->id())
        : 0;
@endphp

{{-- ── Mobile bottom nav (5-item) ── --}}
<nav class="portal-bottom-nav">
    <div class="portal-bottom-nav-grid">
        <a href="{{ route('dashboard') }}" style="display:flex;flex-direction:column;align-items:center;gap:2px;padding:10px 4px 8px;font-size:10px;font-weight:500;color:{{ $navColor('dashboard') }};text-decoration:none">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            Home
        </a>
        <a href="{{ route('transfer') }}" style="display:flex;flex-direction:column;align-items:center;gap:2px;padding:10px 4px 8px;font-size:10px;font-weight:500;color:{{ $navColor('transfer*') }};text-decoration:none">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 16V4m0 0L3 8m4-4 4 4"/><path d="M17 8v12m0 0 4-4m-4 4-4-4"/></svg>
            Transfer
        </a>
        <a href="{{ route('bills') }}" style="display:flex;flex-direction:column;align-items:center;gap:2px;padding:10px 4px 8px;font-size:10px;font-weight:500;color:{{ $navColor('bills*') }};text-decoration:none">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 12h8M8 8h8M8 16h5"/></svg>
            Bills
        </a>
        <a href="{{ route('savings') }}" style="display:flex;flex-direction:column;align-items:center;gap:2px;padding:10px 4px 8px;font-size:10px;font-weight:500;color:{{ $navColor('savings*') }};text-decoration:none">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2"/><path d="M12 6v6l4 2"/></svg>
            Save
        </a>
        <button onclick="document.getElementById('more-drawer').classList.toggle('open')" style="display:flex;flex-direction:column;align-items:center;gap:2px;padding:10px 4px 8px;font-size:10px;font-weight:500;color:#9ca3af;background:none;border:none;cursor:pointer;width:100%;position:relative">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="5" cy="12" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="19" cy="12" r="1.5"/></svg>
            More
            @if($unreadCount > 0)
            <span style="position:absolute;top:6px;right:18px;min-width:16px;height:16px;background:#dc2626;border-radius:8px;font-size:9px;font-weight:700;color:white;display:grid;place-items:center;padding:0 3px">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
            @endif
        </button>
    </div>
</nav>

{{-- ── Mobile More Drawer ── --}}
<div id="more-drawer" class="portal-more-drawer">
    <div class="portal-more-backdrop" onclick="document.getElementById('more-drawer').classList.remove('open')"></div>
    <div class="portal-more-sheet">
        <div style="width:36px;height:4px;background:#e5e7eb;border-radius:4px;margin:0 auto 20px"></div>

        {{-- Primary services --}}
        <p style="font-size:11px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px">Banking</p>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:20px">
            @php
            $bankingItems = [
                ['route' => 'loans',                'label' => 'Loans',       'color' => '#2563eb', 'bg' => '#eff6ff',  'icon' => '<rect x="2" y="3" width="20" height="18" rx="2"/><line x1="8" y1="10" x2="16" y2="10"/><line x1="8" y1="14" x2="14" y2="14"/>'],
                ['route' => 'investments',          'label' => 'Invest',      'color' => '#059669', 'bg' => '#ecfdf5',  'icon' => '<polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>'],
                ['route' => 'savings',              'label' => 'Savings',     'color' => '#0891b2', 'bg' => '#ecfeff',  'icon' => '<path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2"/><path d="M12 6v6l4 2"/>'],
                ['route' => 'savings-challenges',   'label' => 'Challenges',  'color' => '#f59e0b', 'bg' => '#fffbeb',  'icon' => '<circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/>'],
                ['route' => 'airtime',              'label' => 'Airtime',     'color' => '#0891b2', 'bg' => '#ecfeff',  'icon' => '<rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/>'],
                ['route' => 'scheduled-transfers',  'label' => 'Schedule',    'color' => '#7c3aed', 'bg' => '#f5f3ff',  'icon' => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>'],
                ['route' => 'cheque-requests',      'label' => 'Chequebook',  'color' => '#b45309', 'bg' => '#fffbeb',  'icon' => '<rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/>'],
                ['route' => 'account-opening',      'label' => 'New A/C',     'color' => '#059669', 'bg' => '#ecfdf5',  'icon' => '<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>'],
            ];
            @endphp
            @foreach($bankingItems as $item)
            <a href="{{ route($item['route']) }}" onclick="document.getElementById('more-drawer').classList.remove('open')"
               style="display:flex;flex-direction:column;align-items:center;gap:7px;padding:14px 8px;background:{{ $item['bg'] }};border-radius:12px;text-decoration:none">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="{{ $item['color'] }}" stroke-width="2">{!! $item['icon'] !!}</svg>
                <span style="font-size:10px;font-weight:600;color:#374151;text-align:center">{{ $item['label'] }}</span>
            </a>
            @endforeach
        </div>

        {{-- Tools & Account --}}
        <p style="font-size:11px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px">Tools & Account</p>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:20px">
            @php
            $toolItems = [
                ['route' => 'budget',          'label' => 'Budget',      'color' => '#ca8a04', 'bg' => '#fefce8',  'icon' => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>'],
                ['route' => 'credit-score',    'label' => 'Credit',      'color' => '#2563eb', 'bg' => '#eff6ff',  'icon' => '<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>'],
                ['route' => 'rates',           'label' => 'FX Rates',    'color' => '#059669', 'bg' => '#ecfdf5',  'icon' => '<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>'],
                ['route' => 'pay-requests',    'label' => 'Pay Link',    'color' => '#7c3aed', 'bg' => '#f5f3ff',  'icon' => '<path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>'],
                ['route' => 'referral',        'label' => 'Refer',       'color' => '#be185d', 'bg' => '#fdf2f8',  'icon' => '<path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><polyline points="16 6 12 2 8 6"/><line x1="12" y1="2" x2="12" y2="15"/>'],
                ['route' => 'disputes',        'label' => 'Disputes',    'color' => '#dc2626', 'bg' => '#fef2f2',  'icon' => '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>'],
                ['route' => 'kyc.upgrade',     'label' => 'KYC',         'color' => '#0891b2', 'bg' => '#ecfeff',  'icon' => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>'],
                ['route' => 'documents',       'label' => 'Documents',   'color' => '#b45309', 'bg' => '#fffbeb',  'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>'],
            ];
            @endphp
            @foreach($toolItems as $item)
            <a href="{{ route($item['route']) }}" onclick="document.getElementById('more-drawer').classList.remove('open')"
               style="display:flex;flex-direction:column;align-items:center;gap:7px;padding:14px 8px;background:{{ $item['bg'] }};border-radius:12px;text-decoration:none">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="{{ $item['color'] }}" stroke-width="2">{!! $item['icon'] !!}</svg>
                <span style="font-size:10px;font-weight:600;color:#374151;text-align:center">{{ $item['label'] }}</span>
            </a>
            @endforeach
        </div>

        {{-- Account links --}}
        <p style="font-size:11px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px">My Account</p>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px">
            @php
            $accountItems = [
                ['route' => 'notifications', 'label' => 'Inbox',    'color' => '#2563eb', 'bg' => '#eff6ff',  'badge' => $unreadCount, 'icon' => '<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>'],
                ['route' => 'security',            'label' => 'Security', 'color' => '#dc2626', 'bg' => '#fef2f2',  'badge' => 0, 'icon' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>'],
                ['route' => 'profile',             'label' => 'Profile',  'color' => '#374151', 'bg' => '#f9fafb',  'badge' => 0, 'icon' => '<circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>'],
                ['route' => 'loans.apply',         'label' => 'Apply',    'color' => '#059669', 'bg' => '#ecfdf5',  'badge' => 0, 'icon' => '<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>'],
            ];
            @endphp
            @foreach($accountItems as $item)
            <a href="{{ route($item['route']) }}" onclick="document.getElementById('more-drawer').classList.remove('open')"
               style="display:flex;flex-direction:column;align-items:center;gap:7px;padding:14px 8px;background:{{ $item['bg'] }};border-radius:12px;text-decoration:none;position:relative">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="{{ $item['color'] }}" stroke-width="2">{!! $item['icon'] !!}</svg>
                @if($item['badge'] > 0)
                <span style="position:absolute;top:6px;right:10px;min-width:16px;height:16px;background:#dc2626;border-radius:8px;font-size:9px;font-weight:700;color:white;display:grid;place-items:center;padding:0 3px">{{ $item['badge'] > 9 ? '9+' : $item['badge'] }}</span>
                @endif
                <span style="font-size:10px;font-weight:600;color:#374151;text-align:center">{{ $item['label'] }}</span>
            </a>
            @endforeach
        </div>
    </div>
</div>

{{-- ── Desktop sidebar ── --}}
@php
$sideNavGroups = [
    'Banking' => [
        ['route'=>'dashboard',           'label'=>'Dashboard',       'badge'=>0,           'icon'=>'<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>'],
        ['route'=>'transfer*',           'label'=>'Transfer',        'badge'=>0,           'icon'=>'<path d="M7 16V4m0 0L3 8m4-4 4 4"/><path d="M17 8v12m0 0 4-4m-4 4-4-4"/>'],
        ['route'=>'bills*',              'label'=>'Bill Payments',   'badge'=>0,           'icon'=>'<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 12h8M8 8h8M8 16h5"/>'],
        ['route'=>'airtime*',            'label'=>'Airtime & Data',  'badge'=>0,           'icon'=>'<rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/>'],
        ['route'=>'pay-requests*',       'label'=>'Pay Requests',    'badge'=>0,           'icon'=>'<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>'],
        ['route'=>'beneficiaries*',      'label'=>'Beneficiaries',   'badge'=>0,           'icon'=>'<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>'],
        ['route'=>'cards*',              'label'=>'Virtual Cards',   'badge'=>0,           'icon'=>'<rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>'],
        ['route'=>'physical-cards*',     'label'=>'Debit Cards',     'badge'=>0,           'icon'=>'<rect x="2" y="5" width="20" height="14" rx="2"/><circle cx="7" cy="12" r="2"/>'],
        ['route'=>'qr-payment*',         'label'=>'QR Payment',      'badge'=>0,           'icon'=>'<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><line x1="14" y1="14" x2="14" y2="21"/><line x1="14" y1="14" x2="21" y2="14"/>'],
        ['route'=>'cheque-requests*',    'label'=>'Chequebook',      'badge'=>0,           'icon'=>'<rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/>'],
        ['route'=>'standing-orders*',    'label'=>'Standing Orders', 'badge'=>0,           'icon'=>'<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>'],
        ['route'=>'scheduled-transfers*','label'=>'Scheduled Txfr',  'badge'=>0,           'icon'=>'<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 15 14"/>'],
    ],
    'Save & Invest' => [
        ['route'=>'savings*',            'label'=>'Savings Pockets', 'badge'=>0,           'icon'=>'<path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2"/><path d="M12 6v6l4 2"/>'],
        ['route'=>'savings-challenges*', 'label'=>'Challenges',      'badge'=>0,           'icon'=>'<circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/>'],
        ['route'=>'investments*',        'label'=>'Investments',     'badge'=>0,           'icon'=>'<polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>'],
        ['route'=>'budget*',             'label'=>'Budget',          'badge'=>0,           'icon'=>'<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>'],
        ['route'=>'savings-groups*',     'label'=>'Group Savings',   'badge'=>0,           'icon'=>'<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>'],
        ['route'=>'split-bills*',        'label'=>'Split Bills',     'badge'=>0,           'icon'=>'<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>'],
    ],
    'Borrow' => [
        ['route'=>'loans*',              'label'=>'Loans',           'badge'=>0,           'icon'=>'<rect x="2" y="3" width="20" height="18" rx="2"/><line x1="8" y1="10" x2="16" y2="10"/><line x1="8" y1="14" x2="14" y2="14"/>'],
        ['route'=>'overdraft*',          'label'=>'Overdraft',       'badge'=>0,           'icon'=>'<polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/><polyline points="17 18 23 18 23 12"/>'],
        ['route'=>'interbank-transfer*', 'label'=>'Send to Bank',    'badge'=>0,           'icon'=>'<line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>'],
        ['route'=>'bulk-transfer*',      'label'=>'Bulk/Payroll',    'badge'=>0,           'icon'=>'<line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>'],
    ],
    'Tools' => [
        ['route'=>'analytics*',          'label'=>'Analytics',       'badge'=>0,           'icon'=>'<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>'],
        ['route'=>'credit-score*',       'label'=>'Credit Score',    'badge'=>0,           'icon'=>'<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>'],
        ['route'=>'rates*',              'label'=>'FX Rates',        'badge'=>0,           'icon'=>'<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>'],
        ['route'=>'account-opening*',    'label'=>'Open Account',    'badge'=>0,           'icon'=>'<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>'],
        ['route'=>'documents*',          'label'=>'Documents',       'badge'=>0,           'icon'=>'<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>'],
        ['route'=>'kyc.upgrade*',        'label'=>'KYC Upgrade',     'badge'=>0,           'icon'=>'<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>'],
        ['route'=>'referral*',           'label'=>'Referrals',       'badge'=>0,           'icon'=>'<path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><polyline points="16 6 12 2 8 6"/>'],
        ['route'=>'disputes*',           'label'=>'Disputes',        'badge'=>0,           'icon'=>'<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>'],
    ],
    'My Account' => [
        ['route'=>'notifications',       'label'=>'Notifications',   'badge'=>$unreadCount,'icon'=>'<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>'],
        ['route'=>'security',            'label'=>'Security',        'badge'=>0,           'icon'=>'<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>'],
        ['route'=>'profile',             'label'=>'Profile',         'badge'=>0,           'icon'=>'<circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>'],
    ],
];
@endphp
<aside class="portal-sidebar">
    <div style="display:flex;align-items:center;gap:10px;padding:0 20px;height:60px;border-bottom:1px solid #f3f4f6;flex-shrink:0">
        <div style="width:30px;height:30px;border-radius:8px;background:#2563eb;display:grid;place-items:center;flex-shrink:0">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        </div>
        <span style="font-size:15px;font-weight:700;color:#111827">{{ $tenant?->name ?? 'bankOS' }}</span>
    </div>

    <nav style="flex:1;padding:8px 8px;overflow-y:auto" id="sideNav">
        @foreach($sideNavGroups as $groupLabel => $items)
        @php
            $gid = 'snav-' . preg_replace('/[^a-z0-9]+/', '-', strtolower($groupLabel));
            $hasActive = collect($items)->contains(fn($i) => request()->routeIs($i['route']));
        @endphp
        <div class="nav-group" data-group="{{ $gid }}" data-has-active="{{ $hasActive ? '1' : '0' }}" style="margin-bottom:2px">
            <button onclick="toggleNavGroup('{{ $gid }}')" style="display:flex;align-items:center;width:100%;border:none;background:none;cursor:pointer;padding:8px 10px 4px;gap:4px;border-radius:6px;margin-top:4px">
                <span style="font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.07em;flex:1;text-align:left">{{ $groupLabel }}</span>
                <svg id="{{ $gid }}-chev" xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2.5" style="flex-shrink:0;transition:transform .2s"><polyline points="6 9 12 15 18 9"/></svg>
            </button>
            <div id="{{ $gid }}-items">
                @foreach($items as $item)
                @php $active = request()->routeIs($item['route']); @endphp
                <a href="{{ route(rtrim($item['route'], '*')) }}"
                   style="display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:8px;font-size:13px;font-weight:{{ $active ? '600' : '400' }};color:{{ $active ? '#1d4ed8' : '#4b5563' }};background:{{ $active ? '#eff6ff' : 'transparent' }};text-decoration:none;position:relative;margin-bottom:1px">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0">{!! $item['icon'] !!}</svg>
                    <span style="flex:1">{{ $item['label'] }}</span>
                    @if($item['badge'] > 0)
                    <span style="min-width:17px;height:17px;background:#dc2626;border-radius:9px;font-size:10px;font-weight:700;color:white;display:grid;place-items:center;padding:0 3px;flex-shrink:0">{{ $item['badge'] > 9 ? '9+' : $item['badge'] }}</span>
                    @endif
                </a>
                @endforeach
            </div>
        </div>
        @endforeach
    </nav>

    <div style="padding:12px 14px;border-top:1px solid #f3f4f6;flex-shrink:0">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
            <div style="width:34px;height:34px;border-radius:50%;background:#dbeafe;color:#1d4ed8;display:grid;place-items:center;font-size:12px;font-weight:700;flex-shrink:0">
                {{ strtoupper(substr(auth('customer')->user()->first_name ?? 'C', 0, 1)) }}
            </div>
            <div style="flex:1;min-width:0">
                <p style="font-size:13px;font-weight:600;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ auth('customer')->user()->first_name }} {{ auth('customer')->user()->last_name }}</p>
                <p style="font-size:11px;color:#9ca3af;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ auth('customer')->user()->email }}</p>
            </div>
        </div>
        <div style="display:flex;gap:6px">
            <button onclick="toggleDarkMode()" style="display:flex;align-items:center;gap:6px;font-size:12px;color:#6b7280;background:none;border:1px solid #e5e7eb;cursor:pointer;padding:6px 8px;border-radius:8px;flex:1" title="Toggle dark mode">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                Dark
            </button>
            <form method="POST" action="{{ route('logout') }}" style="flex:1">
                @csrf
                <button type="submit" style="display:flex;align-items:center;gap:6px;font-size:12px;color:#6b7280;background:none;border:1px solid #e5e7eb;cursor:pointer;padding:6px 8px;border-radius:8px;width:100%">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Sign out
                </button>
            </form>
        </div>
    </div>
</aside>

{{-- ── Main content ── --}}
<div class="portal-main" style="min-height:100vh">

    {{-- Mobile top bar --}}
    <header class="portal-mobile-header" style="background:white;border-bottom:1px solid #e5e7eb;padding:0 16px;display:flex;align-items:center;justify-content:space-between;height:52px;position:sticky;top:0;z-index:30">
        <a href="{{ route('dashboard') }}" style="display:flex;align-items:center;gap:8px;text-decoration:none">
            <div style="width:26px;height:26px;border-radius:6px;background:#2563eb;display:grid;place-items:center">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
            </div>
            <span style="font-size:15px;font-weight:700;color:#111827">{{ $tenant?->name ?? 'bankOS' }}</span>
        </a>
        <div style="display:flex;align-items:center;gap:10px">
            <a href="{{ route('notifications') }}" style="position:relative;display:grid;place-items:center;color:#6b7280">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                @if($unreadCount > 0)
                <span style="position:absolute;top:-4px;right:-4px;min-width:14px;height:14px;background:#dc2626;border-radius:7px;font-size:8px;font-weight:700;color:white;display:grid;place-items:center;padding:0 2px">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                @endif
            </a>
            <a href="{{ route('profile') }}" style="width:32px;height:32px;border-radius:50%;background:#dbeafe;color:#1d4ed8;display:grid;place-items:center;font-size:12px;font-weight:700;text-decoration:none">
                {{ strtoupper(substr(auth('customer')->user()->first_name ?? 'C', 0, 1)) }}
            </a>
        </div>
    </header>

    <main class="portal-page-main">

        @if(session('success'))
        <div style="display:flex;align-items:flex-start;gap:10px;padding:12px 14px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;font-size:13px;color:#166534;margin-bottom:18px">
            <svg style="flex-shrink:0;margin-top:1px" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            <span>{{ session('success') }}</span>
        </div>
        @endif
        @if(session('error'))
        <div style="display:flex;align-items:flex-start;gap:10px;padding:12px 14px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;font-size:13px;color:#991b1b;margin-bottom:18px">
            <svg style="flex-shrink:0;margin-top:1px" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        @yield('content')
    </main>
</div>

{{-- ── Chatbot / Help Widget ── --}}
<div id="chatbot-bubble">
    <div id="chatbot-panel" style="display:none;position:absolute;bottom:56px;right:0;width:290px;background:white;border:1px solid #e5e7eb;border-radius:16px;box-shadow:0 8px 30px rgba(0,0,0,0.12);overflow:hidden">
        <div style="background:linear-gradient(135deg,#2563eb,#1d4ed8);padding:14px 16px;display:flex;justify-content:space-between;align-items:center">
            <div>
                <p style="font-size:13px;font-weight:700;color:white">Help Centre</p>
                <p style="font-size:10px;color:rgba(255,255,255,0.75)">Quick answers</p>
            </div>
            <button onclick="closeChatbot()" style="background:rgba(255,255,255,0.15);border:none;color:white;border-radius:6px;padding:4px 8px;cursor:pointer;font-size:12px">✕</button>
        </div>
        <div style="padding:14px;max-height:320px;overflow-y:auto" id="chatbot-messages">
            <p style="font-size:12px;color:#6b7280;margin-bottom:12px">Hi 👋 What can we help you with?</p>
            <div id="chatbot-faqs" style="display:flex;flex-direction:column;gap:8px"></div>
        </div>
        <div id="chatbot-answer" style="display:none;padding:0 14px 14px">
            <div id="chatbot-answer-text" style="background:#f3f4f6;border-radius:10px;padding:12px;font-size:12px;color:#374151;line-height:1.6"></div>
            <button onclick="resetChatbot()" style="margin-top:8px;font-size:12px;color:#2563eb;background:none;border:none;cursor:pointer">← Back to questions</button>
        </div>
        <div style="padding:12px 14px;border-top:1px solid #f3f4f6;text-align:center">
            <a href="{{ route('disputes.create') }}" style="font-size:12px;color:#2563eb;font-weight:600">Raise a support ticket →</a>
        </div>
    </div>
    <button onclick="toggleChatbot()" id="chatbot-btn"
            style="width:48px;height:48px;border-radius:50%;background:#2563eb;border:none;cursor:pointer;display:grid;place-items:center;box-shadow:0 4px 14px rgba(37,99,235,0.4)">
        <svg id="chatbot-icon-open" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        <svg id="chatbot-icon-close" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" style="display:none"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
</div>

<script>
// ── PWA: Register service worker + push subscription ──
if ('serviceWorker' in navigator && 'PushManager' in window) {
    navigator.serviceWorker.register('/sw.js').then(function(reg) {
        // Ask for push permission and subscribe
        if (Notification.permission === 'default') {
            Notification.requestPermission().then(function(perm) {
                if (perm === 'granted') bankosSubscribePush(reg);
            });
        } else if (Notification.permission === 'granted') {
            bankosSubscribePush(reg);
        }
    }).catch(() => {});
} else if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/sw.js').catch(() => {});
}

function bankosSubscribePush(reg) {
    reg.pushManager.getSubscription().then(function(existing) {
        if (existing) { bankosStorePush(existing); return; }
        // VAPID public key — replace with real key from config
        const vapidKey = '{{ config("app.vapid_public_key", "") }}';
        if (!vapidKey) return;
        reg.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: vapidKey,
        }).then(bankosStorePush).catch(() => {});
    });
}

function bankosStorePush(sub) {
    const json = sub.toJSON();
    fetch('{{ route("notifications.push-subscribe") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({ endpoint: json.endpoint, keys: json.keys }),
    }).catch(() => {});
}

// ── Dark mode ──
function toggleDarkMode() {
    const body = document.getElementById('portal-body');
    body.classList.toggle('dark-mode');
    localStorage.setItem('darkMode', body.classList.contains('dark-mode') ? '1' : '0');
}

// ── Chatbot FAQs ──
const faqs = [
    { q: 'How do I transfer money?', a: 'Go to Transfer in the menu. Enter beneficiary account number and amount. We\'ll look up the account name before you confirm.' },
    { q: 'Why is my transaction pending?', a: 'Pending transactions are usually processed within a few minutes. If it\'s been over 24 hours, please raise a dispute from the Disputes menu.' },
    { q: 'How do I freeze my account?', a: 'Go to your Account details, scroll down to Account Settings, and click "Freeze Account". You can unfreeze anytime.' },
    { q: 'What is my transaction limit?', a: 'Your limits depend on your KYC tier. Upgrade your KYC via the KYC Upgrade menu to increase limits.' },
    { q: 'How do I change my PIN?', a: 'Go to Security Centre from the menu. Under "Portal PIN", set your 4-digit PIN. Use it to confirm transactions.' },
    { q: 'How do I download a statement?', a: 'Open any account, click "View Statement", select your date range, and download as PDF or Excel.' },
    { q: 'How do I set up 2FA?', a: 'Go to Security Centre and click "Enable 2FA". Scan the QR code with Google Authenticator or Authy.' },
    { q: 'Can I open a second account?', a: 'Yes! Go to Open New Account in the menu. Choose from Savings, Current, Domiciliary, or Kids account.' },
];

function initChatbot() {
    const container = document.getElementById('chatbot-faqs');
    container.innerHTML = faqs.map((f, i) =>
        `<button onclick="showAnswer(${i})" style="text-align:left;padding:10px 12px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;font-size:12px;color:#374151;cursor:pointer;width:100%">${f.q}</button>`
    ).join('');
}

function showAnswer(index) {
    document.getElementById('chatbot-faqs').parentElement.style.display = 'none';
    const answerDiv = document.getElementById('chatbot-answer');
    answerDiv.style.display = 'block';
    document.getElementById('chatbot-answer-text').innerHTML = `<strong>${faqs[index].q}</strong><br><br>${faqs[index].a}`;
}

function resetChatbot() {
    document.getElementById('chatbot-answer').style.display = 'none';
    document.getElementById('chatbot-faqs').parentElement.style.display = 'block';
}

function toggleChatbot() {
    const panel = document.getElementById('chatbot-panel');
    const isOpen = panel.style.display !== 'none';
    panel.style.display = isOpen ? 'none' : 'block';
    document.getElementById('chatbot-icon-open').style.display = isOpen ? 'block' : 'none';
    document.getElementById('chatbot-icon-close').style.display = isOpen ? 'none' : 'block';
    if (!isOpen) initChatbot();
}

function closeChatbot() {
    document.getElementById('chatbot-panel').style.display = 'none';
    document.getElementById('chatbot-icon-open').style.display = 'block';
    document.getElementById('chatbot-icon-close').style.display = 'none';
}

// ── Sidebar accordion ──
function toggleNavGroup(id) {
    var el = document.getElementById(id + '-items');
    var chev = document.getElementById(id + '-chev');
    var isOpen = el.style.display !== 'none';
    el.style.display = isOpen ? 'none' : 'block';
    chev.style.transform = isOpen ? 'rotate(-90deg)' : '';
    try {
        var s = JSON.parse(localStorage.getItem('navGroups') || '{}');
        s[id] = isOpen ? 0 : 1;
        localStorage.setItem('navGroups', JSON.stringify(s));
    } catch(e) {}
}

document.addEventListener('DOMContentLoaded', function () {
    var saved = {};
    try { saved = JSON.parse(localStorage.getItem('navGroups') || '{}'); } catch(e) {}
    document.querySelectorAll('#sideNav .nav-group').forEach(function(g) {
        var id = g.dataset.group;
        var hasActive = g.dataset.hasActive === '1';
        var el = document.getElementById(id + '-items');
        var chev = document.getElementById(id + '-chev');
        // Open if: has active link, OR explicitly saved open, OR is Banking with no saved state
        var isFirst = (id === 'snav-banking');
        var shouldOpen = hasActive || saved[id] === 1 || (isFirst && !(id in saved));
        if (shouldOpen) {
            el.style.display = 'block';
            chev.style.transform = '';
        } else {
            el.style.display = 'none';
            chev.style.transform = 'rotate(-90deg)';
        }
    });
});
</script>

{{-- ── Tawk.to Live Chat ── --}}
{{-- To activate: replace the tawk_property_id and tawk_widget_id values in your .env --}}
@if(config('portal.tawk_property_id'))
<script>
var Tawk_API = Tawk_API || {}, Tawk_LoadStart = new Date();
(function(){
    var s1 = document.createElement("script"), s0 = document.getElementsByTagName("script")[0];
    s1.async = true;
    s1.src = 'https://embed.tawk.to/{{ config("portal.tawk_property_id") }}/{{ config("portal.tawk_widget_id", "default") }}';
    s1.charset = 'UTF-8';
    s1.setAttribute('crossorigin', '*');
    s0.parentNode.insertBefore(s1, s0);
    // Pre-fill visitor info
    Tawk_API.onLoad = function() {
        Tawk_API.setAttributes({
            name:  '{{ auth("customer")->user()?->first_name }} {{ auth("customer")->user()?->last_name }}',
            email: '{{ auth("customer")->user()?->email }}',
        }, function(error){});
    };
})();
</script>
@endif
</body>
</html>
