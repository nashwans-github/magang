<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgressResource\Pages;
use App\Filament\Resources\ProgressResource\RelationManagers;
use App\Models\Progress;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProgressResource extends Resource
{
    protected static ?string $model = Progress::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Jurnal Kegiatan';
    protected static ?string $modelLabel = 'Jurnal Kegiatan';
    protected static ?string $pluralModelLabel = 'Jurnal Kegiatan';
    protected static ?string $navigationGroup = 'Kegiatan Magang';
    protected static ?int $navigationSort = 3;

    public static function canCreate(): bool
    {
        return auth()->user()->role === 'peserta';
    }

    public static function canViewAny(): bool
    {
        // Allow all Admins
        if (in_array(auth()->user()->role, ['admin_pusat', 'admin_opd', 'admin_pembimbing'])) {
            return true;
        }

        return auth()->user()->role === 'peserta' && \App\Models\Peserta::where('user_id', auth()->id())->exists();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    // Logic untuk Peserta: Hidden & Auto-filled
                    Forms\Components\Hidden::make('peserta_id')
                        ->default(fn () => \App\Models\Peserta::where('user_id', auth()->id())->value('id'))
                        ->visible(fn () => auth()->user()->role === 'peserta'),

                    // Logic untuk Admin: Selectable
                    Forms\Components\Select::make('peserta_id')
                        ->relationship('peserta.user', 'name')
                        ->label('Nama Peserta')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->visible(fn () => auth()->user()->role !== 'peserta'),

                    Forms\Components\DatePicker::make('date')
                        ->label('Tanggal')
                        ->required()
                        ->default(now()),
                    Forms\Components\TextInput::make('title')
                        ->label('Judul Kegiatan')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\RichEditor::make('description')
                        ->label('Deskripsi Kegiatan')
                        ->columnSpanFull()
                        ->required(),
                    Forms\Components\FileUpload::make('file_path')
                        ->label('Lampiran File')
                        ->directory('progress-files')
                        ->columnSpanFull(),
                    
                    // Status: Hidden for Peserta, Editable for Admin
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'pending' => 'Menunggu',
                            'approved' => 'Disetujui',
                            'revision' => 'Revisi',
                        ])
                        ->default('pending')
                        ->required()
                        ->visible(fn () => auth()->user()->role !== 'peserta'),

                    // Feedback: Hidden for Peserta unless there is feedback
                    Forms\Components\Textarea::make('feedback')
                        ->label('Catatan Pembimbing')
                        ->columnSpanFull()
                        ->disabled(fn () => auth()->user()->role === 'peserta') // Enabled for admins/pembimbing
                        ->visible(fn ($record) => auth()->user()->role !== 'peserta' || filled($record?->feedback)),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('peserta.user.name')
                    ->label('Peserta')
                    ->searchable()
                    ->sortable()
                    ->visible(fn () => auth()->user()->role !== 'peserta'),
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Kegiatan')
                    ->limit(50)
                    ->searchable()
                    ->description(fn ($record) => $record ? \Illuminate\Support\Str::limit(strip_tags($record->description), 50) : null),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'revision' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'revision' => 'Revisi',
                    }),
                Tables\Columns\TextColumn::make('file_path')
                    ->label('Lampiran')
                    ->formatStateUsing(fn () => 'Lihat File')
                    ->url(fn ($record) => $record?->file_path ? \Illuminate\Support\Facades\Storage::url($record->file_path) : null)
                    ->icon('heroicon-o-paper-clip')
                    ->openUrlInNewTab()
                    ->color('primary')
                    ->visible(fn ($record) => filled($record?->file_path)),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => auth()->user()->role === 'peserta' && $record->status === 'pending'), 
                // Edit only for Peserta when pending? Or Pembimbing too? 
                // Let's keep Edit loose or restrict? User didn't specify. 
                // But typically if approved, no edit.
                // Let's stick to adding the new actions first, and maybe hide generic Edit for Pembimbing.
                
                Tables\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Jurnal')
                    ->form([
                        Forms\Components\Textarea::make('feedback')
                            ->label('Catatan Pembimbing (Opsional)')
                    ])
                    ->action(function (Progress $record, array $data) {
                        $record->update([
                            'status' => 'approved',
                            'feedback' => $data['feedback'],
                        ]);
                    })
                    ->visible(fn ($record) => auth()->user()->role !== 'peserta' && $record->status === 'pending'),

                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Jurnal')
                    ->form([
                        Forms\Components\Textarea::make('feedback')
                            ->label('Catatan Pembimbing')
                            ->required()
                    ])
                    ->action(function (Progress $record, array $data) {
                        $record->update([
                            'status' => 'revision',
                            'feedback' => $data['feedback'],
                        ]);
                    })
                    ->visible(fn ($record) => auth()->user()->role !== 'peserta' && $record->status === 'pending'),

                Tables\Actions\DeleteAction::make()
                     ->visible(fn ($record) => auth()->user()->role === 'peserta' && $record->status === 'pending'), // Restrict delete too?
            ])
            ->bulkActions([
                // Bulk actions removed as requested
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->role === 'peserta') {
            $peserta = \App\Models\Peserta::where('user_id', auth()->id())->first();
            if ($peserta) {
                $query->where('peserta_id', $peserta->id);
            }
        }

        return $query;
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
            'index' => Pages\ListProgress::route('/'),
            'create' => Pages\CreateProgress::route('/create'),
            'edit' => Pages\EditProgress::route('/{record}/edit'),
        ];
    }
}
