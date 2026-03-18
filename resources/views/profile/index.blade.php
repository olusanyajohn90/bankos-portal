@extends('layouts.portal')
@section('title', 'Profile')

@section('content')
{{-- Page Header --}}
<div style="margin-bottom:28px">
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Account</p>
    <h1 style="font-size:20px;font-weight:800;color:#111827;margin:0">My Profile</h1>
</div>

<div style="max-width:640px">

    {{-- Flash messages --}}
    @if(session('success'))
    <div style="margin-bottom:16px;padding:12px 16px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;color:#15803d;font-size:13px;font-weight:500;display:flex;align-items:center;gap:8px">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div style="margin-bottom:16px;padding:12px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;color:#991b1b;font-size:13px;font-weight:500">{{ session('error') }}</div>
    @endif

    {{-- Profile Hero Card --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);overflow:hidden;margin-bottom:16px">
        {{-- Gradient banner --}}
        <div style="background:linear-gradient(135deg,#1e3a8a 0%,#2563eb 60%,#3b82f6 100%);height:72px;position:relative">
            <div style="position:absolute;right:-24px;top:-24px;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,0.07)"></div>
            <div style="position:absolute;left:30%;bottom:-10px;width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,0.05)"></div>
        </div>
        <div style="padding:0 24px 24px 24px">
            {{-- Avatar overlapping banner --}}
            <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-top:-28px;margin-bottom:16px">
                <div style="width:60px;height:60px;border-radius:50%;background:white;border:3px solid white;box-shadow:0 2px 8px rgba(0,0,0,0.12);display:grid;place-items-center;overflow:hidden;flex-shrink:0">
                    <div style="width:100%;height:100%;background:linear-gradient(135deg,#2563eb,#1d4ed8);display:flex;align-items:center;justify-content:center">
                        <span style="font-size:22px;font-weight:800;color:white;line-height:1">{{ strtoupper(substr($customer->first_name ?? 'C', 0, 1)) }}</span>
                    </div>
                </div>
                <div style="display:flex;gap:6px">
                    @php
                        $kycStyles = [
                            'level_1' => ['bg'=>'#fffbeb','color'=>'#92400e','border'=>'#fde68a'],
                            'level_2' => ['bg'=>'#eff6ff','color'=>'#1d4ed8','border'=>'#bfdbfe'],
                            'level_3' => ['bg'=>'#f0fdf4','color'=>'#15803d','border'=>'#bbf7d0'],
                        ];
                        $ks = $kycStyles[$customer->kyc_tier ?? 'level_1'] ?? $kycStyles['level_1'];
                        $kycLabel = ['level_1'=>'Tier 1','level_2'=>'Tier 2','level_3'=>'Tier 3'][$customer->kyc_tier ?? 'level_1'] ?? 'Tier 1';
                    @endphp
                    <span style="font-size:10px;font-weight:700;padding:4px 10px;border-radius:20px;background:{{ $ks['bg'] }};color:{{ $ks['color'] }};border:1px solid {{ $ks['border'] }}">KYC {{ $kycLabel }}</span>
                    <span style="font-size:10px;font-weight:700;padding:4px 10px;border-radius:20px;background:{{ $customer->status === 'active' ? '#f0fdf4' : '#f9fafb' }};color:{{ $customer->status === 'active' ? '#15803d' : '#6b7280' }};border:1px solid {{ $customer->status === 'active' ? '#bbf7d0' : '#e5e7eb' }}">
                        {{ ucfirst($customer->status ?? 'Active') }}
                    </span>
                </div>
            </div>
            <h2 style="font-size:17px;font-weight:800;color:#111827;margin:0 0 2px 0">{{ $customer->first_name }} {{ $customer->middle_name }} {{ $customer->last_name }}</h2>
            <p style="font-size:12px;color:#6b7280;margin:0">{{ $customer->email }}</p>
        </div>

        {{-- Info grid --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;border-top:1px solid #f3f4f6">
            @php
            $infoRows = [
                ['label'=>'Customer Number','value'=>$customer->customer_number ?? '—'],
                ['label'=>'Customer Type','value'=>ucfirst($customer->type ?? 'Individual')],
                ['label'=>'Date of Birth','value'=>$customer->date_of_birth ? \Carbon\Carbon::parse($customer->date_of_birth)->format('d M Y') : '—'],
                ['label'=>'Gender','value'=>ucfirst($customer->gender ?? '—')],
                ['label'=>'Last Login','value'=>$customer->last_login_at ? $customer->last_login_at->diffForHumans() : '—'],
            ];
            @endphp
            @foreach($infoRows as $i => $row)
            <div style="padding:14px 20px;{{ $i % 2 === 0 ? 'border-right:1px solid #f3f4f6;' : '' }}border-bottom:1px solid #f3f4f6">
                <p style="font-size:10px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.04em;margin:0 0 3px 0">{{ $row['label'] }}</p>
                <p style="font-size:13px;font-weight:600;color:#111827;margin:0">{{ $row['value'] }}</p>
            </div>
            @endforeach
            {{-- BVN Status --}}
            <div style="padding:14px 20px;border-bottom:1px solid #f3f4f6">
                <p style="font-size:10px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.04em;margin:0 0 3px 0">BVN Status</p>
                <div style="display:flex;align-items:center;gap:5px">
                    @if($customer->bvn_verified)
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        <span style="font-size:13px;font-weight:600;color:#15803d">Verified</span>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span style="font-size:13px;font-weight:600;color:#d97706">Not verified</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Contact Information --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:24px;margin-bottom:16px">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px">
            <div style="width:36px;height:36px;border-radius:10px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            </div>
            <div>
                <p style="font-size:13px;font-weight:700;color:#111827;margin:0">Contact Information</p>
                <p style="font-size:11px;color:#9ca3af;margin:0">Update your email and phone number</p>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            <div style="margin-bottom:14px">
                <label style="display:block;font-size:11px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Email Address</label>
                <input type="email" name="email" value="{{ old('email', $customer->email) }}"
                       style="width:100%;padding:10px 12px;border:1px solid {{ $errors->has('email') ? '#dc2626' : '#d1d5db' }};border-radius:9px;font-size:13px;box-sizing:border-box;outline:none;color:#111827"
                       placeholder="your@email.com">
                @error('email')<p style="font-size:11px;color:#dc2626;margin:4px 0 0 0">{{ $message }}</p>@enderror
            </div>
            <div style="margin-bottom:20px">
                <label style="display:block;font-size:11px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}"
                       placeholder="+234 xxx xxx xxxx"
                       style="width:100%;padding:10px 12px;border:1px solid {{ $errors->has('phone') ? '#dc2626' : '#d1d5db' }};border-radius:9px;font-size:13px;box-sizing:border-box;outline:none;color:#111827">
                @error('phone')<p style="font-size:11px;color:#dc2626;margin:4px 0 0 0">{{ $message }}</p>@enderror
            </div>
            <button type="submit" style="background:#2563eb;color:white;font-size:13px;font-weight:700;padding:11px 20px;border-radius:10px;border:none;cursor:pointer">
                Save Changes
            </button>
        </form>
    </div>

    {{-- Change Password --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:24px">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px">
            <div style="width:36px;height:36px;border-radius:10px;background:#fef9c3;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </div>
            <div>
                <p style="font-size:13px;font-weight:700;color:#111827;margin:0">Change Password</p>
                <p style="font-size:11px;color:#9ca3af;margin:0">Use a strong password of at least 8 characters</p>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.password') }}">
            @csrf
            <div style="margin-bottom:14px">
                <label style="display:block;font-size:11px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Current Password</label>
                <input type="password" name="current_password"
                       style="width:100%;padding:10px 12px;border:1px solid {{ $errors->has('current_password') ? '#dc2626' : '#d1d5db' }};border-radius:9px;font-size:13px;box-sizing:border-box;outline:none">
                @error('current_password')<p style="font-size:11px;color:#dc2626;margin:4px 0 0 0">{{ $message }}</p>@enderror
            </div>
            <div style="margin-bottom:14px">
                <label style="display:block;font-size:11px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">New Password</label>
                <input type="password" name="password"
                       style="width:100%;padding:10px 12px;border:1px solid {{ $errors->has('password') ? '#dc2626' : '#d1d5db' }};border-radius:9px;font-size:13px;box-sizing:border-box;outline:none">
                @error('password')<p style="font-size:11px;color:#dc2626;margin:4px 0 0 0">{{ $message }}</p>@enderror
            </div>
            <div style="margin-bottom:20px">
                <label style="display:block;font-size:11px;font-weight:600;color:#374151;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Confirm New Password</label>
                <input type="password" name="password_confirmation"
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none">
            </div>
            <button type="submit" style="background:#111827;color:white;font-size:13px;font-weight:700;padding:11px 20px;border-radius:10px;border:none;cursor:pointer">
                Update Password
            </button>
        </form>
    </div>

</div>
@endsection
