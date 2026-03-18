@extends('layouts.portal')
@section('title', 'Budget Planner')

@section('content')
@php $cats = \App\Models\PortalBudget::$categories; @endphp

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:10px">
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#111827;margin:0 0 4px">Budget Planner</h1>
        <p style="font-size:13px;color:#6b7280;margin:0">{{ now()->format('F Y') }} spending vs your budgets</p>
    </div>
</div>

{{-- Summary Hero --}}
<div style="background:linear-gradient(135deg,#1e40af 0%,#1d4ed8 60%,#2563eb 100%);border-radius:16px;padding:24px 28px;color:white;margin-bottom:22px;position:relative;overflow:hidden">
    <div style="position:absolute;right:-20px;top:-20px;width:140px;height:140px;border-radius:50%;background:rgba(255,255,255,0.05)"></div>
    <div style="position:absolute;right:50px;bottom:-40px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,0.03)"></div>
    <div style="position:relative;display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px">
        <div>
            <p style="color:rgba(255,255,255,0.6);font-size:11px;margin:0 0 5px;text-transform:uppercase;letter-spacing:.05em;font-weight:600">Total Budget</p>
            <p style="font-size:24px;font-weight:800;margin:0">NGN {{ number_format($totalBudget, 0) }}</p>
        </div>
        <div>
            <p style="color:rgba(255,255,255,0.6);font-size:11px;margin:0 0 5px;text-transform:uppercase;letter-spacing:.05em;font-weight:600">Total Spent</p>
            <p style="font-size:24px;font-weight:800;color:{{ $totalSpent > $totalBudget ? '#fca5a5' : 'white' }};margin:0">NGN {{ number_format($totalSpent, 0) }}</p>
        </div>
        <div>
            <p style="color:rgba(255,255,255,0.6);font-size:11px;margin:0 0 5px;text-transform:uppercase;letter-spacing:.05em;font-weight:600">Remaining</p>
            @php $remaining = $totalBudget - $totalSpent; @endphp
            <p style="font-size:24px;font-weight:800;color:{{ $remaining < 0 ? '#fca5a5' : '#86efac' }};margin:0">
                NGN {{ number_format(max(0, $remaining), 0) }}
            </p>
        </div>
    </div>
    @php $overallPct = $totalBudget > 0 ? min(100, round(($totalSpent / $totalBudget) * 100)) : 0; @endphp
    <div style="position:relative;margin-top:18px">
        <div style="height:5px;background:rgba(255,255,255,0.2);border-radius:99px;overflow:hidden">
            <div style="height:100%;width:{{ $overallPct }}%;background:{{ $totalSpent > $totalBudget ? '#fca5a5' : 'rgba(255,255,255,0.8)' }};border-radius:99px"></div>
        </div>
        <p style="font-size:11px;color:rgba(255,255,255,0.6);margin:5px 0 0">{{ $overallPct }}% of total budget used</p>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;align-items:start">

    {{-- Left: Budget Category Cards --}}
    <div>
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin:0 0 14px">Category Budgets</p>
        <div style="display:flex;flex-direction:column;gap:10px">
            @foreach($cats as $key => $cat)
            @php
                $budget = $budgets[$key] ?? null;
                $spent  = $spending[$key] ?? 0;
                $limit  = $budget ? (float)$budget->monthly_limit : 0;
                $pct    = $limit > 0 ? min(100, ($spent / $limit) * 100) : ($spent > 0 ? 100 : 0);
                $over   = $limit > 0 && $spent > $limit;
            @endphp
            <div style="background:white;border:1px solid {{ $over ? '#fecaca' : '#e5e7eb' }};border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:16px 18px">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:{{ $budget ? '10px' : '0' }}">
                    <div style="display:flex;align-items:center;gap:10px">
                        <div style="width:36px;height:36px;border-radius:10px;background:#f3f4f6;display:grid;place-items:center;flex-shrink:0">
                            <span style="font-size:18px;line-height:1">{{ $cat['emoji'] }}</span>
                        </div>
                        <div>
                            <p style="font-size:13px;font-weight:700;color:#111827;margin:0 0 2px">{{ $cat['label'] }}</p>
                            <p style="font-size:11px;color:#9ca3af;margin:0">Spent: <span style="font-weight:600;color:{{ $over ? '#dc2626' : '#374151' }}">NGN {{ number_format($spent, 0) }}</span></p>
                        </div>
                    </div>
                    @if($budget)
                    <div style="text-align:right;flex-shrink:0">
                        @if($over)
                        <p style="font-size:12px;font-weight:800;color:#dc2626;margin:0 0 1px">Over budget</p>
                        @else
                        <p style="font-size:13px;font-weight:800;color:#111827;margin:0 0 1px">{{ number_format($pct, 0) }}%</p>
                        @endif
                        <p style="font-size:11px;color:#9ca3af;margin:0">of NGN {{ number_format($limit, 0) }}</p>
                    </div>
                    @else
                    <span style="font-size:11px;color:#d1d5db;font-weight:600">Not set</span>
                    @endif
                </div>
                @if($budget)
                <div style="height:5px;background:#f3f4f6;border-radius:99px;overflow:hidden">
                    <div style="height:100%;width:{{ $pct }}%;background:{{ $over ? '#dc2626' : $cat['color'] }};border-radius:99px"></div>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- Right: Set Budget Form --}}
    <div style="position:sticky;top:80px">
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin:0 0 14px">Set / Update Budget</p>
        <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:22px">
            <form method="POST" action="{{ route('budget.store') }}">
                @csrf
                <div style="margin-bottom:16px">
                    <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:7px">Category</label>
                    <select name="category" required
                            style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;outline:none;background:white;box-sizing:border-box;color:#111827">
                        @foreach($cats as $key => $cat)
                        <option value="{{ $key }}">{{ $cat['emoji'] }} {{ $cat['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-bottom:18px">
                    <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:7px">Monthly Limit (NGN)</label>
                    <input type="number" name="monthly_limit" min="100" step="500" placeholder="e.g. 30,000"
                           style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;outline:none;box-sizing:border-box;color:#111827" required>
                </div>
                <button type="submit"
                        style="width:100%;background:#2563eb;color:white;font-size:13px;font-weight:700;padding:12px;border-radius:10px;border:none;cursor:pointer">
                    Save Budget
                </button>
            </form>

            @if($budgets->isNotEmpty())
            <div style="margin-top:18px;border-top:1px solid #f3f4f6;padding-top:16px">
                <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin:0 0 10px">Remove a Budget</p>
                @foreach($budgets as $cat => $b)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid #f9fafb">
                    <span style="font-size:12px;font-weight:600;color:#374151">
                        {{ \App\Models\PortalBudget::$categories[$cat]['emoji'] ?? '' }}
                        {{ \App\Models\PortalBudget::$categories[$cat]['label'] ?? $cat }}
                    </span>
                    <form method="POST" action="{{ route('budget.destroy', $b->id) }}">
                        @csrf @method('DELETE')
                        <button type="submit"
                                style="font-size:11px;font-weight:700;color:#dc2626;background:none;border:none;cursor:pointer;padding:0">
                            Remove
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:16px;margin-top:14px">
            <p style="font-size:12px;font-weight:700;color:#166534;margin:0 0 5px">Smart Tip</p>
            <p style="font-size:12px;color:#166534;line-height:1.6;margin:0">Set budgets for your top spending categories. We auto-categorize transactions so your progress updates automatically.</p>
        </div>
    </div>

</div>
@endsection
