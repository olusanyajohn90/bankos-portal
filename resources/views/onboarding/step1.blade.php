<x-onboarding-layout title="Personal Info" :currentStep="1">

    <div class="ob-card">
        <h2>Create your account</h2>
        <p class="ob-subtitle">Enter your personal details to get started. It only takes a few minutes.</p>

        @if($errors->any())
        <div class="ob-alert-error">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span>{{ $errors->first() }}</span>
        </div>
        @endif

        <form method="POST" action="{{ route('onboarding.step1') }}">
            @csrf

            <div class="ob-field">
                <label class="ob-label" for="full_name">Full Name</label>
                <input
                    type="text"
                    id="full_name"
                    name="full_name"
                    value="{{ old('full_name') }}"
                    placeholder="e.g. Amara Chukwu"
                    autofocus
                    autocomplete="name"
                    class="ob-input {{ $errors->has('full_name') ? 'has-error' : '' }}"
                >
                @error('full_name')
                    <p class="ob-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="ob-field">
                <label class="ob-label" for="email">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="you@example.com"
                    autocomplete="email"
                    class="ob-input {{ $errors->has('email') ? 'has-error' : '' }}"
                >
                @error('email')
                    <p class="ob-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="ob-field" style="margin-bottom:28px">
                <label class="ob-label" for="phone">Phone Number</label>
                <input
                    type="tel"
                    id="phone"
                    name="phone"
                    value="{{ old('phone') }}"
                    placeholder="e.g. 08012345678"
                    autocomplete="tel"
                    class="ob-input {{ $errors->has('phone') ? 'has-error' : '' }}"
                >
                @error('phone')
                    <p class="ob-error">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="ob-btn ob-btn-primary">Continue</button>
        </form>
    </div>

    <p style="text-align:center;margin-top:18px;font-size:13px;color:#6b7280">
        Already have an account?
        <a href="{{ route('login') }}" class="ob-link">Sign In</a>
    </p>

</x-onboarding-layout>
