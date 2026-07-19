<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Services\TenantService;

class RegistrationController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }
    public function showQrCode($uuid)
    {
        // Gunakan withoutGlobalScope agar rute publik bisa menemukan data tanpa filter organizer_id
        $registration = Registration::withoutGlobalScope('organizer')
            ->with(['event' => function($q) {
                $q->withoutGlobalScope('organizer');
            }])
            ->where('uuid', $uuid)
            ->firstOrFail();

        $checkinUrl = route('checkin.scan', $registration->uuid);

        // Membuat QR code dalam format SVG
        $qrCode = QrCode::size(300)->format('svg')->generate($checkinUrl);

        return view('registration.qrcode', [
            'qrCode' => $qrCode,
            'registration' => $registration,
        ]);
    }

    public function scanCheckIn($uuid)
    {
        $registration = Registration::withoutGlobalScope('organizer')
            ->where('uuid', $uuid)
            ->firstOrFail();

        // This is a placeholder for the check-in logic.
        // For now, it just confirms the ticket is valid.
        return response('QR Code valid untuk ' . $registration->name);
    }
}
