<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PembimbingResource\Pages;
use App\Filament\Resources\PembimbingResource\RelationManagers;
use App\Models\Pembimbing;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PembimbingResource extends Resource
{
    protected static ?string $model = Pembimbing::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Pembimbing';
    protected static ?string $modelLabel = 'Pembimbing';
    protected static ?string $pluralModelLabel = 'Pembimbing';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 3;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->role !== 'admin_opd';
    }

    public static function canCreate(): bool
    {
        return auth()->user()->role !== 'admin_pusat';
    }

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()->role, ['admin_pusat', 'admin_opd']);
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->role !== 'admin_pusat';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\Select::make('user_id')
                        ->relationship('user', 'name')
                        ->label('Nama Pembimbing')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\Select::make('bidang_id')
                        ->relationship('bidang', 'name', modifyQueryUsing: function (Builder $query) {
                            if (auth()->user()->role === 'admin_opd') {
                                return $query->where('opd_id', auth()->user()->opd_id);
                            }
                            return $query;
                        })
                        ->label('Bidang')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\TextInput::make('nip')
                        ->label('NIP')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('position')
                        ->label('Jabatan')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('phone')
                        ->label('No. Telp')
                        ->tel()
                        ->maxLength(255),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Pembimbing')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bidang.name')
                    ->label('Bidang')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable(),
                Tables\Columns\TextColumn::make('position')
                    ->label('Jabatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()->role !== 'admin_pusat'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->role !== 'admin_pusat'),
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
            'index' => Pages\ListPembimbings::route('/'),
            'create' => Pages\CreatePembimbing::route('/create'),
            'edit' => Pages\EditPembimbing::route('/{record}/edit'),
        ];
    }
}
