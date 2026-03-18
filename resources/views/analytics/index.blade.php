@extends('layouts.portal')
@section('title', 'Spending Analytics')

@section('content')

<div style="margin-bottom:28px">
    <h1 style="font-size:20px;font-weight:800;color:#111827;margin:0 0 4px">Analytics</h1>
    <p style="font-size:13px;color:#6b7280;margin:0">Understand your income and spending patterns</p>
</div>

@php
    $income   = (float)($thisMonth->income   ?? 0);
    $expenses = (float)($thisMonth->expenses ?? 0);
    $lastExp  = (float)($lastMonth->expenses ?? 0);
    $diff     = $expenses - $lastExp;
    $net      = $income - $expenses;
@endphp

{{-- Summary Stats --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:20px">
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:18px 20px">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
            <div style="width:32px;height:32px;border-radius:8px;background:#f0fdf4;display:grid;place-items:center;flex-shrink:0">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="2.5"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
            </div>
            <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin:0">Income This Month</p>
        </div>
        <p style="font-size:22px;font-weight:800;color:#15803d;margin:0">+{{ number_format($income, 2) }}</p>
    </div>
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:18px 20px">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
            <div style="width:32px;height:32px;border-radius:8px;background:#fef2f2;display:grid;place-items:center;flex-shrink:0">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5"><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/><polyline points="17 18 23 18 23 12"/></svg>
            </div>
            <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin:0">Spent This Month</p>
        </div>
        <p style="font-size:22px;font-weight:800;color:#dc2626;margin:0">&minus;{{ number_format($expenses, 2) }}</p>
    </div>
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:18px 20px">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
            <div style="width:32px;height:32px;border-radius:8px;background:#eff6ff;display:grid;place-items:center;flex-shrink:0">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
            </div>
            <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;margin:0">vs Last Month</p>
        </div>
        <p style="font-size:22px;font-weight:800;color:{{ $diff > 0 ? '#dc2626' : '#15803d' }};margin:0 0 3px">
            {{ $diff > 0 ? '+' : '' }}{{ number_format($diff, 2) }}
        </p>
        <p style="font-size:11px;color:#9ca3af;margin:0">{{ $diff > 0 ? 'Spent more than last month' : ($diff < 0 ? 'Spent less than last month' : 'Same as last month') }}</p>
    </div>
</div>

{{-- Income vs Expenses Chart --}}
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:22px;margin-bottom:18px">
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin:0 0 18px">Income vs Expenses — Last 6 Months</p>
    <canvas id="incomeExpenseChart" height="200"></canvas>
</div>

{{-- Spending by Category --}}
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:22px">
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin:0 0 18px">Spending by Category — Last 30 Days</p>

    @if(empty($categories) || (is_object($categories) ? $categories->isEmpty() : count($categories) === 0))
    <div style="text-align:center;padding:28px 0">
        <p style="font-size:13px;color:#9ca3af;margin:0">No spending data for this period.</p>
    </div>
    @else
    @php
        $catTotal = is_object($categories) ? $categories->sum('total') : array_sum(array_column((array)$categories, 'total'));
        $colors   = ['#2563eb','#7c3aed','#db2777','#d97706','#059669','#0ea5e9','#dc2626','#6b7280'];
    @endphp
    <div style="display:flex;flex-direction:column;gap:14px">
        @foreach($categories as $i => $cat)
        @php
            $catPct = $catTotal > 0 ? round(($cat->total / $catTotal) * 100, 1) : 0;
            $color  = $colors[$i % count($colors)];
        @endphp
        <div>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
                <div style="display:flex;align-items:center;gap:8px">
                    <div style="width:10px;height:10px;border-radius:3px;background:{{ $color }};flex-shrink:0"></div>
                    <span style="font-size:13px;font-weight:600;color:#374151">{{ ucwords(str_replace('_', ' ', $cat->type)) }}</span>
                </div>
                <div style="text-align:right">
                    <span style="font-size:13px;font-weight:700;color:#111827">NGN {{ number_format($cat->total, 2) }}</span>
                    <span style="font-size:11px;color:#9ca3af;margin-left:5px">{{ $catPct }}%</span>
                </div>
            </div>
            <div style="background:#f3f4f6;border-radius:99px;height:7px;overflow:hidden">
                <div style="background:{{ $color }};height:100%;width:{{ $catPct }}%;border-radius:99px"></div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
fetch('{{ route('analytics.data') }}')
    .then(function(r) { return r.json(); })
    .then(function(data) {
        var ctx = document.getElementById('incomeExpenseChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [
                    {
                        label: 'Income',
                        data: data.income,
                        backgroundColor: 'rgba(21,128,61,0.75)',
                        borderRadius: 6,
                        borderSkipped: false,
                    },
                    {
                        label: 'Expenses',
                        data: data.expense,
                        backgroundColor: 'rgba(220,38,38,0.75)',
                        borderRadius: 6,
                        borderSkipped: false,
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top', labels: { font: { size: 12, weight: '600' }, boxWidth: 12, padding: 16 } }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 12 } } },
                    y: {
                        grid: { color: '#f3f4f6' },
                        ticks: {
                            font: { size: 11 },
                            callback: function(v) { return 'NGN ' + v.toLocaleString(); }
                        }
                    }
                }
            }
        });
    });
</script>

@endsection
