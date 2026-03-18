@extends('layouts.portal')
@section('title', 'Payment Requests')

@section('content')

{{-- Page Header --}}
<div style="margin-bottom:28px">
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">Payments</p>
    <h1 style="font-size:20px;font-weight:800;color:#111827;margin-bottom:4px">Payment Requests</h1>
    <p style="font-size:13px;color:#6b7280">Request money from anyone — share a link, get paid instantly</p>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;align-items:start">

    {{-- Create Request --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:26px">
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px">New Request</p>
        <p style="font-size:15px;font-weight:800;color:#111827;margin-bottom:20px">Create Payment Request</p>

        @if($errors->any())
        <div style="margin-bottom:16px;padding:12px 14px;background:#fef2f2;border:1px solid #fecaca;border-radius:9px;color:#991b1b;font-size:13px;font-weight:500">
            {{ $errors->first() }}
        </div>
        @endif
        @if(session('success'))
        <div style="margin-bottom:16px;padding:12px 14px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:9px;color:#15803d;font-size:13px;font-weight:500">
            {{ session('success') }}
        </div>
        @endif

        <form method="POST" action="{{ route('pay-requests.store') }}">
            @csrf

            <div style="margin-bottom:14px">
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">
                    Receive to Account <span style="color:#dc2626">*</span>
                </label>
                <select name="account_id"
                        style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none;background:white" required>
                    @foreach($accounts as $acct)
                    <option value="{{ $acct->id }}">{{ $acct->account_name }} — {{ $acct->account_number }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom:14px">
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">
                    Amount (NGN) <span style="color:#dc2626">*</span>
                </label>
                <input type="number" name="amount" value="{{ old('amount') }}"
                       min="100" step="50" placeholder="e.g. 5000"
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none" required>
            </div>

            <div style="margin-bottom:14px">
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">
                    Description <span style="font-weight:400;color:#9ca3af">(optional)</span>
                </label>
                <input type="text" name="description" value="{{ old('description') }}"
                       placeholder='e.g. For dinner last night'
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none">
            </div>

            <div style="margin-bottom:8px">
                <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px;padding-top:4px">
                    Send request to <span style="font-weight:400;color:#9ca3af;text-transform:none;letter-spacing:0">(optional)</span>
                </p>
                <div style="margin-bottom:10px">
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Recipient Name</label>
                    <input type="text" name="recipient_name" value="{{ old('recipient_name') }}"
                           placeholder="Recipient name"
                           style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none">
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Recipient Email</label>
                    <input type="email" name="recipient_email" value="{{ old('recipient_email') }}"
                           placeholder="recipient@email.com"
                           style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none">
                </div>
            </div>

            <div style="margin-top:20px">
                <button type="submit"
                        style="width:100%;background:#2563eb;color:white;font-size:13px;font-weight:700;padding:12px 20px;border-radius:10px;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Create Payment Request
                </button>
            </div>
        </form>
    </div>

    {{-- My Requests --}}
    <div>
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:14px">My Requests</p>

        @if($requests->isEmpty())
        <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:48px 24px;text-align:center">
            <div style="width:48px;height:48px;border-radius:12px;background:#f3f4f6;display:inline-flex;align-items:center;justify-content:center;margin-bottom:14px">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.8"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <p style="font-size:13px;font-weight:600;color:#374151;margin-bottom:4px">No payment requests yet</p>
            <p style="font-size:12px;color:#9ca3af">Create a request and share the link to receive payment.</p>
        </div>

        @else
        <div style="display:flex;flex-direction:column;gap:10px">
            @foreach($requests as $req)
            @php
                $sc = [
                    'pending'   => ['#d97706','#fef9c3','#854d0e'],
                    'paid'      => ['#16a34a','#f0fdf4','#15803d'],
                    'expired'   => ['#6b7280','#f3f4f6','#4b5563'],
                    'cancelled' => ['#6b7280','#f3f4f6','#4b5563'],
                ][$req->status] ?? ['#6b7280','#f3f4f6','#4b5563'];
                $payUrl = route('pay-request.public', $req->reference);
            @endphp
            <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:16px 18px">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;gap:10px">
                    <div style="min-width:0">
                        <p style="font-size:18px;font-weight:800;color:#111827">NGN {{ number_format($req->amount, 2) }}</p>
                        @if($req->description)
                        <p style="font-size:12px;color:#6b7280;margin-top:2px">{{ $req->description }}</p>
                        @endif
                        <p style="font-size:11px;color:#9ca3af;margin-top:4px">
                            {{ $req->created_at->format('d M Y') }}
                            @if($req->recipient_name) · For {{ $req->recipient_name }}@endif
                            · Expires {{ $req->expires_at?->format('d M Y') ?? 'Never' }}
                        </p>
                    </div>
                    <span style="display:inline-block;font-size:10px;font-weight:700;padding:4px 10px;border-radius:99px;background:{{ $sc[1] }};color:{{ $sc[2] }};letter-spacing:.04em;text-transform:uppercase;flex-shrink:0;margin-top:2px">
                        {{ ucfirst($req->status) }}
                    </span>
                </div>

                @if($req->status === 'pending')
                <div style="padding-top:12px;border-top:1px solid #f3f4f6">
                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                        <div style="background:#f8fafc;border:1px solid #e5e7eb;border-radius:7px;padding:7px 10px;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:11px;color:#6b7280;font-family:monospace">
                            {{ $payUrl }}
                        </div>
                        <button onclick="navigator.clipboard.writeText('{{ $payUrl }}');this.textContent='Copied';setTimeout(()=>this.textContent='Copy Link',2000)"
                                style="font-size:12px;font-weight:700;color:#2563eb;background:#eff6ff;border:1px solid #bfdbfe;padding:7px 12px;border-radius:7px;cursor:pointer;white-space:nowrap">
                            Copy Link
                        </button>
                        <form method="POST" action="{{ route('pay-requests.destroy', $req->id) }}">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    style="font-size:12px;font-weight:700;color:#dc2626;background:none;border:none;cursor:pointer;padding:0">
                                Cancel
                            </button>
                        </form>
                    </div>
                </div>
                @elseif($req->status === 'paid')
                <div style="padding-top:10px;border-top:1px solid #f3f4f6">
                    <p style="font-size:12px;color:#15803d;font-weight:600">
                        Paid by {{ $req->paid_by_name ?? 'Anonymous' }} · {{ $req->paid_at?->format('d M Y, H:i') }}
                    </p>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>

@endsection
