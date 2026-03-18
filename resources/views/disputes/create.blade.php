@extends('layouts.portal')
@section('title', 'Raise a Dispute')

@section('content')

{{-- Page Header --}}
<div style="display:flex;align-items:center;gap:14px;margin-bottom:28px">
    <a href="{{ route('disputes') }}"
       style="display:inline-flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:8px;border:1px solid #e5e7eb;background:white;color:#6b7280;text-decoration:none;flex-shrink:0">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div>
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px">Disputes Centre</p>
        <h1 style="font-size:20px;font-weight:800;color:#111827">Raise a Dispute</h1>
    </div>
</div>

<div style="max-width:560px">

    @if($errors->any())
    <div style="margin-bottom:20px;padding:13px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;color:#991b1b;font-size:13px;font-weight:500">
        {{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('disputes.store') }}">
        @csrf

        {{-- Dispute Details --}}
        <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:24px;margin-bottom:16px">
            <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:18px">Dispute Details</p>

            <div style="margin-bottom:16px">
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">
                    Dispute Type <span style="color:#dc2626">*</span>
                </label>
                <select name="type"
                        style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none;background:white" required>
                    <option value="" disabled {{ old('type') ? '' : 'selected' }}>Select a dispute type</option>
                    @foreach(\App\Models\PortalDispute::$types as $key => $label)
                    <option value="{{ $key }}" {{ old('type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom:16px">
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Account</label>
                <select name="account_id"
                        style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none;background:white">
                    <option value="">All accounts</option>
                    @foreach($accounts as $acct)
                    <option value="{{ $acct->id }}" {{ old('account_id') == $acct->id ? 'selected' : '' }}>
                        {{ $acct->account_name }} — {{ $acct->account_number }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom:16px">
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">
                    Related Transaction <span style="font-weight:400;color:#9ca3af">(if applicable)</span>
                </label>
                <select name="transaction_id"
                        style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none;background:white">
                    <option value="">Select a transaction</option>
                    @foreach($recentTxns as $txn)
                    <option value="{{ $txn->id }}" {{ old('transaction_id') == $txn->id ? 'selected' : '' }}>
                        {{ $txn->created_at->format('d M Y') }} — {{ Str::limit($txn->description ?? ucfirst($txn->type), 40) }} — NGN {{ number_format($txn->amount, 2) }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom:16px">
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">
                    Disputed Amount (NGN) <span style="font-weight:400;color:#9ca3af">(optional)</span>
                </label>
                <input type="number" name="disputed_amount" value="{{ old('disputed_amount') }}"
                       min="1" step="0.01" placeholder="Leave blank if unknown"
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none">
            </div>

            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">
                    Describe the Issue <span style="color:#dc2626">*</span>
                </label>
                <textarea name="description" rows="5" minlength="20" maxlength="1000"
                          placeholder="Provide as much detail as possible — date, amount, what happened, what you expected..."
                          style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none;resize:vertical;line-height:1.6" required>{{ old('description') }}</textarea>
                <p style="font-size:11px;color:#9ca3af;margin-top:5px">Minimum 20 characters. The more detail you provide, the faster we can resolve this.</p>
            </div>
        </div>

        {{-- What happens next --}}
        <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;padding:16px 18px;margin-bottom:20px">
            <div style="display:flex;align-items:flex-start;gap:10px">
                <div style="width:28px;height:28px;border-radius:7px;background:#2563eb;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <div>
                    <p style="font-size:12px;font-weight:700;color:#1d4ed8;margin-bottom:5px">What happens next?</p>
                    <p style="font-size:12px;color:#1e40af;line-height:1.7">
                        We will assign a ticket number and begin investigating immediately.
                        Expected resolution time is <strong>5–7 business days</strong>.
                        You will be notified of any status updates via email and in-app notifications.
                    </p>
                </div>
            </div>
        </div>

        <button type="submit"
                style="width:100%;background:#dc2626;color:white;font-size:13px;font-weight:700;padding:13px 20px;border-radius:10px;border:none;cursor:pointer">
            Submit Dispute
        </button>
    </form>
</div>

@endsection
