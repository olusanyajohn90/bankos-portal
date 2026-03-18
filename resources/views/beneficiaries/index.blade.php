@extends('layouts.portal')
@section('title', 'Beneficiaries')

@section('content')

{{-- Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px">
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#111827">Saved Beneficiaries</h1>
        <p style="font-size:12px;color:#9ca3af;margin-top:3px">Quick access to your frequent transfer recipients</p>
    </div>
    <button onclick="document.getElementById('add-form').scrollIntoView({behavior:'smooth'})"
            style="background:#2563eb;color:white;font-size:13px;font-weight:700;padding:11px 20px;border-radius:10px;border:none;cursor:pointer;display:flex;align-items:center;gap:7px">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add Beneficiary
    </button>
</div>

@foreach(['success','error'] as $k)
@if(session($k))
<div style="margin-bottom:16px;padding:12px 16px;background:{{ $k==='success'?'#f0fdf4':'#fef2f2' }};border:1px solid {{ $k==='success'?'#bbf7d0':'#fecaca' }};border-radius:10px;color:{{ $k==='success'?'#15803d':'#991b1b' }};font-size:13px">{{ session($k) }}</div>
@endif
@endforeach
@if($errors->any())
<div style="margin-bottom:16px;padding:12px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;color:#991b1b;font-size:13px">{{ $errors->first() }}</div>
@endif

{{-- Beneficiary list --}}
@if($beneficiaries->isEmpty())
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:56px 24px;text-align:center;margin-bottom:20px">
    <div style="width:56px;height:56px;border-radius:50%;background:#f3f4f6;display:flex;align-items:center;justify-content:center;margin:0 auto 16px">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
    </div>
    <p style="font-size:14px;font-weight:700;color:#374151;margin-bottom:4px">No saved beneficiaries</p>
    <p style="font-size:13px;color:#9ca3af">Save frequent recipients below for faster transfers</p>
</div>
@else
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);overflow:hidden;margin-bottom:20px">
    <div style="padding:14px 20px;border-bottom:1px solid #f3f4f6;display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:12px;align-items:center">
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em">Recipient</p>
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em">Account Number</p>
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em">Bank</p>
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em">Actions</p>
    </div>
    @foreach($beneficiaries as $b)
    <div style="padding:14px 20px;border-bottom:1px solid #f9fafb;display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:12px;align-items:center" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background='white'">
        {{-- Avatar + name --}}
        <div style="display:flex;align-items:center;gap:12px">
            <div style="width:38px;height:38px;border-radius:50%;background:#eff6ff;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:800;color:#2563eb;flex-shrink:0">
                {{ strtoupper(substr($b->nickname, 0, 1)) }}
            </div>
            <div>
                <p style="font-size:13px;font-weight:700;color:#111827">{{ $b->nickname }}</p>
                <p style="font-size:11px;color:#9ca3af">{{ $b->account_name }}</p>
            </div>
        </div>
        {{-- Account number --}}
        <p style="font-size:13px;color:#374151;font-family:monospace;font-weight:600">{{ $b->account_number }}</p>
        {{-- Bank --}}
        <p style="font-size:12px;color:#6b7280">{{ $b->bank_name ?: 'Same Bank' }}</p>
        {{-- Actions --}}
        <div style="display:flex;align-items:center;gap:8px">
            <a href="{{ route('transfer') }}?account_number={{ $b->account_number }}"
               style="font-size:12px;color:#2563eb;font-weight:700;text-decoration:none;padding:6px 14px;border:1px solid #bfdbfe;border-radius:8px;background:#eff6ff;white-space:nowrap">
                Transfer
            </a>
            <form method="POST" action="{{ route('beneficiaries.destroy', $b->id) }}" onsubmit="return confirm('Remove {{ addslashes($b->nickname) }} from your beneficiaries?')">
                @csrf @method('DELETE')
                <button type="submit" style="background:none;border:none;cursor:pointer;color:#d1d5db;padding:6px;border-radius:7px;display:flex" onmouseover="this.style.color='#dc2626'" onmouseout="this.style.color='#d1d5db'" title="Remove">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Add new beneficiary form --}}
<div id="add-form" style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:24px">
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:18px">Add New Beneficiary</p>

    <form method="POST" action="{{ route('beneficiaries.store') }}">
        @csrf
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px">
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Nickname <span style="color:#dc2626">*</span></label>
                <input type="text" name="nickname" placeholder="e.g. Mum, Landlord" maxlength="100" required
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none"
                       onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#d1d5db'">
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Account Number <span style="color:#dc2626">*</span></label>
                <input type="text" name="account_number" id="ben-acct" placeholder="10-digit account number" maxlength="20" required
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none;font-family:monospace"
                       onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#d1d5db'">
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Account Name <span style="color:#dc2626">*</span></label>
                <div style="position:relative">
                    <input type="text" name="account_name" id="ben-name" placeholder="Auto-filled on lookup" required
                           style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none"
                           onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#d1d5db'">
                    <span id="lookup-spinner" style="display:none;position:absolute;right:10px;top:50%;transform:translateY(-50%)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="animation:spin 1s linear infinite"><line x1="12" y1="2" x2="12" y2="6"/><line x1="12" y1="18" x2="12" y2="22"/><line x1="4.93" y1="4.93" x2="7.76" y2="7.76"/><line x1="16.24" y1="16.24" x2="19.07" y2="19.07"/><line x1="2" y1="12" x2="6" y2="12"/><line x1="18" y1="12" x2="22" y2="12"/><line x1="4.93" y1="19.07" x2="7.76" y2="16.24"/><line x1="16.24" y1="7.76" x2="19.07" y2="4.93"/></svg>
                    </span>
                </div>
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Bank Name</label>
                <input type="text" name="bank_name" placeholder="Leave blank if same bank" maxlength="100"
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none"
                       onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#d1d5db'">
            </div>
        </div>
        <button type="submit"
                style="background:#2563eb;color:white;font-size:13px;font-weight:700;padding:11px 20px;border-radius:10px;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:7px">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            Save Beneficiary
        </button>
    </form>
</div>

<style>
@keyframes spin { to { transform: translateY(-50%) rotate(360deg); } }
</style>

<script>
document.getElementById('ben-acct').addEventListener('blur', function() {
    const num = this.value.trim();
    if (num.length >= 10) {
        document.getElementById('lookup-spinner').style.display = 'inline';
        fetch('{{ route('transfer.lookup') }}?account_number=' + num)
            .then(r => r.json())
            .then(d => {
                if (d.account_name) document.getElementById('ben-name').value = d.account_name;
            })
            .catch(() => {})
            .finally(() => { document.getElementById('lookup-spinner').style.display = 'none'; });
    }
});
</script>
@endsection
