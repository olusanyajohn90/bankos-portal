@extends('layouts.portal')
@section('title', 'Transactions')

@section('content')
@php
    $creditTypes = ['deposit', 'disbursement', 'interest'];
    $isCredit    = fn($type) => in_array($type, $creditTypes);

    $totalCredits = $transactions->filter(fn($t) => $isCredit($t->type))->sum('amount');
    $totalDebits  = $transactions->filter(fn($t) => !$isCredit($t->type))->sum('amount');
@endphp

{{-- Page Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
    <div style="display:flex;align-items:center;gap:12px">
        <a href="{{ route('dashboard') }}" style="width:34px;height:34px;border-radius:9px;border:1px solid #e5e7eb;background:white;display:grid;place-items:center;color:#6b7280;text-decoration:none;flex-shrink:0">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        </a>
        <div>
            <h1 style="font-size:20px;font-weight:800;color:#111827;margin:0">{{ $account->account_name }}</h1>
            <p style="font-size:12px;color:#9ca3af;margin:2px 0 0;font-family:monospace">{{ $account->account_number }}</p>
        </div>
    </div>
    <a href="{{ route('accounts.show', $account->id) }}" style="font-size:13px;font-weight:600;color:#2563eb;text-decoration:none;display:flex;align-items:center;gap:6px;padding:8px 14px;border:1px solid #dbeafe;border-radius:9px;background:#eff6ff">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
        Account
    </a>
</div>

{{-- Balance + Summary Bar --}}
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;padding:20px 24px;margin-bottom:16px;box-shadow:0 1px 3px rgba(0,0,0,0.06)">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px">
        <div>
            <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin:0 0 4px">Available Balance</p>
            <p style="font-size:26px;font-weight:800;color:#111827;margin:0;font-variant-numeric:tabular-nums">
                <span style="font-size:16px;font-weight:600;color:#6b7280;margin-right:4px">{{ $account->currency ?? 'NGN' }}</span>{{ number_format((float) $account->available_balance, 2) }}
            </p>
        </div>
        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
            <div style="display:flex;align-items:center;gap:8px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:9px 14px">
                <div style="width:7px;height:7px;border-radius:50%;background:#16a34a;flex-shrink:0"></div>
                <div>
                    <p style="font-size:10px;font-weight:700;color:#15803d;text-transform:uppercase;letter-spacing:.05em;margin:0">Money In</p>
                    <p style="font-size:14px;font-weight:800;color:#15803d;margin:1px 0 0;font-family:monospace">+{{ number_format((float)$totalCredits, 2) }}</p>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:8px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:9px 14px">
                <div style="width:7px;height:7px;border-radius:50%;background:#dc2626;flex-shrink:0"></div>
                <div>
                    <p style="font-size:10px;font-weight:700;color:#991b1b;text-transform:uppercase;letter-spacing:.05em;margin:0">Money Out</p>
                    <p style="font-size:14px;font-weight:800;color:#dc2626;margin:1px 0 0;font-family:monospace">-{{ number_format((float)$totalDebits, 2) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filter controls --}}
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;padding:16px 20px;margin-bottom:16px;box-shadow:0 1px 3px rgba(0,0,0,0.06)">
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin:0 0 12px">Filter Transactions</p>
    <form method="GET" style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap">
        <div style="flex:1;min-width:160px">
            <label style="display:block;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px">Search</label>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Description or reference…"
                   style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;color:#374151;outline:none;box-sizing:border-box">
        </div>
        <div style="min-width:140px">
            <label style="display:block;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px">Type</label>
            <select name="type" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;color:#374151;outline:none;background:white;box-sizing:border-box">
                <option value="">All types</option>
                @foreach(['deposit','withdrawal','transfer','repayment','disbursement','fee','interest','reversal'] as $t)
                <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                @endforeach
            </select>
        </div>
        <div style="min-width:130px">
            <label style="display:block;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px">From Date</label>
            <input type="date" name="from" value="{{ request('from') }}"
                   style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;color:#374151;outline:none;box-sizing:border-box">
        </div>
        <div style="min-width:130px">
            <label style="display:block;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px">To Date</label>
            <input type="date" name="to" value="{{ request('to') }}"
                   style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;color:#374151;outline:none;box-sizing:border-box">
        </div>
        <div style="display:flex;gap:8px;align-items:center">
            <button type="submit"
                    style="background:#2563eb;color:white;font-size:13px;font-weight:700;padding:11px 20px;border-radius:10px;border:none;cursor:pointer;white-space:nowrap">
                Apply Filter
            </button>
            @if(request('from') || request('to') || request('q') || request('type'))
            <a href="{{ route('transactions', $account->id) }}"
               style="font-size:13px;color:#6b7280;padding:10px 14px;border:1px solid #e5e7eb;border-radius:10px;text-decoration:none;background:white;white-space:nowrap">
                Clear
            </a>
            @endif
        </div>
    </form>
</div>

{{-- Transaction list --}}
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.06)">
    @if($transactions->isEmpty())
    <div style="padding:64px 24px;text-align:center">
        <div style="width:48px;height:48px;border-radius:12px;background:#f3f4f6;display:grid;place-items:center;margin:0 auto 12px">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        </div>
        <p style="font-size:14px;font-weight:600;color:#374151;margin:0 0 4px">No transactions found</p>
        <p style="font-size:13px;color:#9ca3af;margin:0">Try adjusting your filter criteria</p>
    </div>
    @else

    {{-- Table header --}}
    <div style="display:grid;grid-template-columns:1fr auto;align-items:center;padding:12px 20px;border-bottom:1px solid #f3f4f6;background:#fafafa">
        <span style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em">Transaction</span>
        <span style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;text-align:right">Amount</span>
    </div>

    @foreach($transactions as $txn)
    @php $credit = $isCredit($txn->type); @endphp
    <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid #f9fafb;transition:background 0.15s"
         onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='transparent'">

        {{-- Left: icon + description + date --}}
        <div style="display:flex;align-items:center;gap:13px;flex:1;min-width:0">
            {{-- Icon circle --}}
            <div style="width:38px;height:38px;border-radius:50%;flex-shrink:0;display:grid;place-items:center;background:{{ $credit ? '#f0fdf4' : '#fef2f2' }};border:1px solid {{ $credit ? '#bbf7d0' : '#fecaca' }}">
                @if($credit)
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
                @else
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>
                @endif
            </div>

            {{-- Description + date --}}
            <div style="min-width:0;flex:1">
                <div style="display:flex;align-items:center;gap:7px">
                    <p style="font-size:13px;font-weight:600;color:#111827;margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $txn->description ?? ucfirst($txn->type ?? '—') }}</p>
                    <span style="font-size:10px;font-weight:700;padding:2px 7px;border-radius:5px;flex-shrink:0;background:{{ $credit ? '#f0fdf4' : '#fef2f2' }};color:{{ $credit ? '#16a34a' : '#dc2626' }}">{{ $credit ? 'CR' : 'DR' }}</span>
                </div>
                <div style="display:flex;align-items:center;gap:10px;margin-top:3px">
                    <p style="font-size:12px;color:#9ca3af;margin:0">{{ $txn->created_at?->format('d M Y, H:i') }}</p>
                    @if($txn->reference)
                    <p style="font-size:11px;color:#d1d5db;margin:0;font-family:monospace">{{ $txn->reference }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right: amount + receipt --}}
        <div style="display:flex;align-items:center;gap:12px;flex-shrink:0;margin-left:16px">
            <p style="font-size:14px;font-weight:800;color:{{ $credit ? '#16a34a' : '#dc2626' }};margin:0;font-family:monospace;text-align:right">
                {{ $credit ? '+' : '−' }}{{ number_format((float) $txn->amount, 2) }}
            </p>
            <a href="{{ route('transactions.receipt', $txn->id) }}" title="Download Receipt"
               style="width:30px;height:30px;border-radius:8px;border:1px solid #e5e7eb;background:white;display:grid;place-items:center;color:#9ca3af;text-decoration:none" target="_blank">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
            </a>
        </div>
    </div>
    @endforeach

    {{-- Pagination --}}
    @if($transactions->hasPages())
    <div style="padding:14px 20px;border-top:1px solid #f3f4f6;background:#fafafa">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
            <p style="font-size:12px;color:#9ca3af;margin:0">
                Showing {{ $transactions->firstItem() }}–{{ $transactions->lastItem() }} of {{ $transactions->total() }} transactions
            </p>
            <div style="display:flex;align-items:center;gap:6px">
                @if($transactions->onFirstPage())
                <span style="padding:7px 12px;border-radius:8px;border:1px solid #e5e7eb;font-size:13px;color:#d1d5db;background:white;cursor:default">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                </span>
                @else
                <a href="{{ $transactions->previousPageUrl() }}" style="padding:7px 12px;border-radius:8px;border:1px solid #e5e7eb;font-size:13px;color:#374151;background:white;text-decoration:none;display:flex;align-items:center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                </a>
                @endif

                @foreach($transactions->getUrlRange(max(1,$transactions->currentPage()-2), min($transactions->lastPage(),$transactions->currentPage()+2)) as $page => $url)
                @if($page == $transactions->currentPage())
                <span style="padding:7px 13px;border-radius:8px;background:#2563eb;color:white;font-size:13px;font-weight:700">{{ $page }}</span>
                @else
                <a href="{{ $url }}" style="padding:7px 13px;border-radius:8px;border:1px solid #e5e7eb;font-size:13px;color:#374151;background:white;text-decoration:none">{{ $page }}</a>
                @endif
                @endforeach

                @if($transactions->hasMorePages())
                <a href="{{ $transactions->nextPageUrl() }}" style="padding:7px 12px;border-radius:8px;border:1px solid #e5e7eb;font-size:13px;color:#374151;background:white;text-decoration:none;display:flex;align-items:center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
                @else
                <span style="padding:7px 12px;border-radius:8px;border:1px solid #e5e7eb;font-size:13px;color:#d1d5db;background:white;cursor:default">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </span>
                @endif
            </div>
        </div>
    </div>
    @endif

    @endif
</div>
@endsection
