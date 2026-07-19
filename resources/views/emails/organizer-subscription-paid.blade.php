<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1a1235; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 40px; border: 1px solid #f0f0f0; border-radius: 20px; }
        .header { text-align: center; margin-bottom: 40px; }
        .logo { font-size: 24px; font-weight: 900; color: #6366f1; text-transform: uppercase; }
        .content { margin-bottom: 40px; }
        .highlight { color: #6366f1; font-weight: bold; }
        .button { display: inline-block; padding: 15px 30px; background-color: #6366f1; color: #ffffff !important; text-decoration: none; border-radius: 12px; font-weight: bold; text-transform: uppercase; font-size: 12px; letter-spacing: 1px; }
        .footer { text-align: center; font-size: 12px; color: #a0aec0; margin-top: 40px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{ config('settings.app_name') }}</div>
        </div>
        <div class="content">
            <h2 style="text-transform: uppercase; tracking-tighter: -1px;">Payment Successful!</h2>
            <p>Hello <strong>{{ $organizer->name }}</strong>,</p>
            <p>Your payment for the <span class="highlight">{{ $plan?->name ?? 'Subscription' }}</span> plan has been successfully processed. Your account is now fully active and all limits have been updated.</p>
            
            <div style="background: #f9fafb; padding: 20px; border-radius: 15px; margin: 30px 0;">
                <table style="width: 100%; font-size: 13px;">
                    <tr>
                        <td style="color: #a0aec0;">Invoice ID</td>
                        <td style="text-align: right; font-weight: bold;">#{{ $transaction->id }}</td>
                    </tr>
                    <tr>
                        <td style="color: #a0aec0;">Amount Paid</td>
                        <td style="text-align: right; font-weight: bold;">IDR {{ number_format($transaction->amount) }}</td>
                    </tr>
                    <tr>
                        <td style="color: #a0aec0;">Expires On</td>
                        <td style="text-align: right; font-weight: bold; color: #6366f1;">{{ $organizer->subscription_expires_at->format('d M Y') }}</td>
                    </tr>
                </table>
            </div>

            <p style="text-align: center;">
                <a href="{{ route('login') }}" class="button">Go to Dashboard</a>
            </p>
            
            <p style="font-size: 13px;">We've attached the official PDF invoice to this email for your records.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('settings.app_name') }}. All rights reserved.</p>
            <p>Need help? Contact us at {{ config('settings.footer_email') }}</p>
        </div>
    </div>
</body>
</html>
