@extends('layouts.portal')
@section('title', 'Scheduled Transfers')

@section('content')

{{-- Page Header --}}
<div style="margin-bottom:28px">
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Payments</p>
    <h1 style="font-size:20px;font-weight:800;color:#111827;margin-bottom:4px">Scheduled Transfers</h1>
    <p style="font-size:13px;color:#6b7280">Schedule a one-time transfer to execute automatically on a future date and time</p>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;align-items:start">

    {{-- Schedule Form --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:26px">
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">New Transfer</p>
        <p style="font-size:15px;font-weight:800;color:#111827;margin-bottom:20px">Schedule a Transfer</p>

        @if($errors->any())
        <div style="margin-bottom:16px;padding:12px 14px;background:#fef2f2;border:1px solid #fecaca;border-radius:9px;color:#991b1b;font-size:13px;font-weight:500">
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('scheduled-transfers.store') }}">
            @csrf

            <div style="margin-bottom:14px">
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">From Account</label>
                <select name="account_id"
                        style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none;background:white" required>
                    @foreach($accounts as $a)
                    <option value="{{ $a->id }}">{{ $a->account_name }} · {{ $a->account_number }} (NGN {{ number_format($a->balance, 0) }})</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom:14px">
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Beneficiary Name</label>
                <input type="text" name="beneficiary_name" value="{{ old('beneficiary_name') }}"
                       placeholder="Full name of recipient"
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none" required>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px">
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Account Number</label>
                    <input type="text" name="beneficiary_account" value="{{ old('beneficiary_account') }}"
                           placeholder="0123456789" maxlength="10"
                           style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;letter-spacing:2px;box-sizing:border-box;outline:none;font-family:monospace" required>
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Bank Name</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name') }}"
                           placeholder="e.g. Access Bank"
                           style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none" required>
                </div>
            </div>

            <div style="margin-bottom:14px">
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Amount (NGN)</label>
                <input type="number" name="amount" value="{{ old('amount') }}"
                       min="100" step="50" placeholder="e.g. 25000"
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none" required>
            </div>

            <div style="margin-bottom:14px">
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Schedule Date &amp; Time</label>
                <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}"
                       min="{{ now()->addMinutes(30)->format('Y-m-d\TH:i') }}"
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none" required>
            </div>

            <div style="margin-bottom:18px">
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">
                    Narration <span style="font-weight:400;color:#9ca3af">(optional)</span>
                </label>
                <input type="text" name="narration" value="{{ old('narration') }}"
                       placeholder="Purpose of transfer"
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none">
            </div>

            <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:9px;padding:12px 14px;margin-bottom:18px">
                <p style="font-size:11px;color:#92400e;line-height:1.7;margin:0;font-weight:500">
                    Ensure sufficient balance is available on the scheduled date. Insufficient funds will cause the transfer to fail automatically. You may cancel any pending transfer before it executes.
                </p>
            </div>

            <button type="submit"
                    style="width:100%;background:#2563eb;color:white;font-size:13px;font-weight:700;padding:12px 20px;border-radius:10px;border:none;cursor:pointer">
                Schedule Transfer
            </button>
        </form>
    </div>

    {{-- Scheduled List --}}
    <div>
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:14px">Your Scheduled Transfers</p>

        @if($scheduled->isEmpty())
        <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:48px 24px;text-align:center">
            <div style="width:48px;height:48px;border-radius:12px;background:#f3f4f6;display:inline-flex;align-items:center;justify-content:center;margin-bottom:14px">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <p style="font-size:13px;font-weight:600;color:#374151;margin-bottom:4px">No scheduled transfers</p>
            <p style="font-size:12px;color:#9ca3af">Transfers you schedule will appear here.</p>
        </div>

        @else
        <div style="display:flex;flex-direction:column;gap:10px">
            @foreach($scheduled as $st)
            @php
                $stc = [
                    'pending'   => ['#d97706','#fef9c3','#854d0e'],
                    'processed' => ['#16a34a','#f0fdf4','#15803d'],
                    'failed'    => ['#dc2626','#fef2f2','#991b1b'],
                    'cancelled' => ['#9ca3af','#f3f4f6','#6b7280'],
                ][$st->status] ?? ['#9ca3af','#f3f4f6','#6b7280'];
                $isPast = $st->scheduled_at->isPast();
                $isOverdue = $isPast && $st->status === 'pending';
            @endphp
            <div style="background:white;border:1px solid {{ $isOverdue ? '#fecaca' : '#e5e7eb' }};border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:16px 18px">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;gap:10px">
                    <div style="min-width:0">
                        <p style="font-size:13px;font-weight:700;color:#111827;margin-bottom:3px">{{ $st->beneficiary_name }}</p>
                        <p style="font-size:11px;color:#9ca3af;font-family:monospace">{{ $st->beneficiary_account }} · {{ $st->bank_name }}</p>
                    </div>
                    <div style="text-align:right;flex-shrink:0">
                        <p style="font-size:15px;font-weight:800;color:#dc2626;white-space:nowrap">−NGN {{ number_format($st->amount, 0) }}</p>
                        <span style="display:inline-block;font-size:10px;font-weight:700;padding:3px 8px;border-radius:99px;background:{{ $stc[1] }};color:{{ $stc[2] }};letter-spacing:.04em;text-transform:uppercase;margin-top:3px">
                            {{ ucfirst($st->status) }}
                        </span>
                    </div>
                </div>

                <div style="display:flex;justify-content:space-between;align-items:center;padding-top:10px;border-top:1px solid #f3f4f6">
                    <div style="display:flex;align-items:center;gap:5px">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="{{ $isOverdue ? '#dc2626' : '#9ca3af' }}" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span style="font-size:11px;font-weight:{{ $isOverdue ? '600' : '400' }};color:{{ $isOverdue ? '#dc2626' : '#6b7280' }}">
                            {{ $st->scheduled_at->format('d M Y, H:i') }}
                            @if($isOverdue) — Overdue @endif
                        </span>
                    </div>

                    @if($st->status === 'pending')
                    <form method="POST" action="{{ route('scheduled-transfers.destroy', $st->id) }}">
                        @csrf @method('DELETE')
                        <button type="submit"
                                style="font-size:12px;font-weight:700;color:#dc2626;background:none;border:none;cursor:pointer;padding:0">
                            Cancel
                        </button>
                    </form>
                    @endif
                </div>

                @if($st->narration)
                <p style="font-size:11px;color:#6b7280;margin-top:6px;padding-top:6px;border-top:1px solid #f9fafb">{{ $st->narration }}</p>
                @endif
            </div>
            @endforeach
        </div>
        <div style="margin-top:14px">{{ $scheduled->links() }}</div>
        @endif
    </div>

</div>

@endsection
