<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body {
    font-family: 'DejaVu Sans', sans-serif;
    font-size: 11.5px;
    color: #1a1a2e;
    background: #fff;
    padding: 0;
}
p { font-size:11.5px; line-height:1.95; color:#374151; margin-bottom:13px; }
strong { color:#0c2461; }

.page-footer {
    position:fixed; bottom:0; left:0; right:0;
    background:#0c2461;
    padding:7px 28px;
    font-size:7.5px;
    color:#bfdbfe;
}
.pf-td { font-size:7.5px; color:#bfdbfe; vertical-align:middle; }

.info-label { padding:10px 14px; font-size:11px; color:#6b7280; width:35%; border-bottom:1px solid #f3f4f6; vertical-align:middle; }
.info-value { padding:10px 14px; font-size:11px; font-weight:700; color:#0c2461; border-bottom:1px solid #f3f4f6; vertical-align:middle; }
.status-ok  { color:#15803d; }
</style>
</head>
<body>

{{-- ======= FIXED PAGE FOOTER ======= --}}
<table class="page-footer" width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td class="pf-td" style="text-align:left">
            @if($tenant?->contact_email){{ $tenant->contact_email }}@if($tenant?->contact_phone)&nbsp; | &nbsp;{{ $tenant->contact_phone }}@endif@endif
        </td>
        <td class="pf-td" style="text-align:center">
            @if($tenant?->cbn_license_number)CBN License No: {{ $tenant->cbn_license_number }}@endif
        </td>
        <td class="pf-td" style="text-align:right">
            Ref: {{ $reference }} &nbsp;|&nbsp; {{ now()->format('d M Y') }}
        </td>
    </tr>
</table>

{{-- ======= CONTENT WRAPPER (bottom padding clears fixed footer) ======= --}}
<div style="padding-bottom:55px">

    {{-- ======= HEADER BAND ======= --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#0c2461">
        <tr>
            <td style="padding:22px 30px 18px; vertical-align:bottom">
                <div style="font-size:26px; font-weight:700; color:#ffffff; letter-spacing:0.3px">
                    {{ $tenant?->name ?? config('app.name') }}
                </div>
                @if($tenant?->tagline ?? false)
                <div style="font-size:9px; color:#93c5fd; letter-spacing:2px; text-transform:uppercase; margin-top:2px">
                    {{ $tenant->tagline }}
                </div>
                @endif
                <div style="font-size:9px; color:#bfdbfe; margin-top:7px; line-height:1.9">
                    @if($tenant?->address ?? false){{ $tenant->address }}<br>@endif
                    @if($tenant?->contact_phone ?? false)Tel: {{ $tenant->contact_phone }}&nbsp;&nbsp;@endif
                    @if($tenant?->contact_email ?? false){{ $tenant->contact_email }}@endif
                </div>
            </td>
            <td style="padding:22px 30px 18px; vertical-align:top; text-align:right; width:160px">
                @if($tenant?->cbn_license_number ?? false)
                <table cellpadding="0" cellspacing="0" style="margin-left:auto; border:1px solid #3b82f6; border-radius:3px">
                    <tr>
                        <td style="padding:3px 10px; font-size:7.5px; color:#93c5fd; letter-spacing:1px; white-space:nowrap">
                            CBN LICENSED
                        </td>
                    </tr>
                </table>
                @endif
            </td>
        </tr>
    </table>

    {{-- Gold accent --}}
    <div style="height:3px; background:#c9a84c"></div>

    {{-- ======= DOCUMENT META (Ref + Date) ======= --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="padding:14px 30px; border-bottom:1px solid #e9ecef">
        <tr>
            <td style="vertical-align:middle; font-size:11px; color:#6b7280">
                {{ now()->format('d F Y') }}
            </td>
            <td style="vertical-align:middle; text-align:right">
                <table cellpadding="0" cellspacing="0" style="margin-left:auto; border:1px solid #dee2e6; border-radius:4px; background:#f8f9fc">
                    <tr>
                        <td style="padding:7px 16px">
                            <div style="font-size:8px; color:#9ca3af; text-transform:uppercase; letter-spacing:1px">Document Reference</div>
                            <div style="font-size:13px; font-weight:700; color:#0c2461; font-family:'DejaVu Sans Mono',monospace; letter-spacing:0.5px">{{ $reference }}</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ======= MAIN CONTENT ======= --}}
    <div style="padding:24px 30px 0">

        {{-- Document Title --}}
        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px">
            <tr>
                <td style="text-align:center; padding-bottom:8px; border-bottom:2px solid #0c2461">
                    <span style="font-size:14px; font-weight:700; color:#0c2461; text-transform:uppercase; letter-spacing:3px">
                        Loan Clearance Letter
                    </span>
                </td>
            </tr>
            <tr>
                <td>
                    <table width="60%" cellpadding="0" cellspacing="0" style="margin:0 auto">
                        <tr><td style="height:2px; background:#c9a84c"></td></tr>
                    </table>
                </td>
            </tr>
        </table>

        <p>To Whom It May Concern,</p>

        {{-- Green clearance box --}}
        <table width="100%" cellpadding="0" cellspacing="0"
               style="margin:18px 0; border:1.5px solid #16a34a; border-radius:6px; background:#f0fdf4">
            <tr>
                <td style="width:56px; text-align:center; vertical-align:middle; padding:18px 10px">
                    <table cellpadding="0" cellspacing="0" style="margin:0 auto; width:38px; height:38px; background:#16a34a; border-radius:19px">
                        <tr>
                            <td style="text-align:center; vertical-align:middle; font-size:15px; font-weight:900; color:#fff; line-height:1">
                                OK
                            </td>
                        </tr>
                    </table>
                </td>
                <td style="padding:18px 14px 18px 0; vertical-align:middle">
                    <div style="font-size:13px; font-weight:700; color:#15803d; margin-bottom:3px">
                        LOAN CLEARANCE CONFIRMED
                    </div>
                    <div style="font-size:10px; color:#166534">
                        No outstanding loan obligations as at {{ now()->format('d F Y') }}
                    </div>
                </td>
            </tr>
        </table>

        <p>
            This is to certify that <strong>{{ $customer->first_name }} {{ $customer->last_name }}</strong>
            (Account Number: <strong>{{ $account->account_number }}</strong>) has no outstanding loan obligations
            with <strong>{{ $tenant?->name ?? config('app.name') }}</strong> as at the date of this letter.
            All prior loan obligations have been fully settled and the account is clear of any indebtedness.
        </p>

        {{-- Info table --}}
        <table width="100%" cellpadding="0" cellspacing="0"
               style="margin:18px 0; border:1px solid #dee2e6; border-radius:5px">
            <tr>
                <td colspan="2" style="background:#0c2461; padding:9px 14px; border-radius:4px 4px 0 0">
                    <span style="font-size:8.5px; font-weight:700; color:#bfdbfe; text-transform:uppercase; letter-spacing:1.5px">
                        Customer &amp; Clearance Details
                    </span>
                </td>
            </tr>
            <tr style="background:#f8f9fc">
                <td class="info-label">Customer Name</td>
                <td class="info-value">{{ $customer->first_name }} {{ $customer->last_name }}</td>
            </tr>
            <tr style="background:#ffffff">
                <td class="info-label">Account Number</td>
                <td class="info-value" style="font-family:'DejaVu Sans Mono',monospace">{{ $account->account_number }}</td>
            </tr>
            <tr style="background:#f8f9fc">
                <td class="info-label">Date of Issue</td>
                <td class="info-value">{{ now()->format('d F Y') }}</td>
            </tr>
            <tr style="background:#ffffff">
                <td class="info-label">Document Reference</td>
                <td class="info-value" style="font-family:'DejaVu Sans Mono',monospace">{{ $reference }}</td>
            </tr>
            <tr style="background:#f8f9fc">
                <td class="info-label">Loan Status</td>
                <td class="info-value status-ok">NO OUTSTANDING OBLIGATIONS</td>
            </tr>
            <tr style="background:#ffffff">
                <td class="info-label" style="border-bottom:none">Issued By</td>
                <td class="info-value" style="border-bottom:none">{{ $tenant?->name ?? config('app.name') }}</td>
            </tr>
        </table>

        <p>
            This letter is issued in good faith and based on the records available at the time of issuance.
            {{ $tenant?->name ?? config('app.name') }} bears no liability for any reliance placed on this
            letter after the date of issue. Any changes in the customer's financial standing after this date
            will not be reflected herein.
        </p>

        <p>
            For verification or further enquiries, please contact
            @if($tenant?->contact_email ?? false)<strong>{{ $tenant->contact_email }}</strong>@endif
            @if($tenant?->contact_phone ?? false) or call <strong>{{ $tenant->contact_phone }}</strong>@endif.
        </p>

        {{-- ======= SIGNATURE + STAMP ======= --}}
        <div style="margin-top:44px; padding-top:20px; border-top:1px solid #e5e7eb; overflow:hidden">

            {{-- Stamp (float right) --}}
            <div style="float:right; margin-left:16px; margin-top:4px">
                <table cellpadding="0" cellspacing="0"
                       style="width:94px; height:94px; border:2.5px solid #16a34a; border-radius:47px; text-align:center">
                    <tr>
                        <td style="text-align:center; vertical-align:middle">
                            <table cellpadding="0" cellspacing="0"
                                   style="width:78px; height:78px; border:1px dashed #16a34a; border-radius:39px; margin:0 auto">
                                <tr>
                                    <td style="text-align:center; vertical-align:middle; padding:6px">
                                        <div style="font-size:7px; font-weight:700; text-transform:uppercase; color:#15803d; line-height:1.8; letter-spacing:0.5px">
                                            LOAN<br>CLEARED<br>
                                            <span style="font-size:11px; font-weight:700">{{ now()->format('Y') }}</span><br>
                                            VERIFIED
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>

            {{-- Signature --}}
            <div style="padding-top:26px">
                <div style="border-top:1.5px solid #0c2461; width:210px; margin-bottom:8px; padding-top:2px"></div>
                <div style="font-size:11.5px; font-weight:700; color:#0c2461">Authorised Signatory</div>
                <div style="font-size:10.5px; color:#374151; margin-top:3px; font-weight:600">{{ $tenant?->name ?? config('app.name') }}</div>
                <div style="font-size:10px; color:#9ca3af; margin-top:2px">{{ now()->format('d F Y') }}</div>
            </div>
        </div>

        {{-- Document validity note --}}
        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:28px; border-top:1px solid #f3f4f6; padding-top:12px">
            <tr>
                <td style="padding-top:12px; font-size:9px; color:#9ca3af; line-height:1.7; text-align:center">
                    This is a computer-generated document and is valid without a physical signature unless otherwise stated.
                    Unauthorised alteration of this document is a criminal offence.
                </td>
            </tr>
        </table>

    </div>{{-- /main content --}}
</div>{{-- /content-wrapper --}}

</body>
</html>
