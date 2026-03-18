@extends('layouts.portal')
@section('title', 'KYC Upgrade')

@section('content')
<div style="margin-bottom:28px">
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Verification</p>
    <h1 style="font-size:20px;font-weight:800;color:#111827;margin:0">KYC Tier Upgrade</h1>
</div>

@php
$currentTier = $customer->kyc_tier ?? 'level_1';
$tierNum     = ['level_1'=>1,'level_2'=>2,'level_3'=>3][$currentTier] ?? 1;
@endphp

{{-- Tier progression stepper --}}
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:28px;margin-bottom:20px">
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin:0 0 24px 0">Your Verification Progress</p>

    {{-- Stepper --}}
    <div style="display:flex;align-items:flex-start;gap:0;margin-bottom:28px">
        @foreach($tierLimits as $tier => $limits)
        @php
        $num          = ['level_1'=>1,'level_2'=>2,'level_3'=>3][$tier];
        $isCurrent    = $tier === $currentTier;
        $isDone       = $num < $tierNum;
        $isPending    = $num > $tierNum;
        $stepBg       = $isDone ? '#2563eb' : ($isCurrent ? '#2563eb' : '#e5e7eb');
        $stepColor    = ($isDone || $isCurrent) ? 'white' : '#9ca3af';
        $isLast       = $num === 3;
        @endphp
        <div style="flex:1;display:flex;flex-direction:column;align-items:center;position:relative">
            {{-- Connector line (before, except first) --}}
            @if($num > 1)
            <div style="position:absolute;top:18px;right:50%;width:100%;height:2px;background:{{ $num <= $tierNum ? '#2563eb' : '#e5e7eb' }};z-index:0"></div>
            @endif
            {{-- Step circle --}}
            <div style="width:36px;height:36px;border-radius:50%;background:{{ $stepBg }};display:flex;align-items:center;justify-content:center;position:relative;z-index:1;box-shadow:{{ ($isDone || $isCurrent) ? '0 0 0 4px rgba(37,99,235,0.12)' : 'none' }}">
                @if($isDone)
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                @else
                <span style="font-size:13px;font-weight:800;color:{{ $stepColor }}">{{ $num }}</span>
                @endif
            </div>
            {{-- Step label --}}
            <p style="font-size:11px;font-weight:700;color:{{ ($isDone || $isCurrent) ? '#111827' : '#9ca3af' }};margin:8px 0 2px 0;text-align:center">Tier {{ $num }}</p>
            @if($isCurrent)
            <span style="font-size:9px;font-weight:700;color:#2563eb;background:#eff6ff;padding:2px 8px;border-radius:20px;letter-spacing:.04em">CURRENT</span>
            @elseif($isDone)
            <span style="font-size:9px;font-weight:700;color:#15803d;background:#f0fdf4;padding:2px 8px;border-radius:20px;letter-spacing:.04em">COMPLETE</span>
            @else
            <span style="font-size:9px;font-weight:700;color:#9ca3af;background:#f3f4f6;padding:2px 8px;border-radius:20px;letter-spacing:.04em">LOCKED</span>
            @endif
        </div>
        @endforeach
    </div>

    {{-- Tier limit cards --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px">
        @foreach($tierLimits as $tier => $limits)
        @php
        $num       = ['level_1'=>1,'level_2'=>2,'level_3'=>3][$tier];
        $isCurrent = $tier === $currentTier;
        $isDone    = $num < $tierNum;
        @endphp
        <div style="border:2px solid {{ $isCurrent ? '#2563eb' : ($isDone ? '#bbf7d0' : '#e5e7eb') }};border-radius:12px;padding:16px;background:{{ $isCurrent ? '#f8faff' : ($isDone ? '#f0fdf4' : 'white') }}">
            <p style="font-size:12px;font-weight:800;color:{{ $isCurrent ? '#1d4ed8' : ($isDone ? '#15803d' : '#6b7280') }};margin:0 0 12px 0">Tier {{ $num }}</p>
            <div style="display:flex;flex-direction:column;gap:8px">
                <div>
                    <p style="font-size:9px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.04em;margin:0 0 2px 0">Daily Transfers</p>
                    <p style="font-size:12px;font-weight:700;color:#111827;margin:0">NGN {{ is_numeric($limits['daily_transfer']) ? number_format($limits['daily_transfer']) : $limits['daily_transfer'] }}</p>
                </div>
                <div>
                    <p style="font-size:9px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.04em;margin:0 0 2px 0">Single Transaction</p>
                    <p style="font-size:12px;font-weight:700;color:#111827;margin:0">NGN {{ is_numeric($limits['single_txn']) ? number_format($limits['single_txn']) : $limits['single_txn'] }}</p>
                </div>
                <div>
                    <p style="font-size:9px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.04em;margin:0 0 2px 0">Max Balance</p>
                    <p style="font-size:12px;font-weight:700;color:#111827;margin:0">{{ is_numeric($limits['balance']) ? 'NGN '.number_format($limits['balance']) : $limits['balance'] }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Pending notice --}}
@if($pending)
<div style="background:#fffbeb;border:1px solid #fde68a;border-radius:12px;padding:20px;margin-bottom:20px;display:flex;align-items:flex-start;gap:14px">
    <div style="width:40px;height:40px;border-radius:12px;background:#fef9c3;display:flex;align-items:center;justify-content:center;flex-shrink:0">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
    </div>
    <div>
        <p style="font-size:14px;font-weight:700;color:#92400e;margin:0 0 3px 0">Upgrade Request Under Review</p>
        <p style="font-size:12px;color:#b45309;margin:0;line-height:1.5">Submitted {{ $pending->created_at->diffForHumans() }}. Our compliance team will review within 1–2 business days.</p>
    </div>
</div>

@elseif($tierNum >= 3)
{{-- Already max tier --}}
<div style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1px solid #bbf7d0;border-radius:14px;padding:36px;text-align:center">
    <div style="width:64px;height:64px;border-radius:20px;background:#16a34a;display:flex;align-items:center;justify-content:center;margin:0 auto 16px auto">
        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10" stroke-width="2.5"/></svg>
    </div>
    <p style="font-size:16px;font-weight:800;color:#166534;margin:0 0 6px 0">You're fully verified!</p>
    <p style="font-size:13px;color:#16a34a;margin:0">You're on the highest tier. No restrictions on transfers or balances.</p>
</div>

@else
{{-- Upgrade form --}}
<div style="max-width:580px">
    @if($errors->any())
    <div style="margin-bottom:16px;padding:12px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;color:#991b1b;font-size:13px">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('kyc.upgrade.store') }}" enctype="multipart/form-data">
        @csrf
        <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:24px;margin-bottom:14px">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:22px">
                <div style="width:38px;height:38px;border-radius:11px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <div>
                    <p style="font-size:13px;font-weight:700;color:#111827;margin:0">Identity Verification — Tier {{ $tierNum + 1 }}</p>
                    <p style="font-size:11px;color:#9ca3af;margin:2px 0 0 0">Provide your documents to unlock higher limits</p>
                </div>
            </div>

            {{-- BVN / NIN for Tier 1 → 2 --}}
            @if($tierNum < 2)
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px">
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">BVN (11 digits)</label>
                    <input type="text" name="bvn" maxlength="11" placeholder="Enter BVN"
                           style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none;color:#111827;font-family:monospace;letter-spacing:1px">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">NIN (11 digits)</label>
                    <input type="text" name="nin" maxlength="11" placeholder="Enter NIN"
                           style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none;color:#111827;font-family:monospace;letter-spacing:1px">
                </div>
            </div>
            {{-- Divider --}}
            <div style="height:1px;background:#f3f4f6;margin-bottom:16px"></div>
            @endif

            {{-- ID Type + Number --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px">
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">ID Type</label>
                    <div style="position:relative">
                        <select name="id_type"
                                style="width:100%;padding:10px 36px 10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;font-weight:500;color:#111827;box-sizing:border-box;outline:none;appearance:none;background:white;cursor:pointer" required>
                            <option value="">Select ID type</option>
                            <option value="national_id">National ID Card</option>
                            <option value="drivers_license">Driver's Licence</option>
                            <option value="voters_card">Voter's Card</option>
                            <option value="international_passport">International Passport</option>
                            <option value="nin_slip">NIN Slip</option>
                        </select>
                        <div style="position:absolute;right:12px;top:50%;transform:translateY(-50%);pointer-events:none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        </div>
                    </div>
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">ID Number</label>
                    <input type="text" name="id_number" placeholder="Enter ID number"
                           style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none;color:#111827" required>
                </div>
            </div>

            {{-- Document uploads --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Upload ID Document</label>
                    <label style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;padding:18px 12px;border:2px dashed #d1d5db;border-radius:10px;cursor:pointer;background:#fafafa;text-align:center"
                           for="id_document_input">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.8"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        <p style="font-size:11px;font-weight:600;color:#6b7280;margin:0">Click to upload</p>
                        <p style="font-size:10px;color:#9ca3af;margin:0">JPG, PNG or PDF</p>
                    </label>
                    <input type="file" id="id_document_input" name="id_document" accept="image/*,.pdf" style="display:none" onchange="showFileName(this,'id_doc_name')">
                    <p id="id_doc_name" style="font-size:10px;color:#6b7280;margin:4px 0 0 2px;display:none"></p>
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Selfie with ID</label>
                    <label style="display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;padding:18px 12px;border:2px dashed #d1d5db;border-radius:10px;cursor:pointer;background:#fafafa;text-align:center"
                           for="selfie_input">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.8"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                        <p style="font-size:11px;font-weight:600;color:#6b7280;margin:0">Click to upload</p>
                        <p style="font-size:10px;color:#9ca3af;margin:0">JPG or PNG</p>
                    </label>
                    <input type="file" id="selfie_input" name="selfie" accept="image/*" style="display:none" onchange="showFileName(this,'selfie_name')">
                    <p id="selfie_name" style="font-size:10px;color:#6b7280;margin:4px 0 0 2px;display:none"></p>
                </div>
            </div>
        </div>

        <button type="submit"
                style="width:100%;background:#2563eb;color:white;font-size:14px;font-weight:800;padding:14px;border-radius:12px;border:none;cursor:pointer;letter-spacing:.01em">
            Submit Upgrade Request
        </button>
    </form>
</div>
@endif

{{-- Request history --}}
@if($history->isNotEmpty())
<div style="margin-top:32px;max-width:580px">
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px">Request History</p>
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.06)">
        @foreach($history as $req)
        @php
        $sc = [
            'submitted'    => ['#d97706','#fffbeb','#fde68a'],
            'under_review' => ['#2563eb','#eff6ff','#bfdbfe'],
            'approved'     => ['#15803d','#f0fdf4','#bbf7d0'],
            'rejected'     => ['#dc2626','#fef2f2','#fecaca'],
        ][$req->status] ?? ['#6b7280','#f9fafb','#e5e7eb'];
        @endphp
        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid #f3f4f6">
            <div style="display:flex;align-items:center;gap:12px">
                <div style="width:36px;height:36px;border-radius:10px;background:{{ $sc[1] }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="{{ $sc[0] }}" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <div>
                    <p style="font-size:13px;font-weight:700;color:#111827;margin:0">
                        Tier {{ ['level_1'=>1,'level_2'=>2,'level_3'=>3][$req->current_tier]??1 }}
                        <span style="color:#9ca3af;margin:0 4px">→</span>
                        Tier {{ ['level_2'=>2,'level_3'=>3][$req->target_tier]??2 }}
                    </p>
                    <p style="font-size:11px;color:#9ca3af;margin:1px 0 0 0">{{ $req->created_at->format('d M Y') }}</p>
                </div>
            </div>
            <span style="font-size:10px;font-weight:800;padding:4px 12px;border-radius:20px;background:{{ $sc[1] }};color:{{ $sc[0] }};border:1px solid {{ $sc[2] }};letter-spacing:.04em">
                {{ strtoupper(str_replace('_',' ',$req->status)) }}
            </span>
        </div>
        @endforeach
    </div>
</div>
@endif

<script>
function showFileName(input, targetId) {
    const el = document.getElementById(targetId);
    if(input.files && input.files[0]) {
        el.textContent = input.files[0].name;
        el.style.display = 'block';
    }
}
</script>
@endsection
