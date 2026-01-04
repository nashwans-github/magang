<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BeritaResource\Pages;
use App\Filament\Resources\BeritaResource\RelationManagers;
use App\Models\Berita;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BeritaResource extends Resource
{
    protected static ?string $model = Berita::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationLabel = 'Berita';
    protected static ?string $modelLabel = 'Berita';
    protected static ?string $pluralModelLabel = 'Berita';
    protected static ?string $navigationGroup = 'Konten Website';
    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->role === 'admin_opd') {
            $query->where('opd_id', auth()->user()->opd_id);
        } elseif (auth()->user()->role === 'peserta') {
            $query->whereHas('opd.magangApplications', function ($q) {
                $q->where('user_id', auth()->id())
                  ->where('status', 'approved'); // Only show if accepted
            });
        }

        return $query;
    }

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()->role, ['admin_pusat', 'admin_opd', 'peserta']);
    }

    public static function canCreate(): bool
    {
        return auth()->user()->role === 'admin_opd';
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->role === 'admin_opd' && $record->opd_id === auth()->user()->opd_id;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->role === 'admin_opd' && $record->opd_id === auth()->user()->opd_id;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\Select::make('opd_id')
                        ->relationship('opd', 'name')
                        ->label('OPD')
                        ->searchable()
                        ->preload()
                        ->required(),
                    Forms\Components\TextInput::make('title')
                        ->label('Judul Berita')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', \Illuminate\Support\Str::slug($state)) : null),
                    Forms\Components\TextInput::make('slug')
                        ->label('Slug')
                        ->disabled()
                        ->dehydrated()
                        ->required()
                        ->maxLength(255)
                        ->unique(Berita::class, 'slug', ignoreRecord: true),
                    Forms\Components\FileUpload::make('image')
                        ->label('Gambar Utama')
                        ->image()
                        ->directory('berita-images')
                        ->columnSpanFull(),
                    Forms\Components\RichEditor::make('content')
                        ->label('Konten Berita')
                        ->columnSpanFull()
                        ->required(),
                    Forms\Components\Toggle::make('is_published')
                        ->label('Terbitkan')
                        ->default(true),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->columns([
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\ImageColumn::make('image')
                        ->height('250px')
                        ->width('100%')
                        ->extraImgAttributes([
                            'class' => 'object-cover w-full rounded-t-xl',
                            'style' => 'width: 100%;',
                        ])
                        ->defaultImageUrl(url('/images/placeholder.png')),
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('title')
                            ->weight('bold')
                            ->size(Tables\Columns\TextColumn\TextColumnSize::Large)
                            ->limit(50),
                        Tables\Columns\TextColumn::make('opd.name')
                            ->color('gray')
                            ->size(Tables\Columns\TextColumn\TextColumnSize::Small)
                            ->icon('heroicon-m-building-office-2'),
                        Tables\Columns\Layout\Split::make([
                            Tables\Columns\TextColumn::make('created_at')
                                ->date()
                                ->color('gray')
                                ->size(Tables\Columns\TextColumn\TextColumnSize::Small),
                            Tables\Columns\IconColumn::make('is_published')
                                ->boolean()
                                ->alignRight(),
                        ]),
                    ])->space(2)->extraAttributes(['class' => 'p-4']),
                ])->space(0)->extraAttributes(['class' => 'rounded-xl overflow-hidden ring-1 ring-gray-950/5 dark:ring-white/10']),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat')
                    ->button()
                    ->outlined(),
                Tables\Actions\EditAction::make()
                    ->label('Edit')
                    ->button()
                    ->outlined()
                    ->visible(fn ($record) => auth()->user()->role === 'admin_opd' && $record->opd_id === auth()->user()->opd_id),
                Tables\Actions\DeleteAction::make()
                    ->label('Hapus')
                    ->button()
                    ->outlined()
                    ->visible(fn ($record) => auth()->user()->role === 'admin_opd' && $record->opd_id === auth()->user()->opd_id),
            ])
            ->bulkActions([
                //
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
            'index' => Pages\ListBeritas::route('/'),
            'create' => Pages\CreateBerita::route('/create'),
            'edit' => Pages\EditBerita::route('/{record}/edit'),
        ];
    }
}
