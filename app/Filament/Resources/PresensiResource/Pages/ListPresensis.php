<?php

namespace App\Filament\Resources\PresensiResource\Pages;

use App\Filament\Resources\PresensiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPresensis extends ListRecords
{
    protected static string $resource = PresensiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Create Action removed
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PresensiResource\Widgets\PresensiActionWidget::class,
            // PresensiResource\Widgets\PresensiApprovalWidget::class, // Removed as file is deleted
        ];
    }

    protected $listeners = ['presensi-updated' => '$refresh'];
}
