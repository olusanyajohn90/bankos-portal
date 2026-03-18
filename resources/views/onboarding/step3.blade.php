<x-onboarding-layout title="Account Type" :currentStep="3">

    <div class="ob-card">
        <h2>Choose your account type</h2>
        <p class="ob-subtitle">Select the account that best suits your banking needs.</p>

        @if($errors->any())
        <div class="ob-alert-error">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span>{{ $errors->first() }}</span>
        </div>
        @endif

        <form method="POST" action="{{ route('onboarding.step3.store') }}" id="step3-form">
            @csrf
            <input type="hidden" name="account_type" id="selected-type" value="">

            <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:28px">

                {{-- Savings --}}
                <div class="acct-card" data-type="savings" onclick="selectType('savings')"
                    style="border:2px solid #e5e7eb;border-radius:12px;padding:16px 18px;cursor:pointer;display:flex;align-items:flex-start;gap:14px;transition:border-color .15s,background .15s">
                    <div style="width:40px;height:40px;border-radius:10px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2z"/><path d="M12 6v6l4 2"/></svg>
                    </div>
                    <div style="flex:1">
                        <p style="font-size:14px;font-weight:700;color:#111827;margin-bottom:2px">Savings Account</p>
                        <p style="font-size:12px;color:#6b7280;line-height:1.5">Earn interest on your deposits. Ideal for personal savings and everyday banking.</p>
                        <p style="font-size:11px;color:#2563eb;font-weight:600;margin-top:4px">Interest bearing &middot; No minimum balance</p>
                    </div>
                    <div class="acct-check" style="width:20px;height:20px;border-radius:50%;border:2px solid #e5e7eb;flex-shrink:0;display:flex;align-items:center;justify-content:center;transition:all .15s"></div>
                </div>

                {{-- Current --}}
                <div class="acct-card" data-type="current" onclick="selectType('current')"
                    style="border:2px solid #e5e7eb;border-radius:12px;padding:16px 18px;cursor:pointer;display:flex;align-items:flex-start;gap:14px;transition:border-color .15s,background .15s">
                    <div style="width:40px;height:40px;border-radius:10px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                    </div>
                    <div style="flex:1">
                        <p style="font-size:14px;font-weight:700;color:#111827;margin-bottom:2px">Current Account</p>
                        <p style="font-size:12px;color:#6b7280;line-height:1.5">Unlimited transactions for businesses and high-volume banking needs.</p>
                        <p style="font-size:11px;color:#16a34a;font-weight:600;margin-top:4px">Cheque eligible &middot; Overdraft facility available</p>
                    </div>
                    <div class="acct-check" style="width:20px;height:20px;border-radius:50%;border:2px solid #e5e7eb;flex-shrink:0;display:flex;align-items:center;justify-content:center;transition:all .15s"></div>
                </div>

                {{-- Wallet --}}
                <div class="acct-card" data-type="wallet" onclick="selectType('wallet')"
                    style="border:2px solid #e5e7eb;border-radius:12px;padding:16px 18px;cursor:pointer;display:flex;align-items:flex-start;gap:14px;transition:border-color .15s,background .15s">
                    <div style="width:40px;height:40px;border-radius:10px;background:#faf5ff;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2"><path d="M20 12V8a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-4"/><circle cx="17" cy="12" r="1"/></svg>
                    </div>
                    <div style="flex:1">
                        <p style="font-size:14px;font-weight:700;color:#111827;margin-bottom:2px">Digital Wallet</p>
                        <p style="font-size:12px;color:#6b7280;line-height:1.5">Fast digital payments, transfers, and mobile money. Great for everyday spending.</p>
                        <p style="font-size:11px;color:#7c3aed;font-weight:600;margin-top:4px">Instant transfers &middot; Zero maintenance fee</p>
                    </div>
                    <div class="acct-check" style="width:20px;height:20px;border-radius:50%;border:2px solid #e5e7eb;flex-shrink:0;display:flex;align-items:center;justify-content:center;transition:all .15s"></div>
                </div>

            </div>

            <button type="submit" id="next-btn" class="ob-btn ob-btn-primary" disabled>Continue</button>
        </form>
    </div>

    <p style="text-align:center;margin-top:18px">
        <a href="{{ route('onboarding.step2') }}" class="ob-link-muted">&larr; Back</a>
    </p>

    <style>
        .acct-card.selected {
            border-color: #2563eb !important;
            background: #f0f6ff;
        }
        .acct-card.selected .acct-check {
            background: #2563eb;
            border-color: #2563eb;
        }
    </style>

    <script>
    function selectType(type) {
        document.querySelectorAll('.acct-card').forEach(function(card) {
            card.classList.remove('selected');
        });
        var chosen = document.querySelector('.acct-card[data-type="' + type + '"]');
        if (chosen) chosen.classList.add('selected');
        document.getElementById('selected-type').value = type;
        document.getElementById('next-btn').disabled = false;

        // Update check mark
        chosen.querySelector('.acct-check').innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3.5"><polyline points="20 6 9 17 4 12"/></svg>';
        document.querySelectorAll('.acct-card:not(.selected) .acct-check').forEach(function(el) {
            el.innerHTML = '';
        });
    }
    </script>

</x-onboarding-layout>
