<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $transaction->id }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
        }
        .header {
            border-bottom: 2px solid #6366f1;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .logo {
            font-size: 24px;
            font-weight: 900;
            color: #1a1235;
            text-transform: uppercase;
            letter-spacing: -1px;
        }
        .status-paid {
            color: #10b981;
            font-weight: bold;
            text-transform: uppercase;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        .table th {
            background: #f9fafb;
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #edf2f7;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 1px;
        }
        .table td {
            padding: 12px;
            border-bottom: 1px solid #edf2f7;
        }
        .total-section {
            margin-top: 30px;
            text-align: right;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #a0aec0;
            border-top: 1px solid #edf2f7;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <table style="width: 100%;">
                <tr>
                    <td>
                        <div class="logo">{{ $settings['app_name'] }}</div>
                        <p style="margin-top: 5px; color: #718096;">
                            {{ $settings['address'] }}<br>
                            {{ $settings['phone'] }} | {{ $settings['email'] }}
                        </p>
                    </td>
                    <td style="text-align: right; vertical-align: top;">
                        <h1 style="margin: 0; color: #6366f1; text-transform: uppercase; font-size: 28px;">INVOICE</h1>
                        <p style="margin: 5px 0;">#{{ $transaction->id }}</p>
                        <p style="margin: 0; font-weight: bold;">Date: {{ $transaction->created_at->format('d M Y') }}</p>
                    </td>
                </tr>
            </table>
        </div>

        <table style="width: 100%; margin-top: 20px;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <h4 style="text-transform: uppercase; font-size: 10px; color: #a0aec0; margin-bottom: 5px;">Bill To:</h4>
                    <strong style="font-size: 14px; color: #1a1235;">{{ $organizer->name }}</strong><br>
                    Slug: {{ $organizer->slug }}<br>
                    Status: <span class="status-paid">{{ $transaction->status }}</span>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    <h4 style="text-transform: uppercase; font-size: 10px; color: #a0aec0; margin-bottom: 5px;">Payment Method:</h4>
                    <strong>{{ $payment_method_detail }}</strong><br>
                    ID: {{ $transaction->midtrans_transaction_id ?? '-' }}
                </td>
            </tr>
        </table>

        <table class="table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Unit Price</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>Subscription Plan: {{ $plan?->name ?? 'Custom' }}</strong><br>
                        <span style="font-size: 10px; color: #718096;">Full access to event management hub for {{ $plan?->duration_days ?? 30 }} days.</span>
                    </td>
                    <td style="text-align: center;">1</td>
                    <td style="text-align: right;">IDR {{ number_format($transaction->amount) }}</td>
                    <td style="text-align: right;">IDR {{ number_format($transaction->amount) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="total-section">
            <table style="width: 250px; margin-left: auto;">
                <tr>
                    <td style="padding: 5px 0; color: #718096;">Subtotal</td>
                    <td style="padding: 5px 0; text-align: right;">IDR {{ number_format($transaction->amount) }}</td>
                </tr>
                <tr>
                    <td style="padding: 5px 0; color: #718096;">Tax (0%)</td>
                    <td style="padding: 5px 0; text-align: right;">IDR 0</td>
                </tr>
                <tr style="font-weight: bold; font-size: 16px; color: #1a1235;">
                    <td style="padding: 15px 0; border-top: 2px solid #edf2f7;">TOTAL</td>
                    <td style="padding: 15px 0; text-align: right; border-top: 2px solid #edf2f7; color: #6366f1;">IDR {{ number_format($transaction->amount) }}</td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p>Thank you for choosing {{ $settings['app_name'] }} as your event partner.</p>
            <p>&copy; {{ date('Y') }} {{ $settings['app_name'] }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
