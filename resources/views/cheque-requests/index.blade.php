@extends('layouts.portal')
@section('title', 'Chequebook Request')

@section('content')

{{-- Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px">
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#111827">Chequebook Request</h1>
        <p style="font-size:12px;color:#9ca3af;margin-top:3px">Request a new chequebook for your current account</p>
    </div>
</div>

@foreach(['success','error'] as $k)
@if(session($k))
<div style="margin-bottom:16px;padding:12px 16px;background:{{ $k==='success'?'#f0fdf4':'#fef2f2' }};border:1px solid {{ $k==='success'?'#bbf7d0':'#fecaca' }};border-radius:10px;color:{{ $k==='success'?'#15803d':'#991b1b' }};font-size:13px">{{ session($k) }}</div>
@endif
@endforeach
@if($errors->any())
<div style="margin-bottom:16px;padding:12px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;color:#991b1b;font-size:13px">{{ $errors->first() }}</div>
@endif

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;align-items:start">

    {{-- Request form --}}
    <div>
        @if($accounts->isEmpty())
        <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:14px;padding:28px;text-align:center">
            <div style="width:50px;height:50px;border-radius:50%;background:#fef3c7;display:flex;align-items:center;justify-content:center;margin:0 auto 14px">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <p style="font-size:14px;font-weight:700;color:#92400e;margin-bottom:4px">No Current Account</p>
            <p style="font-size:12px;color:#b45309;margin-bottom:14px;line-height:1.5">Chequebooks are only available for Current accounts.</p>
            <a href="{{ route('account-opening') }}" style="font-size:13px;font-weight:700;color:#2563eb;text-decoration:none">Open a Current Account &rarr;</a>
        </div>
        @else
        <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:24px">
            <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:20px">New Request</p>

            <form method="POST" action="{{ route('cheque-requests.store') }}" id="cheque-form">
                @csrf

                {{-- Account --}}
                <div style="margin-bottom:16px">
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:7px">Account <span style="color:#dc2626">*</span></label>
                    <select name="account_id" required
                            style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none;background:white"
                            onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#d1d5db'">
                        @foreach($accounts as $a)
                        <option value="{{ $a->id }}">{{ $a->account_name }} &middot; {{ $a->account_number }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Number of leaves --}}
                <div style="margin-bottom:16px">
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:10px">Number of Leaves <span style="color:#dc2626">*</span></label>
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px">
                        @foreach(['25_leaves'=>'25','50_leaves'=>'50','100_leaves'=>'100'] as $val=>$num)
                        <label style="cursor:pointer">
                            <input type="radio" name="book_type" value="{{ $val }}" style="display:none" {{ $val==='50_leaves'?'checked':'' }} onchange="styleBookType()">
                            <div class="book-opt" data-val="{{ $val }}"
                                 style="text-align:center;padding:14px 8px;border:2px solid {{ $val==='50_leaves'?'#2563eb':'#e5e7eb' }};border-radius:10px;background:{{ $val==='50_leaves'?'#eff6ff':'white' }};transition:all .15s">
                                <div style="width:32px;height:32px;border-radius:8px;background:{{ $val==='50_leaves'?'#bfdbfe':'#f3f4f6' }};display:flex;align-items:center;justify-content:center;margin:0 auto 8px">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="{{ $val==='50_leaves'?'#2563eb':'#9ca3af' }}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                </div>
                                <p style="font-size:13px;font-weight:800;color:{{ $val==='50_leaves'?'#1d4ed8':'#374151' }}">{{ $num }}</p>
                                <p style="font-size:10px;color:{{ $val==='50_leaves'?'#3b82f6':'#9ca3af' }}">Leaves</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Collection method --}}
                <div style="margin-bottom:16px">
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:10px">Collection Method <span style="color:#dc2626">*</span></label>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
                        <label style="cursor:pointer">
                            <input type="radio" name="collection_method" value="branch_pickup" checked style="display:none" onchange="toggleCollection('branch')">
                            <div id="opt-branch"
                                 style="text-align:center;padding:14px;border:2px solid #2563eb;border-radius:10px;background:#eff6ff">
                                <div style="width:32px;height:32px;border-radius:8px;background:#bfdbfe;display:flex;align-items:center;justify-content:center;margin:0 auto 8px">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                                </div>
                                <p style="font-size:12px;font-weight:700;color:#1d4ed8">Branch Pickup</p>
                            </div>
                        </label>
                        <label style="cursor:pointer">
                            <input type="radio" name="collection_method" value="home_delivery" style="display:none" onchange="toggleCollection('home')">
                            <div id="opt-home"
                                 style="text-align:center;padding:14px;border:2px solid #e5e7eb;border-radius:10px;background:white">
                                <div style="width:32px;height:32px;border-radius:8px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;margin:0 auto 8px">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                </div>
                                <p style="font-size:12px;font-weight:700;color:#374151">Home Delivery</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Branch name --}}
                <div id="branch-field" style="margin-bottom:16px">
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:7px">Preferred Branch</label>
                    <input type="text" name="branch_name" placeholder="e.g. Victoria Island Branch"
                           style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none"
                           onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#d1d5db'">
                </div>

                {{-- Delivery address --}}
                <div id="address-field" style="display:none;margin-bottom:16px">
                    <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:7px">Delivery Address</label>
                    <textarea name="delivery_address" rows="2" placeholder="Full delivery address..."
                              style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none;resize:vertical"
                              onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#d1d5db'"></textarea>
                </div>

                <button type="submit"
                        style="width:100%;background:#2563eb;color:white;font-size:13px;font-weight:700;padding:12px 20px;border-radius:10px;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px"
                        onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    Submit Request
                </button>
            </form>
        </div>
        @endif
    </div>

    {{-- Request history --}}
    <div>
        <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:14px">Request History</p>

        @if($requests->isEmpty())
        <div style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:48px 24px;text-align:center">
            <div style="width:50px;height:50px;border-radius:50%;background:#f3f4f6;display:flex;align-items:center;justify-content:center;margin:0 auto 14px">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            </div>
            <p style="font-size:13px;font-weight:600;color:#374151;margin-bottom:4px">No requests yet</p>
            <p style="font-size:12px;color:#9ca3af">Your chequebook requests will appear here</p>
        </div>
        @else
        <div style="display:flex;flex-direction:column;gap:10px">
            @foreach($requests as $req)
            @php $sc = \App\Models\PortalChequeRequest::$statusColors[$req->status] ?? ['#6b7280','#f9fafb']; @endphp
            <div style="background:white;border:1px solid #e5e7eb;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:16px">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px">
                    <div>
                        <p style="font-size:13px;font-weight:700;color:#111827">{{ str_replace('_',' ',ucwords($req->book_type,'_')) }}</p>
                        <p style="font-size:11px;color:#9ca3af;margin-top:2px;font-family:monospace">{{ $req->reference }}</p>
                        <p style="font-size:11px;color:#9ca3af;margin-top:1px">{{ ucfirst(str_replace('_',' ',$req->collection_method)) }}{{ $req->branch_name ? ' &middot; '.$req->branch_name : '' }}</p>
                    </div>
                    <span style="font-size:10px;font-weight:700;padding:3px 10px;border-radius:20px;background:{{ $sc[1] }};color:{{ $sc[0] }}">{{ strtoupper($req->status) }}</span>
                </div>

                @if($req->status === 'ready')
                <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 12px;margin-top:8px;display:flex;align-items:center;gap:8px">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <p style="font-size:12px;font-weight:600;color:#16a34a">Ready for collection! {{ $req->admin_notes }}</p>
                </div>
                @endif

                @if($req->status === 'pending')
                <form method="POST" action="{{ route('cheque-requests.destroy', $req->id) }}" style="margin-top:10px" onsubmit="return confirm('Cancel this chequebook request?')">
                    @csrf @method('DELETE')
                    <button type="submit" style="font-size:12px;color:#dc2626;background:none;border:1px solid #fecaca;border-radius:7px;cursor:pointer;padding:5px 12px">Cancel Request</button>
                </form>
                @endif

                <p style="font-size:10px;color:#d1d5db;margin-top:8px">{{ $req->created_at->format('d M Y') }}</p>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

<script>
function toggleCollection(type) {
    const branch    = document.getElementById('branch-field');
    const address   = document.getElementById('address-field');
    const optBranch = document.getElementById('opt-branch');
    const optHome   = document.getElementById('opt-home');
    if (type === 'home') {
        branch.style.display = 'none'; address.style.display = 'block';
        optBranch.style.borderColor = '#e5e7eb'; optBranch.style.background = 'white';
        optHome.style.borderColor   = '#2563eb'; optHome.style.background   = '#eff6ff';
        optHome.querySelector('svg').style.stroke = '#2563eb';
        optHome.querySelector('p').style.color = '#1d4ed8';
        optHome.querySelector('div').style.background = '#bfdbfe';
    } else {
        branch.style.display = 'block'; address.style.display = 'none';
        optHome.style.borderColor   = '#e5e7eb'; optHome.style.background   = 'white';
        optBranch.style.borderColor = '#2563eb'; optBranch.style.background = '#eff6ff';
    }
}
function styleBookType() {
    document.querySelectorAll('.book-opt').forEach(function(el) {
        const radio   = document.querySelector('input[value="' + el.dataset.val + '"]');
        const active  = radio.checked;
        el.style.borderColor = active ? '#2563eb' : '#e5e7eb';
        el.style.background  = active ? '#eff6ff' : 'white';
        const iconDiv = el.querySelector('div');
        if (iconDiv) {
            iconDiv.style.background = active ? '#bfdbfe' : '#f3f4f6';
            const svg = iconDiv.querySelector('svg');
            if (svg) svg.style.stroke = active ? '#2563eb' : '#9ca3af';
        }
        const numP = el.querySelectorAll('p')[0];
        const lblP = el.querySelectorAll('p')[1];
        if (numP) numP.style.color = active ? '#1d4ed8' : '#374151';
        if (lblP) lblP.style.color = active ? '#3b82f6' : '#9ca3af';
    });
}
</script>
@endsection
