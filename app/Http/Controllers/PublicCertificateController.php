<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PublicCertificateController extends Controller
{
    public function show($uuid)
    {
        $registration = Registration::withoutGlobalScope('organizer')
            ->where('uuid', $uuid)
            ->firstOrFail();

        // Verifikasi apakah peserta sudah check-in
        if (!$registration->checked_in_at && $registration->checkinLogs()->count() === 0) {
            return view('feedback.error', [
                'message' => 'Certificate is only available for participants who have checked in to the event.'
            ]);
        }

        $event = $registration->event;
        
        // Optional: Only allow if checked in or paid
        // if ($registration->status !== 'checked_in') {
        //     abort(403, 'Certificate is only available for attendees.');
        // }

        $config = $event->certificate_config ?? [];

        $data = [
            'registrantName' => $registration->name,
            'eventName'      => $event->name,
            'title'          => $config['title'] ?? 'Certificate of Participation',
            'body'           => $config['body'] ?? 'is hereby granted this certificate for successfully participating in the',
            'signerName'     => $config['signer_name'] ?? '',
            'signerTitle'    => $config['signer_title'] ?? '',
            'bgPath'         => isset($config['bg_path']) ? storage_path('app/public/' . $config['bg_path']) : null,
            'signaturePath'  => isset($config['signature_path']) ? storage_path('app/public/' . $config['signature_path']) : null,
        ];

        $pdf = Pdf::loadView('certificate.template', $data);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('certificate-' . $registration->name . '.pdf');
    }
}
