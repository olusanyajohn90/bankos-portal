<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'BankOS') }} — Customer Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .hero-gradient { background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 60%, #1d4ed8 100%); }
        .feature-card { transition: transform .2s, box-shadow .2s; }
        .feature-card:hover { transform: translateY(-3px); box-shadow: 0 12px 32px rgba(37,99,235,.12); }
    </style>
</head>
<body class="bg-white text-gray-900 antialiased">

    {{-- NAV --}}
    <header class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-blue-600 grid place-items-center shadow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <span class="font-bold text-lg tracking-tight">bank<span class="text-blue-600">OS</span></span>
            </div>
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                Sign In
            </a>
        </div>
    </header>

    {{-- HERO --}}
    <section class="hero-gradient text-white py-24 px-4">
        <div class="max-w-4xl mx-auto text-center">
            <span class="inline-block bg-white/10 border border-white/20 text-white/90 text-xs font-semibold px-4 py-1.5 rounded-full mb-6 tracking-wide uppercase">
                Secure Customer Banking Portal
            </span>
            <h1 class="text-4xl sm:text-5xl font-extrabold leading-tight mb-6">
                Your accounts,<br>anytime, anywhere.
            </h1>
            <p class="text-blue-100 text-lg max-w-xl mx-auto mb-10">
                View balances, track transactions, and manage your accounts — all in one secure place.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('login') }}"
                   class="inline-flex items-center justify-center gap-2 bg-white text-blue-700 font-bold px-8 py-3.5 rounded-xl hover:bg-blue-50 transition-colors shadow-lg text-base">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    Access Your Account
                </a>
            </div>
            <p class="mt-6 text-blue-200 text-sm">
                Need access? Contact your branch to activate your portal account.
            </p>
        </div>
    </section>

    {{-- FEATURES --}}
    <section class="py-20 px-4 bg-gray-50">
        <div class="max-w-5xl mx-auto">
            <h2 class="text-center text-2xl font-bold text-gray-900 mb-12">Everything you need, at a glance</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">

                <div class="feature-card bg-white rounded-2xl p-8 border border-gray-100 shadow-sm text-center">
                    <div class="w-12 h-12 bg-blue-50 rounded-xl grid place-items-center mx-auto mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Account Balances</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">See your ledger and available balances across all your accounts in real time.</p>
                </div>

                <div class="feature-card bg-white rounded-2xl p-8 border border-gray-100 shadow-sm text-center">
                    <div class="w-12 h-12 bg-green-50 rounded-xl grid place-items-center mx-auto mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Transaction History</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">Browse and filter your full transaction history with credits and debits clearly marked.</p>
                </div>

                <div class="feature-card bg-white rounded-2xl p-8 border border-gray-100 shadow-sm text-center">
                    <div class="w-12 h-12 bg-purple-50 rounded-xl grid place-items-center mx-auto mb-5">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Secure & Private</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">Your data is protected with bank-grade security. Only activated accounts can sign in.</p>
                </div>

            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-16 px-4 bg-white text-center">
        <div class="max-w-lg mx-auto">
            <h2 class="text-2xl font-bold mb-3">Ready to get started?</h2>
            <p class="text-gray-500 mb-8">Your bank has already set up your access. Just sign in with the credentials provided at your branch.</p>
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3.5 rounded-xl transition-colors shadow-md">
                Sign in to your portal
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="bg-gray-50 border-t border-gray-100 py-8 px-4 text-center text-xs text-gray-400">
        <p>&copy; {{ date('Y') }} {{ config('app.name', 'BankOS') }}. All rights reserved. &nbsp;·&nbsp; Powered by BankOS Core Banking</p>
    </footer>

</body>
</html>
