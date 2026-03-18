<x-onboarding-layout title="Review Details" :currentStep="6">

    <div class="ob-card">
        <h2>Review your details</h2>
        <p class="ob-subtitle">Please confirm everything looks correct before creating your account.</p>

        @if($errors->any())
        <div class="ob-alert-error">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span>{{ $errors->first() }}</span>
        </div>
        @endif

        @php
            $bvn    = $onboarding['bvn'] ?? '';
            $masked = strlen($bvn) === 11
                ? substr($bvn, 0, 3) . str_repeat('*', 5) . substr($bvn, -3)
                : $bvn;
            $typeLabels = ['savings' => 'Savings Account', 'current' => 'Current Account', 'wallet' => 'Digital Wallet'];
        @endphp

        {{-- Summary table --}}
        <div style="border:1px solid #f3f4f6;border-radius:10px;overflow:hidden;margin-bottom:24px">

            {{-- Personal Info --}}
            <div style="display:flex;justify-content:space-between;align-items:center;padding:13px 16px;background:#f9fafb;border-bottom:1px solid #f3f4f6">
                <span style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em">Personal Info</span>
                <a href="{{ route('onboarding.start') }}" class="ob-link" style="font-size:12px">Edit</a>
            </div>
            <div style="padding:14px 16px;border-bottom:1px solid #f3f4f6">
                <div style="display:flex;justify-content:space-between;margin-bottom:8px">
                    <span style="font-size:12px;color:#6b7280">Full Name</span>
                    <span style="font-size:13px;font-weight:600;color:#111827">{{ $onboarding['full_name'] ?? '—' }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;margin-bottom:8px">
                    <span style="font-size:12px;color:#6b7280">Email</span>
                    <span style="font-size:13px;font-weight:600;color:#111827">{{ $onboarding['email'] ?? '—' }}</span>
                </div>
                <div style="display:flex;justify-content:space-between">
                    <span style="font-size:12px;color:#6b7280">Phone</span>
                    <span style="font-size:13px;font-weight:600;color:#111827">{{ $onboarding['phone'] ?? '—' }}</span>
                </div>
            </div>

            {{-- Account Type --}}
            <div style="display:flex;justify-content:space-between;align-items:center;padding:13px 16px;background:#f9fafb;border-bottom:1px solid #f3f4f6">
                <span style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em">Account Type</span>
                <a href="{{ route('onboarding.step3') }}" class="ob-link" style="font-size:12px">Edit</a>
            </div>
            <div style="padding:14px 16px;border-bottom:1px solid #f3f4f6">
                <div style="display:flex;justify-content:space-between">
                    <span style="font-size:12px;color:#6b7280">Type</span>
                    <span style="font-size:13px;font-weight:600;color:#111827">{{ $typeLabels[$onboarding['account_type'] ?? ''] ?? ucfirst($onboarding['account_type'] ?? '—') }}</span>
                </div>
            </div>

            {{-- BVN --}}
            <div style="display:flex;justify-content:space-between;align-items:center;padding:13px 16px;background:#f9fafb;border-bottom:1px solid #f3f4f6">
                <span style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em">Identity</span>
                <a href="{{ route('onboarding.step4') }}" class="ob-link" style="font-size:12px">Edit</a>
            </div>
            <div style="padding:14px 16px;border-bottom:1px solid #f3f4f6">
                <div style="display:flex;justify-content:space-between">
                    <span style="font-size:12px;color:#6b7280">BVN</span>
                    <span style="font-size:13px;font-weight:600;color:#111827;letter-spacing:2px">{{ $masked }}</span>
                </div>
            </div>

            {{-- ID Document --}}
            <div style="display:flex;justify-content:space-between;align-items:center;padding:13px 16px;background:#f9fafb;border-bottom:1px solid #f3f4f6">
                <span style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em">ID Document</span>
                <a href="{{ route('onboarding.step5') }}" class="ob-link" style="font-size:12px">Change</a>
            </div>
            <div style="padding:14px 16px">
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <span style="font-size:12px;color:#6b7280">Document</span>
                    @if(!empty($onboarding['id_document_path']))
                        <span style="display:flex;align-items:center;gap:5px;font-size:13px;font-weight:600;color:#16a34a">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            Uploaded
                        </span>
                    @else
                        <span style="font-size:13px;color:#9ca3af;font-style:italic">Not uploaded (optional)</span>
                    @endif
                </div>
            </div>

        </div>

        {{-- Submit --}}
        <form method="POST" action="{{ route('onboarding.submit') }}">
            @csrf
            <button type="submit" class="ob-btn ob-btn-success" style="margin-bottom:14px">
                Create My Account
            </button>
        </form>

        <p style="font-size:11px;color:#9ca3af;text-align:center;line-height:1.6">
            By creating an account, you agree to our
            <a href="#" style="color:#2563eb;text-decoration:none">Terms of Service</a>
            and
            <a href="#" style="color:#2563eb;text-decoration:none">Privacy Policy</a>.
        </p>
    </div>

    <p style="text-align:center;margin-top:18px">
        <a href="{{ route('onboarding.step5') }}" class="ob-link-muted">&larr; Back</a>
    </p>

</x-onboarding-layout>
