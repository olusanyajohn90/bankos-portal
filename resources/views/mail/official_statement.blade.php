<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; color: #111; margin: 0; padding: 0; background: #f9fafb; }
        .wrapper { max-width: 560px; margin: 32px auto; background: #fff; border-radius: 8px; overflow: hidden; border: 1px solid #e5e7eb; }
        .header { background: linear-gradient(135deg, #1e3a8a, #2563eb); padding: 28px 32px; color: white; }
        .header h2 { margin: 0 0 4px; font-size: 20px; }
        .header p { margin: 0; font-size: 12px; color: rgba(255,255,255,0.75); }
        .body { padding: 28px 32px; }
        .body p { margin: 0 0 14px; line-height: 1.6; color: #374151; }
        .info-box { background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px 20px; margin: 20px 0; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 13px; }
        .info-row:last-child { margin-bottom: 0; }
        .info-key { color: #6b7280; }
        .info-val { font-weight: 600; color: #111827; }
        .note { font-size: 12px; color: #9ca3af; margin-top: 20px; padding-top: 20px; border-top: 1px solid #f3f4f6; }
        .footer { background: #f9fafb; padding: 16px 32px; text-align: center; font-size: 11px; color: #9ca3af; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h2>{{ $account->tenant?->name ?? config('app.name') }}</h2>
        <p>Official Account Statement</p>
    </div>
    <div class="body">
        <p>Dear <strong>{{ $customer->first_name }} {{ $customer->last_name }}</strong>,</p>
        <p>
            Your official account statement has been generated and is attached to this email as a PDF document.
            A statement fee of <strong>NGN {{ number_format($fee, 2) }}</strong> has been deducted from your account.
        </p>

        <div class="info-box">
            <div class="info-row">
                <span class="info-key">Statement Reference</span>
                <span class="info-val">{{ $reference }}</span>
            </div>
            <div class="info-row">
                <span class="info-key">Account Name</span>
                <span class="info-val">{{ $account->account_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-key">Account Number</span>
                <span class="info-val" style="font-family:monospace">{{ $account->account_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-key">Generated On</span>
                <span class="info-val">{{ now()->format('d F Y, H:i') }}</span>
            </div>
        </div>

        <p>
            Please find your official bank statement attached as <strong>Statement-{{ $reference }}.pdf</strong>.
            This document is computer-generated and bears the bank's official reference for verification purposes.
        </p>

        <p class="note">
            If you did not request this statement or believe this is an error, please contact your bank immediately and quote reference <strong>{{ $reference }}</strong>.
        </p>
    </div>
    <div class="footer">
        &copy; {{ now()->year }} {{ $account->tenant?->name ?? config('app.name') }} &mdash; This is an automated message, please do not reply.
    </div>
</div>
</body>
</html>
