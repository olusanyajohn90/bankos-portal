@extends('layouts.portal')
@section('title', 'Preview Bulk Transfer')

@section('content')
<div style="display:flex;align-items:center;gap:12px;margin-bottom:24px">
    <a href="{{ route('bulk-transfer.create') }}" style="color:#9ca3af;display:flex;padding:4px">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div>
        <h1 style="font-size:18px;font-weight:700;color:#111827">Preview &amp; Confirm</h1>
        <p style="font-size:12px;color:#9ca3af">Review the transfer details before processing</p>
    </div>
</div>

@if($errors->any())
<div style="margin-bottom:16px;padding:12px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;color:#991b1b;font-size:13px">
    <ul style="margin:0;padding-left:16px">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@if(!empty($parseErrors))
<div style="margin-bottom:16px;padding:12px 16px;background:#fff7ed;border:1px solid #fed7aa;border-radius:10px;color:#c2410c;font-size:12px">
    <p style="font-weight:600;margin-bottom:6px">Some rows were skipped due to errors:</p>
    <ul style="margin:0;padding-left:16px">
        @foreach($parseErrors as $pe)
        <li>{{ $pe }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- Summary Banner --}}
<div style="background:linear-gradient(135deg,#1e3a8a,#2563eb);border-radius:14px;padding:20px 24px;color:white;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between">
    <div>
        <p style="font-size:11px;color:rgba(191,219,254,0.8);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px">Total Transfer Amount</p>
        <p style="font-size:28px;font-weight:800">NGN {{ number_format($totalAmount, 2) }}</p>
    </div>
    <div style="background:rgba(255,255,255,0.12);border-radius:12px;padding:12px 18px;text-align:center">
        <p style="font-size:11px;color:rgba(191,219,254,0.7)">Recipients</p>
        <p style="font-size:24px;font-weight:700">{{ $rowCount }}</p>
    </div>
</div>

{{-- Recipients Table (first 20) --}}
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;margin-bottom:20px">
    <div style="padding:14px 16px;border-bottom:1px solid #f3f4f6;display:flex;justify-content:space-between;align-items:center">
        <p style="font-size:13px;font-weight:700;color:#111827">Recipients</p>
        @if($rowCount > 20)
        <p style="font-size:12px;color:#9ca3af">Showing first 20 of {{ $rowCount }}</p>
        @endif
    </div>
    <div style="overflow-x:auto">
        <table style="width:100%;border-collapse:collapse;min-width:600px">
            <thead>
                <tr style="background:#f9fafb">
                    <th style="padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-align:left;text-transform:uppercase;letter-spacing:.04em">#</th>
                    <th style="padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-align:left;text-transform:uppercase;letter-spacing:.04em">Name</th>
                    <th style="padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-align:left;text-transform:uppercase;letter-spacing:.04em">Account</th>
                    <th style="padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-align:left;text-transform:uppercase;letter-spacing:.04em">Bank</th>
                    <th style="padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-align:right;text-transform:uppercase;letter-spacing:.04em">Amount</th>
                    <th style="padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-align:left;text-transform:uppercase;letter-spacing:.04em">Narration</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($rows, 0, 20) as $row)
                <tr style="border-top:1px solid #f3f4f6">
                    <td style="padding:10px 16px;font-size:12px;color:#6b7280">{{ $row['row_number'] }}</td>
                    <td style="padding:10px 16px;font-size:13px;font-weight:500;color:#111827">{{ $row['beneficiary_name'] }}</td>
                    <td style="padding:10px 16px;font-size:12px;font-family:monospace;color:#374151">{{ $row['account_number'] }}</td>
                    <td style="padding:10px 16px;font-size:12px;color:#6b7280">
                        {{ $row['bank_name'] ?: ($row['bank_code'] ?: '—') }}
                    </td>
                    <td style="padding:10px 16px;text-align:right;font-size:13px;font-weight:700;color:#111827">
                        NGN {{ number_format($row['amount'], 2) }}
                    </td>
                    <td style="padding:10px 16px;font-size:12px;color:#6b7280">{{ $row['narration'] ?: '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($rowCount > 20)
    <div style="padding:12px 16px;border-top:1px solid #f3f4f6;background:#f9fafb;text-align:center">
        <p style="font-size:12px;color:#6b7280">+ {{ $rowCount - 20 }} more recipient(s) not shown</p>
    </div>
    @endif
</div>

{{-- Process Form --}}
<div style="background:white;border:1px solid #e5e7eb;border-radius:14px;padding:24px">
    <p style="font-size:14px;font-weight:700;color:#111827;margin-bottom:16px">Authorise Transfer</p>

    <form method="POST" action="{{ route('bulk-transfer.submit') }}">
        @csrf

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px">
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">
                    Label / Description <span style="color:#9ca3af">(optional)</span>
                </label>
                <input type="text" name="label" value="{{ old('label') }}" maxlength="120"
                       placeholder="e.g. March Payroll"
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;box-sizing:border-box">
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">
                    Source Account <span style="color:#dc2626">*</span>
                </label>
                <select name="account_id" required
                        style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;outline:none;background:white;box-sizing:border-box">
                    <option value="">— Select account —</option>
                    @foreach($accounts as $acc)
                    <option value="{{ $acc->id }}" {{ old('account_id') === $acc->id ? 'selected' : '' }}>
                        {{ $acc->account_number }} — {{ $acc->account_name }}
                        (NGN {{ number_format($acc->available_balance, 2) }})
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div style="margin-bottom:20px">
            <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">
                Transaction PIN <span style="color:#dc2626">*</span>
            </label>
            <input type="password" name="pin" maxlength="4" pattern="\d{4}" required
                   placeholder="****" inputmode="numeric"
                   style="width:160px;padding:10px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:22px;letter-spacing:10px;outline:none;text-align:center">
        </div>

        <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:12px;color:#92400e">
            By clicking Process Transfer, <strong>NGN {{ number_format($totalAmount, 2) }}</strong> will be debited from your selected account and distributed to <strong>{{ $rowCount }} recipient(s)</strong>. This action cannot be undone.
        </div>

        <div style="display:flex;gap:10px">
            <button type="submit"
                    style="flex:1;background:#dc2626;color:white;font-size:13px;font-weight:700;padding:12px;border-radius:10px;border:none;cursor:pointer">
                Process Transfer — NGN {{ number_format($totalAmount, 2) }}
            </button>
            <a href="{{ route('bulk-transfer.create') }}"
               style="padding:12px 20px;border:1px solid #d1d5db;border-radius:10px;font-size:13px;color:#374151;text-decoration:none;display:flex;align-items:center">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
