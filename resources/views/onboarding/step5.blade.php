<x-onboarding-layout title="Upload ID" :currentStep="5">

    <div class="ob-card">
        <h2>Upload ID Document <span style="font-size:13px;font-weight:500;color:#9ca3af">(Optional)</span></h2>
        <p class="ob-subtitle">Uploading a valid ID helps us verify your identity faster and may unlock higher account limits.</p>

        @if($errors->any())
        <div class="ob-alert-error">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5" style="flex-shrink:0;margin-top:1px"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span>{{ $errors->first() }}</span>
        </div>
        @endif

        <form method="POST" action="{{ route('onboarding.step5.store') }}" enctype="multipart/form-data" id="upload-form">
            @csrf

            {{-- Drop zone --}}
            <div id="drop-zone"
                onclick="document.getElementById('id_document').click()"
                style="border:2px dashed #d1d5db;border-radius:12px;padding:36px 20px;text-align:center;cursor:pointer;transition:border-color .15s,background .15s;margin-bottom:16px">
                <div id="drop-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.5" style="margin:0 auto 10px"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    <p style="font-size:13px;font-weight:600;color:#374151;margin-bottom:4px">Click to select or drag a file here</p>
                    <p style="font-size:12px;color:#9ca3af">National ID, Passport or Driver's License</p>
                    <p style="font-size:11px;color:#d1d5db;margin-top:6px">JPG, PNG or PDF &mdash; max 5 MB</p>
                </div>
                <div id="drop-preview" style="display:none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="1.5" style="margin:0 auto 8px"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <p id="drop-filename" style="font-size:13px;font-weight:600;color:#111827;margin-bottom:4px"></p>
                    <p style="font-size:12px;color:#16a34a">File selected. Click to change.</p>
                </div>
            </div>

            <input
                type="file"
                id="id_document"
                name="id_document"
                accept="image/*,.pdf"
                style="display:none"
                onchange="handleFile(this)"
            >

            <div style="margin-bottom:26px">
                <p style="font-size:12px;color:#6b7280;margin-bottom:8px;font-weight:600">Accepted documents:</p>
                <div style="display:flex;flex-wrap:wrap;gap:8px">
                    @foreach(['National ID Card', 'International Passport', "Driver's License", 'Voter's Card'] as $doc)
                    <span style="padding:4px 10px;background:#f3f4f6;border-radius:20px;font-size:11px;color:#374151;font-weight:500">{{ $doc }}</span>
                    @endforeach
                </div>
            </div>

            <button type="submit" id="upload-btn" class="ob-btn ob-btn-primary">Continue</button>
        </form>

        <div style="text-align:center;margin-top:14px">
            <form method="POST" action="{{ route('onboarding.step5.store') }}" style="display:inline">
                @csrf
                <button type="submit" style="background:none;border:none;cursor:pointer;font-size:13px;color:#6b7280;text-decoration:underline;font-weight:500">
                    Skip for now
                </button>
            </form>
        </div>
    </div>

    <p style="text-align:center;margin-top:18px">
        <a href="{{ route('onboarding.step4') }}" class="ob-link-muted">&larr; Back</a>
    </p>

    <script>
    var dropZone = document.getElementById('drop-zone');

    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        dropZone.style.borderColor = '#2563eb';
        dropZone.style.background  = '#eff6ff';
    });
    dropZone.addEventListener('dragleave', function() {
        dropZone.style.borderColor = '#d1d5db';
        dropZone.style.background  = '';
    });
    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        dropZone.style.borderColor = '#d1d5db';
        dropZone.style.background  = '';
        var files = e.dataTransfer.files;
        if (files.length) {
            var input = document.getElementById('id_document');
            // Transfer dropped file to input via DataTransfer
            var dt = new DataTransfer();
            dt.items.add(files[0]);
            input.files = dt.files;
            showPreview(files[0].name);
        }
    });

    function handleFile(input) {
        if (input.files && input.files[0]) {
            showPreview(input.files[0].name);
        }
    }

    function showPreview(name) {
        document.getElementById('drop-icon').style.display    = 'none';
        document.getElementById('drop-preview').style.display = 'block';
        document.getElementById('drop-filename').textContent  = name;
        dropZone.style.borderColor = '#16a34a';
        dropZone.style.background  = '#f0fdf4';
    }
    </script>

</x-onboarding-layout>
