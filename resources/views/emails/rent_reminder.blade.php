<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #0f172a; margin: 0; padding: 0; }
        .container { max-w-xl mx-auto p-8; background-color: #ffffff; margin: 40px auto; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); max-width: 600px; }
        .header { text-align: center; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: bold; color: #0f172a; }
        .logo span { color: #e11d48; }
        .content { font-size: 16px; line-height: 1.6; color: #334155; }
        .invoice-box { background-color: #f1f5f9; padding: 20px; border-radius: 8px; margin: 24px 0; text-align: center; }
        .amount { font-size: 32px; font-weight: bold; color: #0f172a; margin-bottom: 8px; }
        .due-date { font-size: 14px; color: #64748b; font-weight: 600; }
        .due-date.overdue { color: #e11d48; }
        .btn { display: inline-block; background-color: #e11d48; color: #ffffff; text-decoration: none; padding: 14px 28px; border-radius: 8px; font-weight: bold; margin-top: 10px; }
        .footer { text-align: center; margin-top: 40px; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Rent<span>Ease</span></div>
        </div>
        
        <div class="content">
            <p>Hi {{ $invoice->lease->tenant->full_name ?? $invoice->lease->tenant->username }},</p>
            
            @if($type === 'overdue')
                <p>This is a gentle reminder that your rent payment for <strong>{{ $invoice->lease->property->title }}</strong> was due on <strong>{{ $invoice->due_date->format('F j, Y') }}</strong> and is now overdue.</p>
            @else
                <p>This is a quick reminder that your upcoming rent payment for <strong>{{ $invoice->lease->property->title }}</strong> is due on <strong>{{ $invoice->due_date->format('F j, Y') }}</strong>.</p>
            @endif

            <div class="invoice-box">
                <div class="amount">₱{{ number_format($invoice->amount, 0) }}</div>
                <div class="due-date {{ $type === 'overdue' ? 'overdue' : '' }}">
                    {{ $type === 'overdue' ? 'OVERDUE' : 'DUE ON' }} {{ $invoice->due_date->format('M d, Y') }}
                </div>
            </div>

            <p style="text-align: center;">
                <a href="{{ route('tenant.invoices.index') }}" class="btn">Pay Now Securely</a>
            </p>

            <p>If you have already made this payment, please disregard this email or check your dashboard for updates.</p>
            
            <p>Thanks,<br>The RentEase Team</p>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} RentEase. All rights reserved.<br>
            Digos City, Davao del Sur, Philippines
        </div>
    </div>
</body>
</html>
