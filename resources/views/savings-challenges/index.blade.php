@extends('layouts.portal')
@section('title', 'Savings Challenges')

@section('content')

{{-- Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px">
    <div>
        <h1 style="font-size:20px;font-weight:800;color:#111827">Savings Challenges</h1>
        <p style="font-size:12px;color:#9ca3af;margin-top:3px">Build consistent saving habits with goal-based challenges</p>
    </div>
    <button onclick="document.getElementById('create-challenge').scrollIntoView({behavior:'smooth'})"
            style="background:#2563eb;color:white;font-size:13px;font-weight:700;padding:11px 20px;border-radius:10px;border:none;cursor:pointer;display:flex;align-items:center;gap:7px">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Challenge
    </button>
</div>

{{-- Active challenges --}}
@if($challenges->where('status','active')->count() > 0)
<div style="margin-bottom:24px">
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:14px">Active Challenges</p>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
        @foreach($challenges->where('status','active') as $ch)
        @php $pct = $ch->progress_pct; @endphp
        <div style="background:linear-gradient(135deg,#1e40af,#2563eb);border-radius:14px;padding:22px;color:white;position:relative;overflow:hidden">
            <div style="position:absolute;top:-20px;right:-20px;width:90px;height:90px;border-radius:50%;background:rgba(255,255,255,0.07)"></div>
            <div style="position:absolute;bottom:-30px;left:-10px;width:70px;height:70px;border-radius:50%;background:rgba(255,255,255,0.04)"></div>

            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:14px">
                <div>
                    <div style="width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;margin-bottom:10px">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
                    </div>
                    <p style="font-size:15px;font-weight:800;margin-bottom:2px">{{ $ch->name }}</p>
                    <p style="font-size:11px;color:rgba(255,255,255,0.65)">{{ ucfirst($ch->frequency) }} &middot; NGN {{ number_format($ch->amount_per_save,0) }}/save</p>
                </div>
                <div style="text-align:right">
                    <p style="font-size:26px;font-weight:800;line-height:1">{{ $pct }}%</p>
                    <p style="font-size:10px;color:rgba(255,255,255,0.6);margin-top:1px">complete</p>
                    @if($ch->streak_count > 0)
                    <div style="margin-top:6px;display:flex;align-items:center;gap:4px;justify-content:flex-end">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="#fde68a" stroke="#fde68a" stroke-width="1"><path d="M12 2c0 6-8 9-8 15a8 8 0 0 0 16 0c0-6-8-9-8-15z"/></svg>
                        <span style="font-size:11px;color:#fde68a;font-weight:700">{{ $ch->streak_count }} streak</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Progress bar --}}
            <div style="height:7px;background:rgba(255,255,255,0.2);border-radius:7px;overflow:hidden;margin-bottom:8px">
                <div style="height:100%;width:{{ $pct }}%;background:rgba(255,255,255,0.9);border-radius:7px;transition:width .5s"></div>
            </div>
            <div style="display:flex;justify-content:space-between;margin-bottom:16px">
                <span style="font-size:11px;color:rgba(255,255,255,0.65)">NGN {{ number_format($ch->current_amount,0) }}</span>
                <span style="font-size:11px;color:rgba(255,255,255,0.65)">NGN {{ number_format($ch->target_amount,0) }}</span>
            </div>

            <div style="display:flex;gap:8px">
                <form method="POST" action="{{ route('savings-challenges.save', $ch->id) }}" style="flex:1">
                    @csrf
                    <button type="submit" style="width:100%;background:rgba(255,255,255,0.18);color:white;border:1px solid rgba(255,255,255,0.3);padding:9px;border-radius:9px;font-size:12px;font-weight:700;cursor:pointer">
                        + Save NGN {{ number_format($ch->amount_per_save,0) }}
                    </button>
                </form>
                <form method="POST" action="{{ route('savings-challenges.pause', $ch->id) }}">
                    @csrf
                    <button type="submit" title="Pause challenge"
                            style="background:rgba(255,255,255,0.1);color:rgba(255,255,255,0.8);border:1px solid rgba(255,255,255,0.2);padding:9px 12px;border-radius:9px;font-size:12px;cursor:pointer;display:flex;align-items:center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Paused challenges --}}
@if($challenges->where('status','paused')->count() > 0)
<div style="margin-bottom:24px">
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px">Paused</p>
    <div style="display:flex;flex-direction:column;gap:8px">
        @foreach($challenges->where('status','paused') as $ch)
        <div style="background:white;border:2px dashed #e5e7eb;border-radius:12px;padding:16px;display:flex;justify-content:space-between;align-items:center">
            <div style="display:flex;align-items:center;gap:12px">
                <div style="width:38px;height:38px;border-radius:10px;background:#f3f4f6;display:flex;align-items:center;justify-content:center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>
                </div>
                <div>
                    <p style="font-size:13px;font-weight:700;color:#374151">{{ $ch->name }}</p>
                    <p style="font-size:11px;color:#9ca3af">NGN {{ number_format($ch->current_amount,0) }} of NGN {{ number_format($ch->target_amount,0) }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('savings-challenges.resume', $ch->id) }}">
                @csrf
                <button type="submit" style="background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;padding:8px 16px;border-radius:9px;font-size:12px;font-weight:700;cursor:pointer">Resume</button>
            </form>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Completed --}}
@if($challenges->where('status','completed')->count() > 0)
<div style="margin-bottom:24px">
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:12px">Completed</p>
    <div style="display:flex;flex-direction:column;gap:8px">
        @foreach($challenges->where('status','completed') as $ch)
        <div style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);border:1px solid #bbf7d0;border-radius:12px;padding:16px;display:flex;justify-content:space-between;align-items:center">
            <div style="display:flex;align-items:center;gap:12px">
                <div style="width:38px;height:38px;border-radius:10px;background:#16a34a;display:flex;align-items:center;justify-content:center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div>
                    <p style="font-size:13px;font-weight:700;color:#166534">{{ $ch->name }}</p>
                    <p style="font-size:11px;color:#16a34a">NGN {{ number_format($ch->target_amount,0) }} saved &middot; {{ $ch->total_saves }} saves &middot; {{ $ch->completed_at?->format('d M Y') }}</p>
                </div>
            </div>
            <div style="width:38px;height:38px;border-radius:10px;background:#16a34a;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Create new challenge --}}
<div id="create-challenge" style="background:white;border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,0.06);padding:26px">
    <p style="font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:.06em;margin-bottom:18px">Start a New Challenge</p>

    {{-- Template grid --}}
    <p style="font-size:12px;color:#9ca3af;margin-bottom:10px;font-weight:600">Quick templates:</p>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:20px">
        @foreach($templates as $t)
        <button type="button" onclick="applyTemplate('{{ addslashes($t['name']) }}')"
                style="padding:12px 8px;border:1px solid #e5e7eb;border-radius:10px;background:white;cursor:pointer;text-align:center;transition:border-color .15s"
                onmouseover="this.style.borderColor='#2563eb';this.style.background='#eff6ff'" onmouseout="this.style.borderColor='#e5e7eb';this.style.background='white'">
            <div style="width:28px;height:28px;border-radius:8px;background:#eff6ff;display:flex;align-items:center;justify-content:center;margin:0 auto 6px">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
            </div>
            <span style="font-size:11px;font-weight:700;color:#374151">{{ $t['name'] }}</span>
        </button>
        @endforeach
    </div>

    <form method="POST" action="{{ route('savings-challenges.store') }}">
        @csrf
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px">
            <div style="grid-column:1/-1">
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Challenge Name <span style="color:#dc2626">*</span></label>
                <input type="text" name="name" id="ch-name" placeholder="e.g. Vacation Fund, Emergency Savings" required
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none"
                       onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#d1d5db'">
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Target Amount (NGN) <span style="color:#dc2626">*</span></label>
                <input type="number" name="target_amount" min="1000" step="500" placeholder="e.g. 100000" required
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none"
                       onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#d1d5db'">
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Amount Per Save (NGN) <span style="color:#dc2626">*</span></label>
                <input type="number" name="amount_per_save" min="100" step="100" placeholder="e.g. 500" required
                       style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none"
                       onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#d1d5db'">
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Frequency <span style="color:#dc2626">*</span></label>
                <select name="frequency" required
                        style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none;background:white"
                        onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#d1d5db'">
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly" selected>Monthly</option>
                </select>
            </div>
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Debit Account <span style="color:#dc2626">*</span></label>
                <select name="account_id" required
                        style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none;background:white"
                        onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#d1d5db'">
                    @foreach($accounts as $a)
                    <option value="{{ $a->id }}">{{ $a->account_number }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @if($pockets->isNotEmpty())
        <div style="margin-bottom:16px">
            <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:6px">Destination Pocket (optional)</label>
            <select name="pocket_id"
                    style="width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:9px;font-size:13px;box-sizing:border-box;outline:none;background:white"
                    onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#d1d5db'">
                <option value="">— Don't link to pocket —</option>
                @foreach($pockets as $p)
                <option value="{{ $p->id }}">{{ $p->name }} (NGN {{ number_format($p->balance,0) }})</option>
                @endforeach
            </select>
        </div>
        @endif

        <button type="submit"
                style="width:100%;background:#2563eb;color:white;font-size:13px;font-weight:700;padding:12px 20px;border-radius:10px;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px"
                onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
            Start Challenge
        </button>
    </form>
</div>

<script>
function applyTemplate(name) {
    document.getElementById('ch-name').value = name;
    document.getElementById('ch-name').focus();
}
</script>
@endsection
