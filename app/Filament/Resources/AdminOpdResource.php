<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdminOpdResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AdminOpdResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'Admin OPD';
    protected static ?string $modelLabel = 'Admin OPD';
    protected static ?string $pluralModelLabel = 'Admin OPD';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'admin-opds';

    public static function getEloquentQuery(): Builder
    {
        // Scope to only 'admin_opd' role
        return parent::getEloquentQuery()->where('role', 'admin_opd');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->role === 'admin_pusat';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nama Lengkap')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->label('Alamat Email')
                        ->email()
                        ->required()
                        ->unique(User::class, 'email', ignoreRecord: true)
                        ->maxLength(255),
                    Forms\Components\TextInput::make('password')
                        ->label('Kata Sandi')
                        ->password()
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $context): bool => $context === 'create')
                        ->maxLength(255),
                    Forms\Components\Select::make('opd_id')
                        ->label('OPD')
                        ->relationship('opd', 'name')
                        ->required()
                        ->searchable()
                        ->preload(),
                    Forms\Components\Hidden::make('role')
                        ->default('admin_opd'),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('opd.name')
                    ->label('OPD')
                    ->searchable()
                    ->sortable(),
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdminOpds::route('/'),
            'create' => Pages\CreateAdminOpd::route('/create'),
            'edit' => Pages\EditAdminOpd::route('/{record}/edit'),
        ];
    }
}
