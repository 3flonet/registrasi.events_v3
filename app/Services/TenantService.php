<?php

namespace App\Services;

use App\Models\Organizer;

class TenantService
{
    protected ?Organizer $organizer = null;

    public function setOrganizer(Organizer $organizer): void
    {
        $this->organizer = $organizer;
    }

    public function getOrganizer(): ?Organizer
    {
        return $this->organizer;
    }

    public function getOrganizerId(): ?int
    {
        return $this->organizer?->id;
    }

    public function isTenantScope(): bool
    {
        return $this->organizer !== null;
    }
}
