<?php

namespace App\Filament\Resources\PresensiResource\Pages;

use App\Filament\Resources\PresensiResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePresensi extends CreateRecord
{
    protected static string $resource = PresensiResource::class;

    public static function canCreateAnother(): bool
    {
        // Disable "Create & Create Another" for 'peserta' role, or globally if desired
        if (auth()->user()->role === 'peserta') {
            return false;
        }
        return parent::canCreateAnother();
    }
}
