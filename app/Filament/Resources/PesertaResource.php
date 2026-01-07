<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PesertaResource\Pages;
use App\Filament\Resources\PesertaResource\RelationManagers;
use App\Models\Peserta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PesertaResource extends Resource
{
    protected static ?string $model = Peserta::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Peserta Magang';
    protected static ?string $modelLabel = 'Peserta Magang';
    protected static ?string $pluralModelLabel = 'Peserta Magang';
    protected static ?string $navigationGroup = 'Kegiatan Magang';
    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->role === 'admin_opd') {
            $query->whereHas('bidang', function ($q) {
                $q->where('opd_id', auth()->user()->opd_id);
            });
        } elseif (auth()->user()->role === 'admin_pembimbing') {
            $pembimbing = \App\Models\Pembimbing::where('user_id', auth()->id())->first();
            if ($pembimbing) {
                $query->where('bidang_id', $pembimbing->bidang_id);
            } else {
                // If pembimbing record not found (shouldn't happen ideally), show nothing or all?
                // 安全策 (Safety): show nothing
                $query->whereRaw('1 = 0');
            }
        }

        return $query;
    }

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()->role, ['admin_pusat', 'admin_opd', 'admin_pembimbing']);
    }

    public static function canCreate(): bool
    {
        return false; // Disable manual creation of Peserta
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->role === 'admin_opd';
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->role === 'admin_opd';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\Select::make('user_id')
                        ->relationship('user', 'name')
                        ->label('Nama Peserta')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\Select::make('magang_application_id')
                        ->relationship('magangApplication', 'institution_name')
                        ->label('Asal Instansi')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\Select::make('bidang_id')
                        ->relationship('bidang', 'name')
                        ->label('Bidang')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\TextInput::make('major')
                        ->label('Jurusan')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('student_id_number')
                        ->label('NIM/NIS')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'active' => 'Aktif',
                            'completed' => 'Selesai',
                            'dropped' => 'Keluar/Diberhentikan',
                        ])
                        ->required()
                        ->default('active'),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bidang.name')
                    ->label('Bidang')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bidang.opd.name')
                    ->label('OPD')
                    ->searchable()
                    ->sortable()
                    ->visible(fn () => auth()->user()->role === 'admin_pusat'),
                Tables\Columns\TextColumn::make('major')
                    ->label('Jurusan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'completed' => 'info',
                        'dropped' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Bergabung Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups(
                auth()->user()->role !== 'admin_pembimbing' ? [
                    Tables\Grouping\Group::make('magangApplication.institution_name')
                        ->label('Instansi')
                        ->collapsible()
                        ->titlePrefixedWithLabel(false),
                ] : []
            )
            ->defaultGroup(auth()->user()->role !== 'admin_pembimbing' ? 'magangApplication.institution_name' : null)
            ->filters([
                Tables\Filters\SelectFilter::make('magang_application_id')
                    ->relationship('magangApplication', 'institution_name')
                    ->label('Asal Instansi')
                    ->searchable()
                    ->preload()
                    ->visible(fn () => auth()->user()->role !== 'admin_pembimbing'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()->role === 'admin_opd'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->role === 'admin_opd'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPesertas::route('/'),
            'create' => Pages\CreatePeserta::route('/create'),
            'edit' => Pages\EditPeserta::route('/{record}/edit'),
        ];
    }
}
