<?php

namespace App\Imports;

use App\Models\Invitation;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class InvitationsImport implements ToModel, WithHeadingRow
{
    protected $eventId;

    public function __construct($eventId)
    {
        $this->eventId = $eventId;
    }

    public function model(array $row)
    {
        // Pastikan row memiliki data minimal (nama wajib ada)
        if (!isset($row['name']) && !isset($row['nama'])) {
            return null;
        }

        return new Invitation([
            'event_id'     => $this->eventId,
            'name'         => $row['name'] ?? $row['nama'] ?? 'Guest',
            'email'        => $row['email'] ?? null,
            'phone_number' => $row['phone'] ?? $row['phone_number'] ?? $row['wa'] ?? null,
            'company'      => $row['company'] ?? $row['instansi'] ?? null,
            'category'     => $row['jabatan'] ?? $row['category'] ?? $row['kategori'] ?? 'Uncategorized',
            'status'       => 'pending',
        ]);
    }
}
