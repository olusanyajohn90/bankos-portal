@extends('layouts.portal')
@section('title', 'Create Savings Group')

@section('content')
<div style="display:flex;align-items:center;gap:12px;margin-bottom:28px">
    <a href="{{ route('savings-groups') }}"
       style="display:flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:9px;border:1px solid #e5e7eb;color:#6b7280;text-decoration:none;flex-shrink:0;background:white">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#111827;margin:0 0 3px">Create a Savings Group</h1>
        <p style="font-size:13px;color:#6b7280;margin:0">Set up an Ajo, Thrift or Esusu group</p>
    </div>
</div>

@if($errors->any())
<div style="margin-bottom:18px;padding:13px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;color:#991b1b;font-size:13px">
    <ul style="margin:0;padding-left:18px;line-height:1.8">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div style="display:flex;align-items:flex-start;gap:10px;padding:13px 16px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;margin-bottom:20px">
    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <p style="font-size:13px;color:#1d4ed8;margin:0">You will be the first member and group admin. Others can join after you create the group.</p>
</div>

<div style="max-width:640px">
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:28px">
        <form method="POST" action="{{ route('savings-groups.store') }}">
            @csrf

            <div style="margin-bottom:20px">
                <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:7px">
                    Group Name <span style="color:#dc2626">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}" required maxlength="120"
                       placeholder="e.g. Lagos Friends Ajo 2026"
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none;color:#111827">
            </div>

            <div style="margin-bottom:20px">
                <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:7px">
                    Description <span style="font-size:11px;font-weight:400;color:#9ca3af">(optional)</span>
                </label>
                <textarea name="description" rows="3" maxlength="500"
                          placeholder="Briefly describe the purpose of this group..."
                          style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;outline:none;box-sizing:border-box;resize:vertical;color:#111827">{{ old('description') }}</textarea>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">
                <div>
                    <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:7px">
                        Contribution Amount (NGN) <span style="color:#dc2626">*</span>
                    </label>
                    <input type="number" name="contribution_amount" value="{{ old('contribution_amount') }}"
                           required min="100" step="50" placeholder="e.g. 10000"
                           style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;outline:none;box-sizing:border-box;color:#111827">
                    <p style="font-size:11px;color:#9ca3af;margin:5px 0 0">Minimum NGN 100 per member per cycle</p>
                </div>

                <div>
                    <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:7px">
                        Frequency <span style="color:#dc2626">*</span>
                    </label>
                    <select name="frequency" required
                            style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;outline:none;background:white;box-sizing:border-box;color:#111827">
                        <option value="">— Select —</option>
                        <option value="daily"   {{ old('frequency') === 'daily'   ? 'selected' : '' }}>Daily</option>
                        <option value="weekly"  {{ old('frequency') === 'weekly'  ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ old('frequency') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom:28px">
                <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:7px">
                    Maximum Members <span style="color:#dc2626">*</span>
                </label>
                <select name="max_members" required
                        style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;outline:none;background:white;box-sizing:border-box;color:#111827">
                    <option value="">— Select —</option>
                    @for($i = 2; $i <= 20; $i++)
                    <option value="{{ $i }}" {{ (int) old('max_members') === $i ? 'selected' : '' }}>
                        {{ $i }} members ({{ $i }} cycles)
                    </option>
                    @endfor
                </select>
                <p style="font-size:11px;color:#9ca3af;margin:5px 0 0">Each member receives the full pot once. Total cycles = number of members.</p>
            </div>

            <div style="display:flex;gap:10px">
                <button type="submit"
                        style="flex:1;background:#2563eb;color:white;font-size:13px;font-weight:700;padding:12px;border-radius:10px;border:none;cursor:pointer">
                    Create Group
                </button>
                <a href="{{ route('savings-groups') }}"
                   style="padding:12px 20px;border:1px solid #e5e7eb;border-radius:10px;font-size:13px;font-weight:600;color:#6b7280;text-decoration:none;display:flex;align-items:center;background:white">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
