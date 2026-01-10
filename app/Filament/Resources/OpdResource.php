<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OpdResource\Pages;
use App\Filament\Resources\OpdResource\RelationManagers;
use App\Models\Opd;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OpdResource extends Resource
{
    protected static ?string $model = Opd::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'OPD';
    protected static ?string $modelLabel = 'OPD';
    protected static ?string $pluralModelLabel = 'OPD';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()->role, ['admin_pusat', 'admin_opd']);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->role === 'admin_opd') {
            $query->where('id', auth()->user()->opd_id);
        }

        return $query;
    }

    public static function canCreate(): bool
    {
         return auth()->user()->role === 'admin_pusat';
    }

    public static function canDelete(Model $record): bool
    {
         return auth()->user()->role === 'admin_pusat';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi OPD')->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nama OPD')
                        ->required()
                        ->maxLength(255)
                        ->disabled(fn () => auth()->user()->role === 'admin_opd') // Admin OPD cannot change name/slug
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', \Illuminate\Support\Str::slug($state)) : null),
                    Forms\Components\TextInput::make('slug')
                        ->label('Slug')
                        ->disabled()
                        ->dehydrated()
                        ->required()
                        ->maxLength(255)
                        ->unique(Opd::class, 'slug', ignoreRecord: true),
                    
                    Forms\Components\Select::make('admin_user_id')
                        ->label('Akun Admin OPD')
                        ->options(function ($record) {
                            return \App\Models\User::where('role', 'admin_opd')
                                ->where(function ($query) use ($record) {
                                    $query->whereNull('opd_id');
                                    if ($record) {
                                        $query->orWhere('opd_id', $record->id);
                                    }
                                })
                                ->pluck('name', 'id');
                        })
                        ->searchable()
                        ->preload()
                        ->visible(fn () => auth()->user()->role === 'admin_pusat')
                        ->afterStateHydrated(function (Forms\Components\Select $component, $record) {
                            if ($record) {
                                $admin = \App\Models\User::where('opd_id', $record->id)->where('role', 'admin_opd')->first();
                                if ($admin) {
                                    $component->state($admin->id);
                                }
                            }
                        })
                        ->saveRelationshipsUsing(function ($state, $record) {
                            if ($state) {
                                $user = \App\Models\User::find($state);
                                if ($user) {
                                    $user->update(['opd_id' => $record->id]);
                                }
                            }
                        })
                        ->createOptionForm([
                            Forms\Components\TextInput::make('name')
                                ->label('Nama Lengkap')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->required()
                                ->unique('users', 'email')
                                ->maxLength(255),
                            Forms\Components\TextInput::make('password')
                                ->label('Kata Sandi')
                                ->password()
                                ->required()
                                ->maxLength(255)
                                ->dehydrateStateUsing(fn ($state) => \Illuminate\Support\Facades\Hash::make($state)),
                            Forms\Components\Hidden::make('role')
                                ->default('admin_opd'),
                        ])
                        ->createOptionUsing(function (array $data) {
                            return \App\Models\User::create($data)->id;
                        }),

                    Forms\Components\Textarea::make('address')
                        ->label('Alamat')
                        ->required(fn () => auth()->user()->role === 'admin_opd') // Required only when editing profile
                        ->visible(fn () => auth()->user()->role === 'admin_opd')
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('phone')
                        ->label('No. Telp')
                        ->tel()
                        ->maxLength(255)
                        ->visible(fn () => auth()->user()->role === 'admin_opd'),
                    Forms\Components\TextInput::make('operational_hours')
                        ->label('Jam Operasional')
                        ->maxLength(255)
                        ->visible(fn () => auth()->user()->role === 'admin_opd'),
                ])->columns(2),

                Forms\Components\Section::make('Detail & Persyaratan')
                    ->schema([
                        Forms\Components\Textarea::make('required_education')
                            ->label('Pendidikan yang Dicari')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('document_requirements')
                            ->label('Persyaratan Dokumen')
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('documentation_images')
                            ->label('Dokumentasi')
                            ->image()
                            ->multiple()
                            ->directory('opd-images')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn () => auth()->user()->role === 'admin_opd'), // Only visible to Admin OPD
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama OPD')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('No. Telp')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('Alamat')
                    ->limit(50),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('magang_applications_count')
                    ->counts('magangApplications')
                    ->label('Jumlah Pemohon')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('created_until')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                     ->visible(fn () => auth()->user()->role === 'admin_pusat'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                         ->visible(fn () => auth()->user()->role === 'admin_pusat'),
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
            'index' => Pages\ListOpds::route('/'),
            'create' => Pages\CreateOpd::route('/create'),
            'edit' => Pages\EditOpd::route('/{record}/edit'),
        ];
    }
}
