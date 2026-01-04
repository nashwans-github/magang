<?php

namespace App\Filament\Resources\AdminOpdResource\Pages;

use App\Filament\Resources\AdminOpdResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdminOpds extends ListRecords
{
    protected static string $resource = AdminOpdResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
