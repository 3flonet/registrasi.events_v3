<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToOrganizer;

class EventEmailTemplate extends Model
{
    use HasFactory, BelongsToOrganizer;

    protected $fillable = [
        'event_id',
        'organizer_id',
        'subject',
        'content',
        'whatsapp_content',
        'whatsapp_template_id',
        'category',
        'banner_path',
        'whatsapp_header_media_path',
        'whatsapp_buttons_mapping',
    ];

    protected $casts = [
        'whatsapp_buttons_mapping' => 'array',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function whatsappTemplate()
    {
        return $this->belongsTo(WhatsAppTemplate::class, 'whatsapp_template_id');
    }

    public static function getLibrary()
    {
        return [
            'transactional' => [
                'title' => 'E-Ticket Confirmation',
                'subject' => '🎟️ Registration Confirmed: {event_name}',
                'whatsapp_content' => "Halo *{name}*! 👋\n\nSelamat! Pendaftaran Anda untuk *{event_name}* telah berhasil dikonfirmasi.\n\n🎫 *TIKET:* {ticket_code}\n📅 *TANGGAL:* {date}\n📍 *LOKASI:* {event_instruction}\n\nSampai jumpa di acara! 🚀",
                'content' => "<div style='background-color: #f8fafc; padding: 50px 20px; font-family: sans-serif;'><div style='max-width: 600px; margin: 0 auto; background: white; border-radius: 32px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);'><div style='padding: 50px; text-align: center;'><!--[if mso]><table role=\"presentation\" width=\"100%\" style=\"text-align:center;\"><tr><td align=\"center\"><![endif]--><table align='center' width='80' height='80' cellpadding='0' cellspacing='0' style='margin: 0 auto 30px auto; background: #ecfdf5; border-radius: 24px; border-collapse: separate;'><tr><td align='center' valign='middle' style='color: #10b981; font-size: 40px; line-height: 1;'>✓</td></tr></table><!--[if mso]></td></tr></table><![endif]--><h1 style='color: #1a1235; font-size: 28px;'>PENDAFTARAN BERHASIL!</h1><p style='color: #64748b;'>Halo {name}, kursi Anda telah diamankan untuk {event_name}.</p><div style='background: #f8fafc; border: 2px dashed #e2e8f0; border-radius: 24px; padding: 30px; margin: 30px 0;'><h2 style='margin-top: 15px;'>{ticket_code}</h2></div><p>Lokasi: {event_instruction}</p><a href='{link_ticket}' style='display: inline-block; background: #322365; color: white; padding: 20px 40px; text-decoration: none; border-radius: 15px; font-weight: bold;'>LIHAT E-TIKET</a></div></div></div>"
            ],
            'auto_checkin' => [
                'title' => 'Check-in Notification',
                'subject' => '✨ Welcome to {event_name}!',
                'whatsapp_content' => "Selamat Datang, *{name}*! ✨\n\nSenang sekali Anda telah hadir di *{event_name}*. Kehadiran Anda telah tercatat pada pukul *{time}*.\n\nNikmati acaranya! 🌟",
                'content' => "<div style='background-color: #ecfdf5; padding: 50px 20px; font-family: sans-serif;'><div style='max-width: 600px; margin: 0 auto; background: white; border-radius: 32px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);'><div style='padding: 50px; text-align: center;'><!--[if mso]><table role=\"presentation\" width=\"100%\" style=\"text-align:center;\"><tr><td align=\"center\"><![endif]--><table align='center' width='80' height='80' cellpadding='0' cellspacing='0' style='margin: 0 auto 30px auto; background: #d1fae5; border-radius: 24px; border-collapse: separate;'><tr><td align='center' valign='middle' style='font-size: 40px; line-height: 1;'>👋</td></tr></table><!--[if mso]></td></tr></table><![endif]--><h1 style='color: #064e3b; font-size: 28px;'>SELAMAT DATANG!</h1><p style='color: #065f46;'>Halo {name}, Anda telah berhasil check-in di <strong>{event_name}</strong>.</p><div style='background: #f0fdf4; border-radius: 24px; padding: 25px; margin: 30px 0; border: 1px solid #d1fae5;'><p style='margin: 0; font-size: 14px; color: #059669;'>Waktu Kehadiran</p><h2 style='margin: 5px 0 0 0; color: #064e3b;'>{time}</h2></div><p style='color: #64748b;'>Selamat menikmati rangkaian acara kami!</p></div></div></div>"
            ],
            'event_invoice' => [
                'title' => 'Invoice Notification',
                'subject' => '⏳ Pembayaran Tiket: {event_name}',
                'whatsapp_content' => "Halo *{name}*,\n\nTerima kasih telah mendaftar. Harap selesaikan pembayaran agar tiket dapat segera kami terbitkan.\n\n💰 *TOTAL:* {total_bayar}\n🔗 *LINK BAYAR:* {payment_link}",
                'content' => "<div style='background-color: #f8fafc; padding: 50px 20px; font-family: sans-serif;'><div style='max-width: 600px; margin: 0 auto; background: white; border-radius: 32px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); border-top: 10px solid #1e293b;'><div style='padding: 50px;'><p style='color: #64748b; font-weight: 800; font-size: 12px; letter-spacing: 2px; margin-bottom: 10px;'>OFFICIAL INVOICE</p><h1 style='color: #1a1235; font-size: 32px; margin: 0 0 30px 0;'>{event_name}</h1><div style='background: #f8fafc; border-radius: 24px; padding: 30px; margin-bottom: 30px;'><table width='100%' cellpadding='0' cellspacing='0'><tr><td style='color: #64748b; padding-bottom: 15px;'>Nama Peserta</td><td align='right' style='color: #1a1235; font-weight: bold; padding-bottom: 15px;'>{name}</td></tr><tr><td style='color: #64748b; padding-top: 15px; border-top: 1px solid #e2e8f0;'>Total Tagihan</td><td align='right' style='color: #1e293b; font-size: 24px; font-weight: 900; padding-top: 15px; border-top: 1px solid #e2e8f0;'>{total_bayar}</td></tr></table></div><a href='{payment_link}' style='display: block; background: #1e293b; color: white; text-align: center; padding: 22px; border-radius: 20px; text-decoration: none; font-weight: bold; font-size: 16px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);'>BAYAR SEKARANG</a><p style='text-align: center; color: #94a3b8; font-size: 12px; margin-top: 30px;'>Harap segera selesaikan pembayaran untuk mengamankan slot Anda.</p></div></div></div>"
            ],
            'reminder' => [
                'title' => 'Event Reminder',
                'subject' => '⏰ PENGINGAT: {event_name} Segera Dimulai!',
                'whatsapp_content' => "Halo *{name}*! 👋\n\nIni adalah pengingat bahwa event *{event_name}* akan segera dimulai.\n\n📅 *TANGGAL:* {date}\n⏰ *JAM:* {time}\n📍 *LOKASI:* {event_instruction}\n\nSampai jumpa! 🚀",
                'content' => "<div style='background-color: #fffbeb; padding: 50px 20px; font-family: sans-serif;'><div style='max-width: 600px; margin: 0 auto; background: white; border-radius: 32px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);'><div style='padding: 50px; text-align: center;'><div style='font-size: 50px; margin-bottom: 20px;'>⏰</div><h1 style='color: #92400e; font-size: 28px;'>SUDAH SIAP?</h1><p style='color: #b45309;'>Halo {name}, kami sudah tidak sabar bertemu Anda di <strong>{event_name}</strong>.</p><div style='background: #fffbeb; border-radius: 24px; padding: 30px; margin: 30px 0; text-align: left;'><div style='margin-bottom: 15px;'><small style='color: #b45309; font-weight: bold;'>WAKTU & TEMPAT</small><div style='color: #92400e; font-size: 18px; font-weight: bold; margin-top: 5px;'>{date} • {time}</div></div><div style='color: #92400e; font-size: 14px; line-height: 1.6;'>{event_instruction}</div></div><a href='{link_ticket}' style='display: inline-block; background: #f59e0b; color: white; padding: 20px 40px; text-decoration: none; border-radius: 15px; font-weight: bold;'>SIAPKAN E-TIKET</a></div></div></div>"
            ],
            'certificate' => [
                'title' => 'Certificate Notification',
                'subject' => '🎓 E-Certificate: {event_name}',
                'whatsapp_content' => "Halo *{name}*! 👋\n\nSelamat! Sertifikat resmi Anda untuk *{event_name}* telah siap.\n\n🔗 *DOWNLOAD:* {link_certificate}",
                'content' => "<div style='background-color: #f0fdf4; padding: 50px 20px; font-family: sans-serif;'><div style='max-width: 600px; margin: 0 auto; background: white; border-radius: 32px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);'><div style='padding: 50px; text-align: center;'><div style='font-size: 60px; margin-bottom: 20px;'>🎓</div><h1 style='color: #064e3b; font-size: 28px;'>APRESIASI UNTUK ANDA!</h1><p style='color: #065f46;'>Halo {name}, terima kasih telah berpartisipasi di {event_name}. Sertifikat resmi Anda telah terbit.</p><div style='margin: 40px 0;'><a href='{link_certificate}' style='display: inline-block; background: #10b981; color: white; padding: 22px 45px; text-decoration: none; border-radius: 20px; font-weight: bold; font-size: 16px;'>UNDUH SERTIFIKAT (PDF)</a></div><p style='color: #94a3b8; font-size: 12px;'>Sertifikat ini merupakan bentuk penghargaan atas kehadiran dan kontribusi Anda.</p></div></div></div>"
            ],
            'event_feedback' => [
                'title' => 'Feedback Survey',
                'subject' => '🙏 Thank You! How was {event_name}?',
                'whatsapp_content' => "Halo *{name}*! 👋\n\nTerima kasih telah hadir di *{event_name}*. Kami ingin mendengar pendapat Anda.\n\n🔗 *SURVEY:* {link_feedback}",
                'content' => "<div style='background-color: #fdf2f8; padding: 50px 20px; font-family: sans-serif;'><div style='max-width: 600px; margin: 0 auto; background: white; border-radius: 32px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);'><div style='padding: 50px; text-align: center;'><div style='font-size: 50px; margin-bottom: 20px;'>🙏</div><h1 style='color: #831843; font-size: 28px;'>KAMI INGIN MENDENGAR ANDA</h1><p style='color: #9d174d;'>Halo {name}, bagaimana kesan Anda mengikuti <strong>{event_name}</strong>?</p><p style='color: #64748b; margin-bottom: 30px;'>Bantu kami menjadi lebih baik dengan memberikan feedback singkat.</p><a href='{link_feedback}' style='display: inline-block; background: #db2777; color: white; padding: 22px 45px; text-decoration: none; border-radius: 20px; font-weight: bold;'>ISI SURVEY SINGKAT</a></div></div></div>"
            ],
        ];
    }
}
