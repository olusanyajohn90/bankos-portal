@extends('layouts.portal')
@section('title', 'New Split Bill')

@section('content')
<div style="display:flex;align-items:center;gap:12px;margin-bottom:28px">
    <a href="{{ route('split-bills') }}"
       style="display:flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:9px;border:1px solid #e5e7eb;color:#6b7280;text-decoration:none;flex-shrink:0;background:white">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
    </a>
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#111827;margin:0 0 3px">New Split Bill</h1>
        <p style="font-size:13px;color:#6b7280;margin:0">Divide a shared expense among multiple people</p>
    </div>
</div>

@if($errors->any())
<div style="margin-bottom:18px;padding:13px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;color:#991b1b;font-size:13px">
    <ul style="margin:0;padding-left:18px;line-height:1.8">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('split-bills.store') }}" id="splitForm">
@csrf
<div style="display:grid;grid-template-columns:1fr 1fr;gap:18px">

    {{-- Left: Bill Details --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:26px">
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin:0 0 20px">Bill Details</p>

        <div style="margin-bottom:18px">
            <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:7px">
                Receive Payments Into <span style="color:#dc2626">*</span>
            </label>
            <select name="account_id" required
                    style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;background:white;box-sizing:border-box;color:#111827;outline:none">
                @foreach($accounts as $acc)
                <option value="{{ $acc->id }}">{{ $acc->account_name }} — {{ $acc->account_number }}</option>
                @endforeach
            </select>
        </div>

        <div style="margin-bottom:18px">
            <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:7px">
                Bill Title <span style="color:#dc2626">*</span>
            </label>
            <input type="text" name="title" value="{{ old('title') }}" required maxlength="200"
                   placeholder="e.g. Team Lunch, Aso-ebi, Trip Expenses"
                   style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;color:#111827;outline:none">
        </div>

        <div style="margin-bottom:18px">
            <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:7px">Description <span style="font-size:11px;font-weight:400;color:#9ca3af">(optional)</span></label>
            <textarea name="description" rows="2" maxlength="500"
                      style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;resize:vertical;box-sizing:border-box;color:#111827;outline:none">{{ old('description') }}</textarea>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:18px">
            <div>
                <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:7px">
                    Total Amount (NGN) <span style="color:#dc2626">*</span>
                </label>
                <input type="number" name="total_amount" id="totalAmount" value="{{ old('total_amount') }}"
                       required min="100" step="0.01" oninput="calcPerPerson()"
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;color:#111827;outline:none">
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:7px">
                    No. of People <span style="color:#dc2626">*</span>
                </label>
                <input type="number" name="participant_count" id="participantCount" value="{{ old('participant_count', 2) }}"
                       required min="2" max="50" oninput="calcPerPerson(); syncRows()"
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;color:#111827;outline:none">
            </div>
        </div>

        <div id="perPersonBox" style="display:none;background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:14px;margin-bottom:18px">
            <p style="font-size:11px;color:#1d4ed8;margin:0 0 3px;font-weight:600;text-transform:uppercase;letter-spacing:.04em">Per Person</p>
            <p style="font-size:22px;font-weight:800;color:#1e3a8a;margin:0" id="perPersonAmt">NGN 0.00</p>
        </div>

        <div>
            <label style="display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:7px">
                Expires In (days)
            </label>
            <select name="expires_in_days"
                    style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;background:white;color:#111827;outline:none">
                <option value="7">7 days (default)</option>
                <option value="3">3 days</option>
                <option value="14">14 days</option>
                <option value="30">30 days</option>
            </select>
        </div>
    </div>

    {{-- Right: Participants --}}
    <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:26px">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
            <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin:0">Participants</p>
            <span style="font-size:11px;color:#9ca3af">Name required &middot; phone/email optional</span>
        </div>

        <div id="participantRows"></div>

        <button type="button" onclick="addRow()"
                style="width:100%;padding:10px;border:1px dashed #d1d5db;border-radius:9px;background:none;font-size:13px;color:#6b7280;cursor:pointer;margin-top:4px;font-weight:600">
            + Add Participant
        </button>
    </div>
</div>

<div style="margin-top:20px;display:flex;justify-content:flex-end;gap:12px">
    <a href="{{ route('split-bills') }}"
       style="padding:11px 22px;border:1px solid #e5e7eb;border-radius:10px;font-size:13px;font-weight:600;color:#6b7280;text-decoration:none;background:white">
        Cancel
    </a>
    <button type="submit"
            style="background:#2563eb;color:white;font-size:13px;font-weight:700;padding:11px 28px;border-radius:10px;border:none;cursor:pointer">
        Create Split Bill
    </button>
</div>
</form>

<script>
var rowCount = 0;

function calcPerPerson() {
    var total = parseFloat(document.getElementById('totalAmount').value) || 0;
    var people = parseInt(document.getElementById('participantCount').value) || 0;
    var box = document.getElementById('perPersonBox');
    if (total > 0 && people > 0) {
        var per = total / people;
        document.getElementById('perPersonAmt').textContent = 'NGN ' + per.toLocaleString('en-NG', {minimumFractionDigits:2, maximumFractionDigits:2});
        box.style.display = 'block';
    } else {
        box.style.display = 'none';
    }
}

function addRow(name, phone, email) {
    var container = document.getElementById('participantRows');
    var idx = rowCount++;
    var div = document.createElement('div');
    div.id = 'prow-' + idx;
    div.style.cssText = 'border:1px solid #f3f4f6;border-radius:10px;padding:12px 14px;margin-bottom:10px;background:#fafafa';
    div.innerHTML =
        '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">' +
            '<span style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.04em">Participant ' + (idx+1) + '</span>' +
            '<button type="button" onclick="removeRow(' + idx + ')" style="background:none;border:none;color:#dc2626;cursor:pointer;font-size:11px;font-weight:600;padding:0">Remove</button>' +
        '</div>' +
        '<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px">' +
            '<input type="text" name="participants[' + idx + '][name]" placeholder="Full name *" required value="' + (name||'') + '"' +
            ' style="padding:9px 11px;border:1px solid #d1d5db;border-radius:8px;font-size:12px;box-sizing:border-box;color:#111827;outline:none">' +
            '<input type="text" name="participants[' + idx + '][phone]" placeholder="Phone (optional)" value="' + (phone||'') + '"' +
            ' style="padding:9px 11px;border:1px solid #d1d5db;border-radius:8px;font-size:12px;box-sizing:border-box;color:#111827;outline:none">' +
            '<input type="email" name="participants[' + idx + '][email]" placeholder="Email (optional)" value="' + (email||'') + '"' +
            ' style="padding:9px 11px;border:1px solid #d1d5db;border-radius:8px;font-size:12px;box-sizing:border-box;color:#111827;outline:none">' +
        '</div>';
    container.appendChild(div);
}

function removeRow(idx) {
    var el = document.getElementById('prow-' + idx);
    if (el) el.remove();
}

function syncRows() {
    var desired = parseInt(document.getElementById('participantCount').value) || 2;
    var current = document.getElementById('participantRows').children.length;
    while (current < desired) { addRow(); current++; }
}

addRow(); addRow();
</script>
@endsection
