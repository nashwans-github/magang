<?php

namespace App\Filament\Resources\MagangApplicationResource\Pages;

use App\Filament\Resources\MagangApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMagangApplication extends CreateRecord
{
    protected static string $resource = MagangApplicationResource::class;

    protected static bool $canCreateAnother = false;

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->label('Ajukan Permohonan'),
            $this->getCancelFormAction(),
        ];
    }
}
