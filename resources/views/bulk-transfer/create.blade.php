@extends('layouts.portal')
@section('title', 'New Bulk Transfer')

@section('content')

{{-- Page Header --}}
<div style="display:flex;align-items:center;gap:14px;margin-bottom:28px">
    <a href="{{ route('bulk-transfer') }}"
       style="display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:8px;border:1px solid #e5e7eb;background:white;color:#6b7280;text-decoration:none;flex-shrink:0">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div>
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Bulk Transfer</p>
        <h1 style="font-size:20px;font-weight:800;color:#111827">New Bulk Transfer</h1>
    </div>
</div>

{{-- Validation Errors --}}
@if($errors->any())
<div style="margin-bottom:20px;padding:13px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;color:#991b1b;font-size:13px">
    <ul style="margin:0;padding-left:18px;line-height:1.9">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start">

    {{-- Upload Form --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:26px">
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Step 1</p>
        <p style="font-size:15px;font-weight:800;color:#111827;margin-bottom:20px">Upload CSV File</p>

        <form method="POST" action="{{ route('bulk-transfer.upload') }}" enctype="multipart/form-data">
            @csrf

            <div style="margin-bottom:22px">
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:8px">
                    CSV File <span style="color:#dc2626">*</span>
                </label>
                <div style="border:2px dashed #d1d5db;border-radius:12px;padding:32px 20px;text-align:center;position:relative;background:#f9fafb;transition:border-color .15s"
                     onmouseover="this.style.borderColor='#93c5fd'" onmouseout="this.style.borderColor='#d1d5db'">
                    <div style="width:44px;height:44px;background:#eff6ff;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.8"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    </div>
                    <p style="font-size:13px;font-weight:700;color:#374151;margin-bottom:4px">Click to choose a CSV file</p>
                    <p style="font-size:12px;color:#9ca3af">CSV format · max 2 MB · max 100 rows</p>
                    <input type="file" name="csv_file" accept=".csv,.txt" required
                           style="position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%"
                           onchange="document.getElementById('fname').textContent=this.files[0]?.name||'';document.getElementById('fname').style.display=this.files[0]?'block':'none'">
                </div>
                <p id="fname" style="display:none;font-size:12px;color:#2563eb;margin-top:8px;font-weight:600;padding:6px 10px;background:#eff6ff;border-radius:6px"></p>
            </div>

            <div style="margin-bottom:20px">
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:8px">
                    Transfer Label <span style="font-weight:400;color:#9ca3af">(optional)</span>
                </label>
                <input type="text" name="label" placeholder="e.g. March Payroll, Vendor Payments"
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none">
            </div>

            <button type="submit"
                    style="width:100%;background:#2563eb;color:white;font-size:13px;font-weight:700;padding:12px 20px;border-radius:10px;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Upload &amp; Preview
            </button>
        </form>
    </div>

    {{-- Right column: Instructions + Template --}}
    <div style="display:flex;flex-direction:column;gap:16px">

        {{-- Instructions card --}}
        <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:14px;padding:22px">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px">
                <div style="width:28px;height:28px;background:#2563eb;border-radius:7px;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <p style="font-size:13px;font-weight:800;color:#1d4ed8">CSV Format Instructions</p>
            </div>
            <ul style="font-size:12px;color:#1e40af;padding-left:18px;line-height:2;margin:0">
                <li>First row must be the header row — it will be skipped</li>
                <li>Maximum 100 rows. One transfer per row.</li>
                <li>Required columns: <strong>Name</strong>, <strong>Account Number</strong>, <strong>Amount</strong></li>
                <li>Optional columns: <strong>Bank Code</strong>, <strong>Bank Name</strong>, <strong>Narration</strong></li>
                <li>Amount must be a positive number (e.g. <code style="background:#dbeafe;padding:1px 5px;border-radius:4px">5000</code> or <code style="background:#dbeafe;padding:1px 5px;border-radius:4px">5000.00</code>)</li>
                <li>Do not include currency symbols or commas in amounts</li>
            </ul>

            {{-- Excel tip --}}
            <div style="margin-top:14px;padding:10px 12px;background:#fef9c3;border:1px solid #fde047;border-radius:8px">
                <p style="font-size:11px;color:#713f12;line-height:1.6;margin:0">
                    <strong>Excel tip:</strong> Account numbers with leading zeros (e.g. <code style="background:#fef08a;padding:1px 4px;border-radius:3px">0123456789</code>) may be stripped by Excel.
                    Format the <em>Account Number</em> column as <strong>Text</strong> before typing, or save the file as CSV from Google Sheets.
                    Numbers shorter than 10 digits will be automatically padded with leading zeros.
                </p>
            </div>
        </div>

        {{-- Template download card --}}
        <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:22px">
            <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Step 2 (Recommended)</p>
            <p style="font-size:14px;font-weight:800;color:#111827;margin-bottom:6px">Download Template</p>
            <p style="font-size:12px;color:#6b7280;margin-bottom:16px">
                Use our pre-formatted template to ensure your CSV is structured correctly before uploading.
            </p>

            <a href="data:text/csv;charset=utf-8,Name,Account%20Number,Bank%20Code,Bank%20Name,Amount,Narration%0AJohn%20Doe,%220123456789%22,058,GTBank,50000.00,March%20Salary%0A"
               download="bulk-transfer-template.csv"
               style="display:inline-flex;align-items:center;gap:8px;background:#f0fdf4;color:#15803d;font-size:12px;font-weight:700;padding:10px 16px;border-radius:9px;text-decoration:none;border:1px solid #bbf7d0;margin-bottom:18px">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Download CSV Template
            </a>

            <div style="background:#f8fafc;border:1px solid #e5e7eb;border-radius:9px;padding:14px;overflow-x:auto">
                <p style="font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px">Template Preview</p>
                <table style="font-size:11px;color:#374151;border-collapse:collapse;white-space:nowrap;width:100%">
                    <thead>
                        <tr>
                            <th style="padding:4px 10px;background:#e5e7eb;border-radius:4px;font-weight:700;text-align:left">Name</th>
                            <th style="padding:4px 10px;background:#e5e7eb;font-weight:700;text-align:left">Account Number</th>
                            <th style="padding:4px 10px;background:#e5e7eb;font-weight:700;text-align:left">Bank Code</th>
                            <th style="padding:4px 10px;background:#e5e7eb;font-weight:700;text-align:left">Bank Name</th>
                            <th style="padding:4px 10px;background:#e5e7eb;font-weight:700;text-align:left">Amount</th>
                            <th style="padding:4px 10px;background:#e5e7eb;font-weight:700;text-align:left">Narration</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding:5px 10px;color:#6b7280">John Doe</td>
                            <td style="padding:5px 10px;color:#6b7280;font-family:monospace">0123456789</td>
                            <td style="padding:5px 10px;color:#6b7280">058</td>
                            <td style="padding:5px 10px;color:#6b7280">GTBank</td>
                            <td style="padding:5px 10px;color:#6b7280">50000</td>
                            <td style="padding:5px 10px;color:#6b7280">March Salary</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

@endsection
