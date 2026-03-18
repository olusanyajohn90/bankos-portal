@extends('layouts.portal')
@section('title', 'Account Details')

@section('content')

{{-- Page Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
    <div style="display:flex;align-items:center;gap:12px">
        <a href="{{ route('dashboard') }}" style="width:34px;height:34px;border-radius:9px;border:1px solid #e5e7eb;background:white;display:grid;place-items:center;color:#6b7280;text-decoration:none;flex-shrink:0">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        </a>
        <div>
            <h1 style="font-size:20px;font-weight:800;color:#111827;margin:0">Account Details</h1>
            <p style="font-size:13px;color:#6b7280;margin:3px 0 0">{{ ucwords(str_replace('_', ' ', $account->type ?? 'savings')) }} Account</p>
        </div>
    </div>
    <a href="{{ route('transactions', $account->id) }}" style="display:flex;align-items:center;gap:7px;font-size:13px;font-weight:700;color:#2563eb;text-decoration:none;padding:9px 16px;border:1px solid #dbeafe;border-radius:10px;background:#eff6ff">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        Transactions
    </a>
</div>

{{-- Frozen notice --}}
@if($account->is_frozen ?? false)
<div style="display:flex;align-items:center;gap:10px;padding:12px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;margin-bottom:16px">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2" style="flex-shrink:0"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    <p style="font-size:13px;font-weight:600;color:#991b1b;margin:0">This account is frozen. No transactions can be made until you unfreeze it.</p>
</div>
@endif

{{-- Hero Card (physical card design) --}}
<div style="background:linear-gradient(135deg,#0f172a 0%,#1e3a8a 45%,#2563eb 100%);border-radius:20px;padding:28px;color:white;margin-bottom:20px;box-shadow:0 8px 32px rgba(37,99,235,0.4);position:relative;overflow:hidden;min-height:180px">
    {{-- Background decoration --}}
    <div style="position:absolute;top:-40px;right:-40px;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,0.04);pointer-events:none"></div>
    <div style="position:absolute;bottom:-60px;right:40px;width:160px;height:160px;border-radius:50%;background:rgba(255,255,255,0.04);pointer-events:none"></div>

    {{-- Top row: type + status --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:22px;position:relative">
        <div>
            <p style="font-size:11px;font-weight:700;color:rgba(147,197,253,0.9);text-transform:uppercase;letter-spacing:.1em;margin:0 0 4px">{{ ucwords(str_replace('_', ' ', $account->type ?? 'Savings')) }} Account</p>
            <p style="font-size:18px;font-weight:700;color:white;margin:0">{{ $account->account_name }}</p>
        </div>
        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px">
            @if($account->is_frozen ?? false)
            <span style="font-size:10px;font-weight:700;background:rgba(239,68,68,0.25);color:#fca5a5;padding:4px 10px;border-radius:20px;border:1px solid rgba(239,68,68,0.3)">
                FROZEN
            </span>
            @else
            <span style="font-size:10px;font-weight:700;background:rgba(74,222,128,0.2);color:#86efac;padding:4px 10px;border-radius:20px;border:1px solid rgba(74,222,128,0.25)">
                ACTIVE
            </span>
            @endif
            {{-- Bank chip icon --}}
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="1"><rect x="1" y="4" width="22" height="16" rx="3"/><path d="M1 10h22"/></svg>
        </div>
    </div>

    {{-- Account number --}}
    <div style="background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.15);border-radius:12px;padding:12px 16px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;backdrop-filter:blur(4px);position:relative">
        <div>
            <p style="font-size:10px;font-weight:600;color:rgba(147,197,253,0.8);text-transform:uppercase;letter-spacing:.08em;margin:0 0 4px">Account Number</p>
            <p style="font-family:monospace;font-size:20px;font-weight:800;letter-spacing:4px;margin:0;color:white" id="acct-number">{{ $account->account_number }}</p>
        </div>
        <button onclick="copyAccountNumber()" style="background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.2);cursor:pointer;color:rgba(191,219,254,0.9);padding:8px 12px;border-radius:8px;display:flex;align-items:center;gap:6px;font-size:12px;font-weight:600" title="Copy account number">
            <svg id="copy-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
            <span id="copy-label">Copy</span>
        </button>
    </div>

    {{-- Balance row --}}
    <div style="display:grid;grid-template-columns:1fr{{ (float)$account->ledger_balance !== (float)$account->available_balance ? ' 1fr' : '' }};gap:16px;position:relative">
        <div>
            <p style="font-size:10px;font-weight:600;color:rgba(147,197,253,0.8);text-transform:uppercase;letter-spacing:.08em;margin:0 0 4px">Available Balance</p>
            <p style="font-size:26px;font-weight:800;color:white;margin:0;font-variant-numeric:tabular-nums">
                <span style="font-size:14px;font-weight:600;margin-right:4px;opacity:0.7">{{ $account->currency ?? 'NGN' }}</span>{{ number_format((float)$account->available_balance, 2) }}
            </p>
        </div>
        @if((float)$account->ledger_balance !== (float)$account->available_balance)
        <div>
            <p style="font-size:10px;font-weight:600;color:rgba(147,197,253,0.8);text-transform:uppercase;letter-spacing:.08em;margin:0 0 4px">Ledger Balance</p>
            <p style="font-size:18px;font-weight:700;color:rgba(255,255,255,0.75);margin:0;font-variant-numeric:tabular-nums">
                <span style="font-size:13px;font-weight:600;margin-right:3px;opacity:0.7">{{ $account->currency ?? 'NGN' }}</span>{{ number_format((float)$account->ledger_balance, 2) }}
            </p>
        </div>
        @endif
    </div>
</div>

{{-- Quick Actions --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:20px">
    {{-- Transfer --}}
    <a href="{{ route('transfer') }}?from={{ $account->id }}"
       style="display:flex;flex-direction:column;align-items:center;gap:9px;background:white;border:1px solid #e5e7eb;border-radius:14px;padding:16px 8px;text-decoration:none;text-align:center;box-shadow:0 1px 3px rgba(0,0,0,0.06);transition:border-color 0.15s"
       onmouseover="this.style.borderColor='#bfdbfe'" onmouseout="this.style.borderColor='#e5e7eb'">
        <div style="width:40px;height:40px;border-radius:12px;background:#eff6ff;display:grid;place-items:center">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
        </div>
        <span style="font-size:12px;font-weight:700;color:#374151">Transfer</span>
    </a>

    {{-- NIP Transfer --}}
    <a href="{{ route('interbank-transfer') }}"
       style="display:flex;flex-direction:column;align-items:center;gap:9px;background:white;border:1px solid #e5e7eb;border-radius:14px;padding:16px 8px;text-decoration:none;text-align:center;box-shadow:0 1px 3px rgba(0,0,0,0.06);transition:border-color 0.15s"
       onmouseover="this.style.borderColor='#bfdbfe'" onmouseout="this.style.borderColor='#e5e7eb'">
        <div style="width:40px;height:40px;border-radius:12px;background:#eff6ff;display:grid;place-items:center">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M7 16V4m0 0L3 8m4-4 4 4"/><path d="M17 8v12m0 0 4-4m-4 4-4-4"/></svg>
        </div>
        <span style="font-size:12px;font-weight:700;color:#374151">NIP Transfer</span>
    </a>

    {{-- Statement --}}
    <a href="{{ route('accounts.statement', $account->id) }}"
       style="display:flex;flex-direction:column;align-items:center;gap:9px;background:white;border:1px solid #e5e7eb;border-radius:14px;padding:16px 8px;text-decoration:none;text-align:center;box-shadow:0 1px 3px rgba(0,0,0,0.06);transition:border-color 0.15s"
       onmouseover="this.style.borderColor='#bbf7d0'" onmouseout="this.style.borderColor='#e5e7eb'">
        <div style="width:40px;height:40px;border-radius:12px;background:#f0fdf4;display:grid;place-items:center">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        </div>
        <span style="font-size:12px;font-weight:700;color:#374151">Statement</span>
    </a>

    {{-- Freeze / Unfreeze --}}
    @if($account->status === 'active' && !($account->is_frozen ?? false))
    <form method="POST" action="{{ route('accounts.freeze', $account->id) }}" style="display:contents">
        @csrf
        <button type="submit" onclick="return confirm('Freeze this account? No transactions will be allowed until you unfreeze it.')"
                style="display:flex;flex-direction:column;align-items:center;gap:9px;background:white;border:1px solid #e5e7eb;border-radius:14px;padding:16px 8px;text-align:center;width:100%;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.06);transition:border-color 0.15s"
                onmouseover="this.style.borderColor='#fecaca'" onmouseout="this.style.borderColor='#e5e7eb'">
            <div style="width:40px;height:40px;border-radius:12px;background:#fef2f2;display:grid;place-items:center">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </div>
            <span style="font-size:12px;font-weight:700;color:#dc2626">Freeze</span>
        </button>
    </form>
    @else
    <form method="POST" action="{{ route('accounts.unfreeze', $account->id) }}" style="display:contents">
        @csrf
        <button type="submit"
                style="display:flex;flex-direction:column;align-items:center;gap:9px;background:white;border:1px solid #e5e7eb;border-radius:14px;padding:16px 8px;text-align:center;width:100%;cursor:pointer;box-shadow:0 1px 3px rgba(0,0,0,0.06);transition:border-color 0.15s"
                onmouseover="this.style.borderColor='#bbf7d0'" onmouseout="this.style.borderColor='#e5e7eb'">
            <div style="width:40px;height:40px;border-radius:12px;background:#f0fdf4;display:grid;place-items:center">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <span style="font-size:12px;font-weight:700;color:#16a34a">Unfreeze</span>
        </button>
    </form>
    @endif
</div>

{{-- Account Details Grid --}}
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;padding:22px 24px;margin-bottom:20px;box-shadow:0 1px 3px rgba(0,0,0,0.06)">
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin:0 0 16px">Account Information</p>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
        <div>
            <p style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;margin:0 0 4px">Account Name</p>
            <p style="font-size:14px;font-weight:600;color:#111827;margin:0">{{ $account->account_name }}</p>
        </div>
        <div>
            <p style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;margin:0 0 4px">Account Number</p>
            <p style="font-size:14px;font-weight:600;color:#111827;margin:0;font-family:monospace;letter-spacing:1px">{{ $account->account_number }}</p>
        </div>
        <div>
            <p style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;margin:0 0 4px">Account Type</p>
            <p style="font-size:14px;font-weight:600;color:#111827;margin:0">{{ ucwords(str_replace('_', ' ', $account->type ?? 'Savings')) }}</p>
        </div>
        <div>
            <p style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;margin:0 0 4px">Currency</p>
            <p style="font-size:14px;font-weight:600;color:#111827;margin:0">{{ $account->currency ?? 'NGN' }}</p>
        </div>
        <div>
            <p style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;margin:0 0 4px">Status</p>
            @if($account->is_frozen ?? false)
            <span style="font-size:12px;font-weight:700;padding:3px 10px;border-radius:20px;background:#fef2f2;color:#dc2626;border:1px solid #fecaca">Frozen</span>
            @elseif($account->status === 'active')
            <span style="font-size:12px;font-weight:700;padding:3px 10px;border-radius:20px;background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0">Active</span>
            @else
            <span style="font-size:12px;font-weight:700;padding:3px 10px;border-radius:20px;background:#fefce8;color:#a16207;border:1px solid #fde68a">{{ ucfirst($account->status ?? 'Inactive') }}</span>
            @endif
        </div>
        @if($account->created_at)
        <div>
            <p style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;margin:0 0 4px">Date Opened</p>
            <p style="font-size:14px;font-weight:600;color:#111827;margin:0">{{ $account->created_at->format('d M Y') }}</p>
        </div>
        @endif
    </div>
</div>

{{-- Recent Transactions --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
    <h2 style="font-size:14px;font-weight:700;color:#111827;margin:0">Recent Transactions</h2>
    <a href="{{ route('transactions', $account->id) }}" style="font-size:13px;font-weight:600;color:#2563eb;text-decoration:none;display:flex;align-items:center;gap:4px">
        View all
        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
    </a>
</div>

<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.06)">
    @if($recent->isEmpty())
    <div style="padding:48px 24px;text-align:center">
        <div style="width:44px;height:44px;border-radius:12px;background:#f3f4f6;display:grid;place-items:center;margin:0 auto 12px">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        </div>
        <p style="font-size:14px;font-weight:600;color:#374151;margin:0 0 4px">No transactions yet</p>
        <p style="font-size:13px;color:#9ca3af;margin:0">Your recent activity will appear here</p>
    </div>
    @else
    @foreach($recent as $txn)
    @php $isCredit = in_array($txn->type, ['deposit', 'disbursement', 'interest']); @endphp
    <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid #f9fafb"
         onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='transparent'">
        <div style="display:flex;align-items:center;gap:13px;flex:1;min-width:0">
            <div style="width:38px;height:38px;border-radius:50%;flex-shrink:0;display:grid;place-items:center;background:{{ $isCredit ? '#f0fdf4' : '#fef2f2' }};border:1px solid {{ $isCredit ? '#bbf7d0' : '#fecaca' }}">
                @if($isCredit)
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
                @else
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>
                @endif
            </div>
            <div style="min-width:0">
                <p style="font-size:13px;font-weight:600;color:#111827;margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:240px">{{ $txn->description ?? ucfirst($txn->type) }}</p>
                <p style="font-size:12px;color:#9ca3af;margin:3px 0 0">{{ $txn->created_at?->format('d M Y, H:i') }}</p>
            </div>
        </div>
        <div style="text-align:right;flex-shrink:0;margin-left:12px">
            <p style="font-size:14px;font-weight:800;color:{{ $isCredit ? '#16a34a' : '#dc2626' }};margin:0;font-family:monospace">
                {{ $isCredit ? '+' : '−' }}{{ number_format((float)$txn->amount, 2) }}
            </p>
            <span style="font-size:10px;font-weight:700;padding:2px 7px;border-radius:5px;background:{{ $txn->status === 'success' ? '#f0fdf4' : '#fefce8' }};color:{{ $txn->status === 'success' ? '#15803d' : '#a16207' }}">
                {{ ucfirst($txn->status ?? 'pending') }}
            </span>
        </div>
    </div>
    @endforeach
    <div style="padding:12px 20px;border-top:1px solid #f3f4f6;text-align:center;background:#fafafa">
        <a href="{{ route('transactions', $account->id) }}" style="font-size:13px;font-weight:600;color:#2563eb;text-decoration:none">View all transactions</a>
    </div>
    @endif
</div>

<script>
function copyAccountNumber() {
    const num = document.getElementById('acct-number').innerText.replace(/\s/g, '');
    navigator.clipboard.writeText(num).then(() => {
        const icon = document.getElementById('copy-icon');
        const label = document.getElementById('copy-label');
        icon.innerHTML = '<polyline points="20 6 9 17 4 12" stroke="currentColor" stroke-width="2.5"/>';
        label.textContent = 'Copied!';
        setTimeout(() => {
            icon.innerHTML = '<rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>';
            label.textContent = 'Copy';
        }, 2000);
    });
}
</script>
@endsection
