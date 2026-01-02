<?php

namespace App\Filament\Resources\MagangApplicationResource\Pages;

use App\Filament\Resources\MagangApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMagangApplications extends ListRecords
{
    protected static string $resource = MagangApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
