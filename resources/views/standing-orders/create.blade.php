@extends('layouts.portal')
@section('title', 'New Standing Order')

@section('content')

{{-- Page Header --}}
<div style="display:flex;align-items:center;gap:14px;margin-bottom:28px">
    <a href="{{ route('standing-orders') }}"
       style="display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:8px;border:1px solid #e5e7eb;background:white;color:#6b7280;text-decoration:none;flex-shrink:0">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div>
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Standing Orders</p>
        <h1 style="font-size:20px;font-weight:800;color:#111827">New Standing Order</h1>
    </div>
</div>

@if($errors->any())
<div style="margin-bottom:20px;padding:13px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;color:#991b1b;font-size:13px;font-weight:500">
    {{ $errors->first() }}
</div>
@endif

<form method="POST" action="{{ route('standing-orders.store') }}" style="max-width:540px">
    @csrf

    {{-- Order Details --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:24px;margin-bottom:16px">
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:18px">Order Details</p>

        <div style="margin-bottom:16px">
            <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">
                Label / Purpose <span style="color:#dc2626">*</span>
            </label>
            <input type="text" name="nickname" value="{{ old('nickname') }}"
                   placeholder="e.g. Monthly Rent, Weekly Savings" maxlength="100"
                   style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none" required>
        </div>

        <div style="margin-bottom:16px">
            <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">
                From Account <span style="color:#dc2626">*</span>
            </label>
            <select name="account_id"
                    style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none;background:white" required>
                @foreach($accounts as $acc)
                <option value="{{ $acc->id }}" {{ old('account_id') == $acc->id ? 'selected' : '' }}>
                    {{ $acc->account_name }} — NGN {{ number_format($acc->available_balance, 2) }}
                </option>
                @endforeach
            </select>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px">
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">
                    Beneficiary Account No. <span style="color:#dc2626">*</span>
                </label>
                <input type="text" name="beneficiary_account_number" id="so-acct"
                       value="{{ old('beneficiary_account_number') }}"
                       placeholder="0123456789" maxlength="20"
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none;font-family:monospace;letter-spacing:1px" required>
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">
                    Beneficiary Name <span style="color:#dc2626">*</span>
                </label>
                <input type="text" name="beneficiary_account_name" id="so-name"
                       value="{{ old('beneficiary_account_name') }}"
                       placeholder="Auto-filled on lookup"
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none" required>
            </div>
        </div>

        <div>
            <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">
                Amount (NGN) <span style="color:#dc2626">*</span>
            </label>
            <input type="number" name="amount" value="{{ old('amount') }}"
                   placeholder="0.00" min="1" step="0.01"
                   style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none" required>
        </div>
    </div>

    {{-- Schedule --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:24px;margin-bottom:16px">
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:18px">Schedule</p>

        <div style="margin-bottom:16px">
            <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">
                Frequency <span style="color:#dc2626">*</span>
            </label>
            <select name="frequency" id="freq" onchange="toggleFrequencyFields()"
                    style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none;background:white" required>
                <option value="monthly" {{ old('frequency','monthly') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                <option value="weekly"  {{ old('frequency') === 'weekly'  ? 'selected' : '' }}>Weekly</option>
                <option value="daily"   {{ old('frequency') === 'daily'   ? 'selected' : '' }}>Daily</option>
            </select>
        </div>

        <div id="monthly-fields" style="margin-bottom:16px">
            <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Day of Month (1–28)</label>
            <input type="number" name="day_of_month" value="{{ old('day_of_month') }}"
                   min="1" max="28" placeholder="e.g. 1 for the 1st of every month"
                   style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none">
        </div>

        <div id="weekly-fields" style="display:none;margin-bottom:16px">
            <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Day of Week</label>
            <select name="day_of_week"
                    style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none;background:white">
                @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $i => $d)
                <option value="{{ $i }}" {{ old('day_of_week') == $i ? 'selected' : '' }}>{{ $d }}</option>
                @endforeach
            </select>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">
                    Start Date <span style="color:#dc2626">*</span>
                </label>
                <input type="date" name="start_date" value="{{ old('start_date') }}"
                       min="{{ now()->format('Y-m-d') }}"
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none" required>
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">
                    End Date <span style="font-weight:400;color:#9ca3af">(optional)</span>
                </label>
                <input type="date" name="end_date" value="{{ old('end_date') }}"
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none">
            </div>
        </div>
    </div>

    <button type="submit"
            style="width:100%;background:#2563eb;color:white;font-size:13px;font-weight:700;padding:13px 20px;border-radius:10px;border:none;cursor:pointer">
        Create Standing Order
    </button>
</form>

<script>
function toggleFrequencyFields() {
    const freq = document.getElementById('freq').value;
    document.getElementById('monthly-fields').style.display = freq === 'monthly' ? 'block' : 'none';
    document.getElementById('weekly-fields').style.display  = freq === 'weekly'  ? 'block' : 'none';
}
document.getElementById('so-acct').addEventListener('blur', function() {
    const num = this.value.trim();
    if (num.length >= 10) {
        fetch('{{ route('transfer.lookup') }}?account_number=' + num)
            .then(r => r.json())
            .then(d => { if (d.account_name) document.getElementById('so-name').value = d.account_name; })
            .catch(() => {});
    }
});
</script>

@endsection
