@extends('layouts.portal')
@section('title', 'Apply for a Loan')

@section('content')

{{-- Back + title --}}
<div style="display:flex;align-items:center;gap:12px;margin-bottom:28px">
    <a href="{{ route('loans') }}"
       style="width:36px;height:36px;border-radius:10px;background:white;border:1px solid #e5e7eb;display:flex;align-items:center;justify-content:center;color:#6b7280;text-decoration:none;flex-shrink:0">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#111827;margin-bottom:2px">Apply for a Loan</h1>
        <p style="font-size:13px;color:#6b7280">Quick decision &middot; Competitive rates &middot; Flexible tenors</p>
    </div>
</div>

<div style="max-width:600px">

    @if($errors->any())
    <div style="margin-bottom:18px;padding:12px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;color:#991b1b;font-size:13px;font-weight:600">
        {{ $errors->first() }}
    </div>
    @endif

    {{-- Loan type picker --}}
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px">Select Loan Type</p>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:24px" id="type-grid">
        @foreach(\App\Models\LoanApplication::$types as $key => $type)
        <label style="cursor:pointer">
            <input type="radio" name="_loan_type_display" value="{{ $key }}" style="display:none"
                   onchange="document.getElementById('loan_type').value=this.value;document.querySelectorAll('.ltype-card').forEach(c=>{c.style.borderColor='#e5e7eb';c.style.background='white'});this.closest('label').querySelector('.ltype-card').style.borderColor='#2563eb';this.closest('label').querySelector('.ltype-card').style.background='#eff6ff'">
            <div class="ltype-card" style="padding:16px;background:white;border:2px solid #e5e7eb;border-radius:12px;transition:border-color .15s,background .15s">
                <p style="font-size:14px;font-weight:800;color:#111827;margin-bottom:4px">{{ $type['label'] }}</p>
                <p style="font-size:12px;color:#6b7280;line-height:1.4">{{ $type['desc'] }}</p>
            </div>
        </label>
        @endforeach
    </div>

    <form method="POST" action="{{ route('loans.apply.store') }}">
        @csrf
        <input type="hidden" name="loan_type" id="loan_type" value="personal">

        {{-- Loan Details card --}}
        <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:24px;margin-bottom:14px">
            <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:20px">Loan Details</p>

            {{-- Amount — large input --}}
            <div style="margin-bottom:18px">
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Loan Amount (NGN) <span style="color:#dc2626">*</span></label>
                <input type="number" name="requested_amount" id="amt" min="5000" step="1000" placeholder="e.g. 500,000"
                       style="width:100%;padding:14px 16px;border:1px solid #d1d5db;border-radius:9px;font-size:22px;font-weight:800;color:#111827;outline:none;box-sizing:border-box;letter-spacing:-0.5px"
                       required oninput="calcInstallment()">
                <p style="font-size:11px;color:#9ca3af;margin-top:5px">Minimum: NGN 5,000</p>
            </div>

            {{-- Tenor --}}
            <div style="margin-bottom:18px">
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Tenor (months) <span style="color:#dc2626">*</span></label>
                <div style="display:grid;grid-template-columns:repeat(9,1fr);gap:6px">
                    @foreach([3,6,9,12,18,24,36,48,60] as $m)
                    <label style="cursor:pointer;text-align:center">
                        <input type="radio" name="requested_tenor_months" value="{{ $m }}" style="display:none" {{ $m==12?'checked':'' }} onchange="calcInstallment()">
                        <div class="tenor-chip" data-months="{{ $m }}"
                             style="padding:9px 4px;border:2px solid {{ $m==12?'#2563eb':'#e5e7eb' }};border-radius:9px;background:{{ $m==12?'#2563eb':'white' }};color:{{ $m==12?'white':'#374151' }};font-size:12px;font-weight:700;transition:.15s">
                            {{ $m }}
                        </div>
                    </label>
                    @endforeach
                </div>
                <p style="font-size:11px;color:#9ca3af;margin-top:5px">Months</p>
            </div>

            {{-- Live repayment preview --}}
            <div id="preview" style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;padding:16px;margin-bottom:18px;display:none">
                <p style="font-size:11px;font-weight:700;color:#1d4ed8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px">Repayment Estimate</p>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px">
                    <div>
                        <p style="font-size:11px;color:#3b82f6;margin-bottom:3px">Monthly Payment</p>
                        <p id="est-monthly" style="font-size:16px;font-weight:800;color:#1d4ed8">—</p>
                    </div>
                    <div>
                        <p style="font-size:11px;color:#3b82f6;margin-bottom:3px">Total Payable</p>
                        <p id="est-total" style="font-size:16px;font-weight:800;color:#1d4ed8">—</p>
                    </div>
                    <div>
                        <p style="font-size:11px;color:#3b82f6;margin-bottom:3px">Total Interest</p>
                        <p id="est-interest" style="font-size:16px;font-weight:800;color:#1d4ed8">—</p>
                    </div>
                </div>
                <p style="font-size:11px;color:#93c5fd;margin-top:10px">Estimate at 24% p.a. flat. Actual rate confirmed after credit review.</p>
            </div>

            {{-- Purpose --}}
            <div style="margin-bottom:18px">
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Purpose / Description <span style="color:#dc2626">*</span></label>
                <textarea name="purpose" rows="3" placeholder="Briefly describe what you need this loan for..."
                          style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;outline:none;box-sizing:border-box;resize:vertical;line-height:1.5" required></textarea>
            </div>

            {{-- Disbursement account --}}
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Disbursement Account</label>
                <select name="account_id" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;outline:none;background:white;box-sizing:border-box">
                    <option value="">— Select account —</option>
                    @foreach($accounts as $acct)
                    <option value="{{ $acct->id }}">{{ $acct->account_name }} — {{ $acct->account_number }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Financial Information card --}}
        <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:24px;margin-bottom:14px">
            <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:20px">Financial Information</p>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Employment Status <span style="color:#dc2626">*</span></label>
                    <select name="employment_status" style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;outline:none;background:white;box-sizing:border-box" required>
                        <option value="">Select...</option>
                        <option value="employed">Employed (Salary)</option>
                        <option value="self_employed">Self-Employed</option>
                        <option value="business_owner">Business Owner</option>
                        <option value="retired">Retired</option>
                        <option value="student">Student</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Monthly Income (NGN)</label>
                    <input type="number" name="monthly_income" min="0" step="1000" placeholder="e.g. 150,000"
                           style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;outline:none;box-sizing:border-box">
                </div>
                <div style="grid-column:1/-1">
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Employer / Business Name</label>
                    <input type="text" name="employer_name" placeholder="Where do you work?"
                           style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;outline:none;box-sizing:border-box">
                </div>
            </div>
        </div>

        {{-- Collateral card --}}
        <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:24px;margin-bottom:14px">
            <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:20px">Collateral <span style="font-weight:400;text-transform:none;letter-spacing:0;font-size:11px;color:#9ca3af">(optional)</span></p>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                <div style="grid-column:1/-1">
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Collateral Description</label>
                    <input type="text" name="collateral_description" placeholder="e.g. 2019 Toyota Camry"
                           style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;outline:none;box-sizing:border-box">
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Estimated Value (NGN)</label>
                    <input type="number" name="collateral_value" min="0"
                           style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;outline:none;box-sizing:border-box">
                </div>
            </div>
        </div>

        {{-- Consent notice --}}
        <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:12px;padding:14px 16px;margin-bottom:20px">
            <p style="font-size:12px;color:#92400e;line-height:1.6">By submitting, you consent to a credit check and confirm that all information provided is accurate. Providing false information may result in application rejection and account suspension.</p>
        </div>

        <button type="submit"
                style="width:100%;background:#2563eb;color:white;font-size:13px;font-weight:700;padding:14px 20px;border-radius:10px;border:none;cursor:pointer;letter-spacing:.01em">
            Submit Loan Application
        </button>
    </form>
</div>

<script>
function calcInstallment() {
    const amount = parseFloat(document.getElementById('amt').value) || 0;
    const checked = document.querySelector('input[name="requested_tenor_months"]:checked');
    const months = checked ? parseInt(checked.value) : 12;
    if (amount < 1000) { document.getElementById('preview').style.display = 'none'; return; }
    const rate = 0.02;
    const interest = amount * rate * months;
    const total = amount + interest;
    const monthly = total / months;
    document.getElementById('est-monthly').textContent = 'NGN ' + monthly.toLocaleString('en-NG', {minimumFractionDigits:0, maximumFractionDigits:0});
    document.getElementById('est-total').textContent   = 'NGN ' + total.toLocaleString('en-NG', {minimumFractionDigits:0, maximumFractionDigits:0});
    document.getElementById('est-interest').textContent = 'NGN ' + interest.toLocaleString('en-NG', {minimumFractionDigits:0, maximumFractionDigits:0});
    document.getElementById('preview').style.display = 'block';
}

// Tenor chip visual toggle
document.querySelectorAll('input[name="requested_tenor_months"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.tenor-chip').forEach(function(chip) {
            chip.style.background    = 'white';
            chip.style.borderColor   = '#e5e7eb';
            chip.style.color         = '#374151';
        });
        var activeChip = document.querySelector('.tenor-chip[data-months="' + this.value + '"]');
        if (activeChip) {
            activeChip.style.background  = '#2563eb';
            activeChip.style.borderColor = '#2563eb';
            activeChip.style.color       = 'white';
        }
        calcInstallment();
    });
});
</script>
@endsection
