<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SuratResource\Pages;
use App\Filament\Resources\SuratResource\RelationManagers;
use App\Models\Surat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SuratResource extends Resource
{
    protected static ?string $model = Surat::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationLabel = 'Surat & Dokumen';
    protected static ?string $modelLabel = 'Surat & Dokumen';
    protected static ?string $pluralModelLabel = 'Surat & Dokumen';
    protected static ?string $navigationGroup = 'Evaluasi & Surat';
    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()->role, ['admin_opd', 'peserta']);
    }

    public static function canCreate(): bool
    {
        return in_array(auth()->user()->role, ['peserta']); 
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->role !== 'peserta';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\Select::make('peserta_id')
                        ->relationship('peserta', 'id', modifyQueryUsing: function (Builder $query) {
                            if (auth()->user()->role === 'admin_opd') {
                                $query->whereHas('bidang', function ($q) {
                                    $q->where('opd_id', auth()->user()->opd_id);
                                });
                            }
                            return $query;
                        })
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->user->name)
                        ->label('Nama Peserta')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->default(function () {
                            if (auth()->user()->role === 'peserta') {
                                return \App\Models\Peserta::where('user_id', auth()->id())->value('id');
                            }
                            return null;
                        })
                        ->disabled(fn () => auth()->user()->role === 'peserta'),
                    // Hidden field to ensure data is sent when disabled
                    Forms\Components\Hidden::make('peserta_id')
                        ->default(function () {
                            if (auth()->user()->role === 'peserta') {
                                return \App\Models\Peserta::where('user_id', auth()->id())->value('id');
                            }
                            return null;
                        })
                        ->visible(fn () => auth()->user()->role === 'peserta'),
                        
                    Forms\Components\Select::make('type')
                        ->label('Jenis Surat')
                        ->options([
                            'acceptance' => 'Surat Penerimaan',
                            'completion' => 'Surat Selesai Magang',
                        ])
                        ->required(),
                    Forms\Components\DatePicker::make('issued_date')
                        ->label('Tanggal Terbit')
                        // ->required() // Relaxed requirement for Peserta request
                        ->default(now())
                        ->visible(fn () => auth()->user()->role !== 'peserta'), 
                    Forms\Components\FileUpload::make('file_path')
                        ->label('File Surat')
                        ->directory('surat-files')
                        ->required(fn () => auth()->user()->role !== 'peserta') // Admin must upload (if fulfilling)
                        ->visible(fn () => auth()->user()->role !== 'peserta') // User doesn't upload
                        ->columnSpanFull(),
                ])->columns(2),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->role === 'peserta') {
            $query->whereHas('peserta', function ($q) {
                $q->where('user_id', auth()->id());
            });
        } elseif (auth()->user()->role === 'admin_opd') {
            $query->whereHas('peserta.bidang', function ($q) {
                $q->where('opd_id', auth()->user()->opd_id);
            });
        }

        return $query;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('peserta.user.name')
                    ->label('Peserta')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Jenis Surat')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'acceptance' => 'Penerimaan',
                        'completion' => 'Selesai Magang',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'acceptance' => 'success',
                        'completion' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('issued_date')
                    ->label('Tanggal Terbit')
                    ->date()
                    ->sortable()
                    ->placeholder('Menunggu Validasi'),
                Tables\Columns\TextColumn::make('file_path')
                    ->label('File')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Download' : 'Belum Ada')
                    ->color(fn ($state) => $state ? 'success' : 'gray')
                    ->url(fn ($record) => ($record && $record->file_path) ? \Illuminate\Support\Facades\Storage::url($record->file_path) : null)
                    ->openUrlInNewTab(),
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
                    ->visible(fn () => auth()->user()->role !== 'peserta'),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()->role !== 'peserta'), // Allow user to cancel if needed? Or just hide.
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->role !== 'peserta'),
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
            'index' => Pages\ListSurats::route('/'),
            'create' => Pages\CreateSurat::route('/create'),
            'edit' => Pages\EditSurat::route('/{record}/edit'),
        ];
    }
}
