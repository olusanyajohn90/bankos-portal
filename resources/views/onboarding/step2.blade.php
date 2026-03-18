<x-onboarding-layout title="Set Password" :currentStep="2">

    <div class="ob-card">
        <h2>Secure your account</h2>
        <p class="ob-subtitle">Choose a strong password to protect your banking account.</p>

        @if($errors->any())
        <div class="ob-alert-error">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span>{{ $errors->first() }}</span>
        </div>
        @endif

        <form method="POST" action="{{ route('onboarding.step2.store') }}">
            @csrf

            <div class="ob-field">
                <label class="ob-label" for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Minimum 8 characters"
                    autocomplete="new-password"
                    class="ob-input {{ $errors->has('password') ? 'has-error' : '' }}"
                    oninput="updateStrength(this.value)"
                >
                @error('password')
                    <p class="ob-error">{{ $message }}</p>
                @enderror

                {{-- Strength indicator --}}
                <div style="margin-top:8px">
                    <div style="height:4px;background:#e5e7eb;border-radius:4px;overflow:hidden">
                        <div id="strength-bar" style="height:100%;width:0%;border-radius:4px;transition:width .25s,background .25s;background:#ef4444"></div>
                    </div>
                    <p id="strength-label" style="font-size:11px;color:#9ca3af;margin-top:4px"></p>
                </div>
            </div>

            <div class="ob-field" style="margin-bottom:28px">
                <label class="ob-label" for="password_confirmation">Confirm Password</label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    placeholder="Re-enter your password"
                    autocomplete="new-password"
                    class="ob-input"
                >
            </div>

            <button type="submit" class="ob-btn ob-btn-primary">Continue</button>
        </form>
    </div>

    <p style="text-align:center;margin-top:18px">
        <a href="{{ route('onboarding.start') }}" class="ob-link-muted">
            &larr; Back to step 1
        </a>
    </p>

    <script>
    function updateStrength(val) {
        var bar   = document.getElementById('strength-bar');
        var label = document.getElementById('strength-label');
        var score = 0;
        if (val.length >= 8)  score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;
        var levels = [
            { w: '0%',   c: '#ef4444', t: '' },
            { w: '25%',  c: '#ef4444', t: 'Weak' },
            { w: '50%',  c: '#f59e0b', t: 'Fair' },
            { w: '75%',  c: '#3b82f6', t: 'Good' },
            { w: '100%', c: '#16a34a', t: 'Strong' },
        ];
        var l = levels[score] || levels[0];
        bar.style.width      = l.w;
        bar.style.background = l.c;
        label.textContent    = l.t;
        label.style.color    = l.c;
    }
    </script>

</x-onboarding-layout>
