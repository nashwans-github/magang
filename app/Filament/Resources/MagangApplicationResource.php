<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MagangApplicationResource\Pages;
use App\Filament\Resources\MagangApplicationResource\RelationManagers;
use App\Models\MagangApplication;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MagangApplicationResource extends Resource
{
    protected static ?string $model = MagangApplication::class;

    public static function canViewAny(): bool
    {
        return auth()->user()->role !== 'admin_pembimbing';
    }

    public static function canCreate(): bool
    {
        return ! in_array(auth()->user()->role, ['admin_opd', 'admin_pembimbing', 'admin_pusat']); 
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()->role !== 'admin_pusat';
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()->role !== 'admin_pusat';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (in_array(auth()->user()->role, ['pemohon', 'peserta'])) {
            $query->where('user_id', auth()->id());
        } elseif (auth()->user()->role === 'admin_opd') {
            $query->where('opd_id', auth()->user()->opd_id);
        }

        return $query;
    }

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Permohonan Magang';
    protected static ?string $modelLabel = 'Permohonan Magang';
    protected static ?string $pluralModelLabel = 'Permohonan Magang';
    protected static ?string $navigationGroup = 'Pendaftaran';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\Select::make('user_id')
                        ->relationship('user', 'name')
                        ->label('Pemohon')
                        ->searchable()
                        ->default(auth()->id())
                        ->required()
                        ->hidden(fn () => in_array(auth()->user()->role, ['pemohon', 'peserta'])),
                    Forms\Components\Hidden::make('user_id')
                        ->default(auth()->id())
                        ->visible(fn () => in_array(auth()->user()->role, ['pemohon', 'peserta'])),
                    Forms\Components\Select::make('opd_id')
                        ->relationship('opd', 'name')
                        ->label('Tujuan OPD')
                        ->searchable()
                        ->preload()
                        ->live()
                        ->required(),
                    Forms\Components\Select::make('bidang_id')
                        ->label('Bidang Magang (Ketua/Individu)')
                        ->options(function (Forms\Get $get) {
                            $opdId = $get('opd_id');
                            if (! $opdId) return [];
                            return \App\Models\Bidang::where('opd_id', $opdId)->pluck('name', 'id');
                        })
                        ->searchable()
                        ->preload()
                        ->required()
                        ->visible(fn (Forms\Get $get) => filled($get('opd_id'))),
                    Forms\Components\TextInput::make('institution_name')
                        ->label('Asal Instansi')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('jurusan')
                        ->label('Jurusan')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\DatePicker::make('start_date')
                        ->label('Tanggal Mulai')
                        ->required(),
                    Forms\Components\DatePicker::make('end_date')
                        ->label('Tanggal Selesai')
                        ->required(),
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'pending' => 'Menunggu',
                            'approved' => 'Disetujui',
                            'rejected' => 'Ditolak',
                        ])
                        ->required()
                        ->default('pending')
                        ->visible(fn () => ! in_array(auth()->user()->role, ['pemohon', 'peserta'])),
                    Forms\Components\Hidden::make('status')
                        ->default('pending')
                        ->visible(fn () => in_array(auth()->user()->role, ['pemohon', 'peserta'])),
                    Forms\Components\FileUpload::make('documents')
                        ->label('Berkas Persyaratan')
                        ->multiple()
                        ->directory('application-documents')
                        ->openable()
                        ->downloadable()
                        ->previewable(true)
                        ->reorderable()
                        ->appendFiles()
                        ->columnSpanFull(),
                    
                    Forms\Components\Section::make('Anggota Kelompok (Opsional)')
                        ->description(new \Illuminate\Support\HtmlString('Isi jika pengajuan ini untuk kelompok. Kosongkan jika individu.<br><strong>Catatan:</strong> Jika disetujui, akun untuk anggota akan dibuat otomatis dengan password bawaan: <strong>password123</strong>'))
                        ->schema([
                            Forms\Components\Repeater::make('members')
                                ->relationship('members')
                                ->hiddenLabel()
                                ->schema([
                                    Forms\Components\TextInput::make('name')
                                        ->label('Nama Lengkap')
                                        ->required(),
                                    Forms\Components\TextInput::make('jurusan')
                                        ->label('Jurusan')
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('email')
                                        ->label('Email')
                                        ->email()
                                        ->required(),
                                    Forms\Components\TextInput::make('phone')
                                        ->label('No. Telepon')
                                        ->tel()
                                        ->required(),
                                    Forms\Components\Select::make('bidang_id')
                                        ->label('Pilihan Bidang')
                                        ->placeholder('Pilih OPD terlebih dahulu')
                                        ->options(function (Forms\Get $get) {
                                            $opdId = $get('../../opd_id');
                                            if (! $opdId) {
                                                return [];
                                            }
                                            return \App\Models\Bidang::where('opd_id', $opdId)->pluck('name', 'id');
                                        })
                                        ->required(),
                                ])
                                ->columns(2)
                                ->columnSpanFull()
                                ->addActionLabel('Tambah Anggota')
                                ->addable(fn () => auth()->user()->role !== 'admin_opd')
                                ->deletable(fn () => auth()->user()->role !== 'admin_opd')
                        ])
                        ->visible(fn () => true),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pemohon')
                    ->searchable()
                    ->visible(fn () => ! in_array(auth()->user()->role, ['pemohon', 'peserta'])),
                Tables\Columns\TextColumn::make('opd.name')
                    ->label('Tujuan OPD')
                    ->searchable(),
                Tables\Columns\TextColumn::make('institution_name')
                    ->label('Instansi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('documents')
                    ->label('Berkas')
                    ->getStateUsing(fn () => 'Lihat Berkas')
                    ->badge()
                    ->color('info')
                    ->action(
                        Tables\Actions\Action::make('view_documents')
                            ->modalHeading('Berkas Persyaratan')
                            ->modalContent(function ($record) {
                                $state = $record->documents;
                                $docs = $state;
                                if (is_string($docs)) {
                                    $decoded = json_decode($docs, true);
                                    if (is_array($decoded)) {
                                        $docs = $decoded;
                                    }
                                }
                                $docs = \Illuminate\Support\Arr::wrap($docs);
                                
                                return view('filament.resources.magang-application-resource.pages.view-documents', [
                                    'documents' => $docs
                                ]);
                            })
                            ->modalSubmitAction(false)
                            ->modalCancelAction(fn ($action) => $action->label('Tutup'))
                    ),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Mulai')
                    ->date()
                   ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Selesai')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
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
                        default => 'gray',
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(function ($record) {
                        // Admin Pusat cannot edit (read-only)
                        if (auth()->user()->role === 'admin_pusat') {
                            return false;
                        }
                        
                        // Edit only allowed if status is pending (for everyone: User, Admin OPD)
                        return $record->status === 'pending';
                    }),
                Tables\Actions\DeleteAction::make()
                    ->label(fn () => in_array(auth()->user()->role, ['pemohon', 'peserta']) ? 'Batalkan Pengajuan' : 'Hapus')
                    ->visible(function ($record) {
                        // Admin Pusat cannot delete
                        if (auth()->user()->role === 'admin_pusat') {
                            return false;
                        }

                        // Delete only allowed if status is pending
                        // Exception: Admin OPD might want to delete rejected ones? 
                        // User request: "hapus ubah dan hapus ketika sudah disetuujui" -> implies if Approved, NO Delete.
                        // Assuming if Rejected, arguably NO Delete too (keep history).
                        // So stricter rule: Only Pending.
                        return $record->status === 'pending';
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                         ->visible(fn () => ! in_array(auth()->user()->role, ['pemohon', 'peserta', 'admin_pusat'])),
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
            'index' => Pages\ListMagangApplications::route('/'),
            'create' => Pages\CreateMagangApplication::route('/create'),
            'edit' => Pages\EditMagangApplication::route('/{record}/edit'),
        ];
    }
}
