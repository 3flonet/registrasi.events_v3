<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Barryvdh\DomPDF\Facade\Pdf; // Impor fasad PDF

class CertificateController extends Controller
{
    public function download(Registration $registration)
    {
        $event = $registration->event;
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

        // Atur orientasi kertas menjadi landscape
        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('certificate-' . $registration->name . '.pdf');
    }
}
