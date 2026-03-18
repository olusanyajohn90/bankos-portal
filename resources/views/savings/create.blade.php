@extends('layouts.portal')
@section('title', 'New Savings Pocket')

@section('content')

{{-- Back + title --}}
<div style="display:flex;align-items:center;gap:12px;margin-bottom:28px">
    <a href="{{ route('savings') }}"
       style="width:36px;height:36px;border-radius:10px;background:white;border:1px solid #e5e7eb;display:flex;align-items:center;justify-content:center;color:#6b7280;text-decoration:none;flex-shrink:0">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#111827;margin-bottom:2px">New Savings Pocket</h1>
        <p style="font-size:13px;color:#6b7280">Create a named pocket to save towards a specific goal</p>
    </div>
</div>

@if($errors->any())
<div style="margin-bottom:18px;padding:12px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;color:#991b1b;font-size:13px;font-weight:600">{{ $errors->first() }}</div>
@endif

<form method="POST" action="{{ route('savings.store') }}" style="max-width:520px">
    @csrf

    {{-- Pocket details card --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:24px;margin-bottom:14px">
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:20px">Pocket Details</p>

        {{-- Icon picker --}}
        <div style="margin-bottom:18px">
            <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:8px">Choose an Icon</label>
            <div style="display:flex;gap:8px;flex-wrap:wrap" id="emoji-row">
                @foreach(['💰','🏠','✈️','🎓','💊','🚗','💍','🛍️','🏖️','⚡','🎯','🏋️'] as $e)
                <button type="button" onclick="selectEmoji('{{ $e }}')"
                        class="emoji-btn" data-emoji="{{ $e }}"
                        style="font-size:22px;width:44px;height:44px;border-radius:10px;border:2px solid transparent;cursor:pointer;background:#f9fafb;display:flex;align-items:center;justify-content:center">{{ $e }}</button>
                @endforeach
            </div>
            <input type="hidden" name="emoji" id="emoji-input" value="💰">
        </div>

        {{-- Pocket name --}}
        <div style="margin-bottom:18px">
            <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Pocket Name <span style="color:#dc2626">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}"
                   placeholder="e.g. School Fees, Emergency Fund, Dubai Trip"
                   style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;outline:none;box-sizing:border-box" required>
        </div>

        {{-- Target amount + date --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:18px">
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Target Amount <span style="font-weight:400;color:#9ca3af">(optional)</span></label>
                <input type="number" name="target_amount" value="{{ old('target_amount') }}"
                       placeholder="0.00" min="1" step="0.01"
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;outline:none;box-sizing:border-box">
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Target Date <span style="font-weight:400;color:#9ca3af">(optional)</span></label>
                <input type="date" name="target_date" value="{{ old('target_date') }}"
                       min="{{ now()->addDay()->format('Y-m-d') }}"
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;outline:none;box-sizing:border-box">
            </div>
        </div>

        {{-- Linked account --}}
        <div>
            <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Linked Account <span style="color:#dc2626">*</span></label>
            <select name="account_id" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;outline:none;background:white;box-sizing:border-box" required>
                @foreach($accounts as $acc)
                <option value="{{ $acc->id }}">{{ $acc->account_name }} — {{ $acc->account_number }} (NGN {{ number_format($acc->available_balance, 2) }})</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Pocket type card --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:24px;margin-bottom:20px">
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:16px">Pocket Type</p>

        @foreach([
            ['manual',    'Manual Pocket',         'Add funds whenever you want. You control every deposit.'],
            ['round_up',  'Round-Up Savings',       'Automatically save the kobo change from every transaction.'],
            ['scheduled', 'Scheduled Auto-Save',    'Automatically move funds on a set schedule — daily, weekly, or monthly.'],
        ] as [$val, $title, $desc])
        <label style="display:flex;align-items:flex-start;gap:14px;padding:14px 16px;border-radius:10px;border:2px solid #e5e7eb;margin-bottom:10px;cursor:pointer;transition:border-color .15s"
               onclick="this.style.borderColor='#2563eb';document.querySelectorAll('.type-card').forEach(c=>{if(c!==this)c.style.borderColor='#e5e7eb'})"
               class="type-card">
            <input type="radio" name="type" value="{{ $val }}" {{ old('type','manual') === $val ? 'checked' : '' }} style="margin-top:3px;flex-shrink:0;accent-color:#2563eb">
            <div>
                <p style="font-size:13px;font-weight:700;color:#111827;margin-bottom:3px">{{ $title }}</p>
                <p style="font-size:12px;color:#6b7280;line-height:1.5">{{ $desc }}</p>
            </div>
        </label>
        @endforeach
    </div>

    <button type="submit"
            style="width:100%;background:#2563eb;color:white;font-size:13px;font-weight:700;padding:14px 20px;border-radius:10px;border:none;cursor:pointer">
        Create Pocket
    </button>
</form>

<script>
function selectEmoji(e) {
    document.getElementById('emoji-input').value = e;
    document.querySelectorAll('.emoji-btn').forEach(function(b) {
        b.style.borderColor = b.dataset.emoji === e ? '#2563eb' : 'transparent';
        b.style.background  = b.dataset.emoji === e ? '#eff6ff' : '#f9fafb';
    });
}
selectEmoji('💰');
</script>
@endsection
