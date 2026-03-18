<x-onboarding-layout title="BVN Verification" :currentStep="4">

    <div class="ob-card">
        <h2>Verify your identity</h2>
        <p class="ob-subtitle">Your Bank Verification Number (BVN) is required to open an account.</p>

        @if($errors->any())
        <div class="ob-alert-error">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span>{{ $errors->first() }}</span>
        </div>
        @endif

        <form method="POST" action="{{ route('onboarding.step4.store') }}">
            @csrf

            <div class="ob-field" style="margin-bottom:18px">
                <label class="ob-label" for="bvn">Bank Verification Number (BVN)</label>
                <input
                    type="text"
                    id="bvn"
                    name="bvn"
                    value="{{ old('bvn') }}"
                    placeholder="Enter your 11-digit BVN"
                    maxlength="11"
                    inputmode="numeric"
                    pattern="[0-9]{11}"
                    autocomplete="off"
                    class="ob-input {{ $errors->has('bvn') ? 'has-error' : '' }}"
                    style="letter-spacing:3px;font-size:16px;font-weight:600"
                    oninput="this.value=this.value.replace(/\D/g,'')"
                >
                @error('bvn')
                    <p class="ob-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="ob-alert-info" style="margin-bottom:26px">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#1e40af" stroke-width="2" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                <span>Your BVN is required by CBN regulations for account opening. It is used solely to verify your identity and will not be shared with third parties.</span>
            </div>

            <button type="submit" class="ob-btn ob-btn-primary">Verify &amp; Continue</button>
        </form>
    </div>

    <p style="text-align:center;margin-top:18px">
        <a href="{{ route('onboarding.step3') }}" class="ob-link-muted">&larr; Back</a>
    </p>

</x-onboarding-layout>
