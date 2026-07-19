<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f3f4f6;
            color: #333;
            padding: 20px;
            line-height: 1.6;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header {
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            color: #1f2937;
        }

        .details {
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            font-size: 14px;
        }

        .details ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .details li {
            margin-bottom: 8px;
        }

        .btn-container {
            text-align: center;
            margin-top: 30px;
            margin-bottom: 30px;
        }

        .button {
            background-color: #2563eb;
            color: #ffffff !important;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            display: inline-block;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #6b7280;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container" style="max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04); font-family: 'Outfit', sans-serif;">
        
        {{-- Header dengan Gradient --}}
        <div style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); padding: 40px; text-align: center; color: #ffffff;">
            <div style="text-transform: uppercase; letter-spacing: 0.3em; font-size: 10px; font-weight: 800; color: #818cf8; margin-bottom: 12px;">Official Invoice</div>
            <h1 style="margin: 0; font-size: 28px; font-weight: 900; letter-spacing: -0.025em;">Tagihan Pendaftaran</h1>
            <div style="margin-top: 20px; display: inline-block; background: rgba(255,255,255,0.1); padding: 8px 16px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                NO: {{ substr($registration->transaction->id ?? 'TRX-DEFAULT', -8) }}
            </div>
        </div>

        <div style="padding: 40px;">
            <p style="margin: 0; font-size: 16px; color: #1f2937;">Halo <strong>{{ $registration->name }}</strong>,</p>
            <p style="margin-top: 10px; font-size: 15px; color: #4b5563; line-height: 1.6;">Terima kasih telah memilih untuk bergabung bersama kami di event <strong>{{ $registration->event->name }}</strong>. Pendaftaran Anda telah kami amankan sementara.</p>

            {{-- Status Card --}}
            <div style="margin-top: 30px; background: #fffbeb; border: 1px solid #fef3c7; border-radius: 16px; padding: 20px; display: flex; align-items: center; gap: 15px;">
                <div style="font-size: 24px;">⏳</div>
                <div>
                    <div style="font-size: 10px; font-weight: 800; color: #92400e; uppercase tracking-widest;">Status Saat Ini</div>
                    <div style="font-size: 14px; font-weight: 700; color: #d97706;">MENUNGGU PEMBAYARAN</div>
                </div>
            </div>

            {{-- Rincian Tabular --}}
            <div style="margin-top: 30px; border: 1px solid #f1f5f9; border-radius: 20px; overflow: hidden;">
                <div style="background: #f8fafc; padding: 15px 20px; border-bottom: 1px solid #f1f5f9; font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; tracking-widest;">Rincian Tagihan</div>
                <div style="padding: 20px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 8px 0; font-size: 14px; color: #64748b;">Event</td>
                            <td style="padding: 8px 0; font-size: 14px; color: #1e293b; text-align: right; font-weight: 600;">{{ $registration->event->name }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; font-size: 14px; color: #64748b;">Kategori Tiket</td>
                            <td style="padding: 8px 0; font-size: 14px; color: #1e293b; text-align: right; font-weight: 600;">{{ $registration->ticketTier->name ?? 'Standard' }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 20px 0 0 0; font-size: 11px; color: #94a3b8; text-transform: uppercase; font-weight: 800;">Total Bayar</td>
                            <td style="padding: 20px 0 0 0; font-size: 24px; color: #4338ca; text-align: right; font-weight: 900;">Rp {{ number_format($registration->total_price, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Call to Action --}}
            <div style="margin-top: 40px; text-align: center;">
                <p style="font-size: 14px; color: #64748b; margin-bottom: 20px;">Klik tombol di bawah ini untuk melihat metode pembayaran dan menyelesaikan transaksi Anda secara aman.</p>
                <a href="{{ route('invoice.show', $registration->uuid) }}" style="display: inline-block; background: #4338ca; color: #ffffff; padding: 18px 36px; border-radius: 16px; text-decoration: none; font-weight: 800; font-size: 15px; box-shadow: 0 10px 15px -3px rgba(67, 56, 202, 0.4);">
                    BAYAR SEKARANG &rarr;
                </a>
            </div>

            {{-- Fallback Link --}}
            <div style="margin-top: 40px; border-top: 1px dashed #e2e8f0; padding-top: 20px; text-align: center;">
                <p style="font-size: 11px; color: #94a3b8; margin: 0;">Jika tombol tidak berfungsi, salin link berikut:</p>
                <a href="{{ route('invoice.show', $registration->uuid) }}" style="font-size: 11px; color: #6366f1; text-decoration: none; word-break: break-all;">{{ route('invoice.show', $registration->uuid) }}</a>
            </div>
        </div>

        {{-- Footer --}}
        <div style="background: #f8fafc; padding: 30px; text-align: center; border-top: 1px solid #f1f5f9;">
            <p style="margin: 0; font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase; tracking-widest;">&copy; {{ date('Y') }} {{ config('app.name') }}</p>
            <p style="margin-top: 5px; font-size: 11px; color: #94a3b8;">Sistem Notifikasi Otomatis - Registrasi.Events</p>
        </div>
    </div>
</body>

</html>