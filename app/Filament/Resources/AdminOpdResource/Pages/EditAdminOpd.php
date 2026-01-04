<?php

namespace App\Filament\Resources\AdminOpdResource\Pages;

use App\Filament\Resources\AdminOpdResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdminOpd extends EditRecord
{
    protected static string $resource = AdminOpdResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
