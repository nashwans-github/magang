<?php

namespace App\Filament\Widgets;

use App\Models\MagangApplication;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestApplicationsWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $heading = 'Permohonan Terbaru';

    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return auth()->user()->role === 'admin_pusat';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                MagangApplication::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pemohon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('opd.name')
                    ->label('Tujuan OPD'),
                Tables\Columns\TextColumn::make('institution_name')
                    ->label('Instansi'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('Lihat')
                    ->url(fn (MagangApplication $record): string => \App\Filament\Resources\MagangApplicationResource::getUrl('index')),
            ]);
    }
}
