<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PresensiResource\Pages;
use App\Filament\Resources\PresensiResource\RelationManagers;
use App\Models\Presensi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PresensiResource extends Resource
{
    protected static ?string $model = Presensi::class;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->role === 'peserta') {
            $query->whereHas('peserta', function ($q) {
                $q->where('user_id', auth()->id());
            });
        } elseif (auth()->user()->role === 'admin_pembimbing') {
            // Filter by Bidang
            $pembimbing = \App\Models\Pembimbing::where('user_id', auth()->id())->first();
            if ($pembimbing) {
                // Scoping by Bidang
                $query->whereHas('peserta', function($q) use ($pembimbing) {
                    $q->where('bidang_id', $pembimbing->bidang_id);
                });
                
                // Group by Peserta (Show latest record only) for the Card View
                // Using a subquery to get the latest ID for each peserta_id
                $latestIds = \App\Models\Presensi::selectRaw('MAX(id) as id')
                    ->whereHas('peserta', function($q) use ($pembimbing) {
                        $q->where('bidang_id', $pembimbing->bidang_id);
                    })
                    ->groupBy('peserta_id')
                    ->pluck('id');
                
                $query->whereIn('id', $latestIds);
            }
        } 
        // Admin OPD? If needed, add similar scoping logic here.

        return $query;
    }

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Presensi';
    protected static ?string $modelLabel = 'Presensi';
    protected static ?string $pluralModelLabel = 'Presensi';
    protected static ?string $navigationGroup = 'Kegiatan Magang';
    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        // Allow all Admins
        if (in_array(auth()->user()->role, ['admin_pembimbing'])) {
            return true;
        }

        // For Peserta: Only visible if they are actually a 'Peserta'
        return auth()->user()->role === 'peserta' && \App\Models\Peserta::where('user_id', auth()->id())->exists();
    }

    public static function canCreate(): bool
    {
        return false; // Disable manual creation
    }

    public static function form(Form $form): Form
    {
         return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\Select::make('peserta_id')
                        ->relationship('peserta.user', 'name')
                        ->label('Nama Peserta')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->disabled(),
                    Forms\Components\DatePicker::make('date')
                        ->label('Tanggal')
                        ->required(),
                    Forms\Components\TimePicker::make('check_in')
                        ->label('Jam Masuk')
                        ->required(),
                    Forms\Components\TimePicker::make('check_out')
                        ->label('Jam Keluar'),
                     Forms\Components\Select::make('status')
                        ->options([
                            'hadir' => 'Hadir',
                            'sakit' => 'Sakit',
                            'izin' => 'Izin',
                            'alpa' => 'Alpha',
                        ])
                        ->required(),
                    Forms\Components\Textarea::make('notes')
                        ->label('Keterangan'),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        $isAdminPembimbing = auth()->user()->role === 'admin_pembimbing';

        return $table
            ->contentGrid(fn () => $isAdminPembimbing ? ['md' => 2, 'xl' => 3] : null)
            ->recordAction(fn () => $isAdminPembimbing ? 'history' : null) // Click card triggers 'history' action
            ->recordUrl(null) // Disable default edit/view URL behavior on click
            ->defaultSort('date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('peserta.user.name')
                    ->label('Peserta')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->size('lg'),
                
                // Columns for Normal List (Peserta/Others)
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable()
                    ->visible(!$isAdminPembimbing),
                Tables\Columns\TextColumn::make('check_in')
                    ->label('Masuk')
                    ->time()
                    ->visible(!$isAdminPembimbing),
                Tables\Columns\TextColumn::make('check_out')
                    ->label('Keluar')
                    ->time()
                    ->visible(!$isAdminPembimbing),
                
                // Columns for Card View (Pembimbing)
                Tables\Columns\TextColumn::make('status') // Last Status
                    ->label('Status Terakhir')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'hadir' => 'success',
                        'sakit' => 'warning',
                        'izin' => 'info',
                        'alpa' => 'danger',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('date') // Last Date for Card
                    ->label('Terakhir Presensi')
                    ->date('d M Y')
                    ->visible($isAdminPembimbing)
                    ->color('gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'hadir' => 'Hadir',
                        'sakit' => 'Sakit',
                        'izin' => 'Izin',
                        'alpa' => 'Alpha',
                    ])
                    ->visible(!$isAdminPembimbing), // Hide filter in card view or keep? Maybe confusing if filtered by status of latest record.
                Tables\Filters\Filter::make('today')
                    ->label('Hari Ini')
                    ->query(fn (Builder $query): Builder => $query->whereDate('date', now()))
                    ->visible(!$isAdminPembimbing),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->visible(!$isAdminPembimbing),
                Tables\Actions\EditAction::make()
                    ->visible(fn () => false), // Disable Edit for everyone (or restrict very strictly if needed, e.g. Admin Pusat only)
                
                // Action for Card View: See History
                Tables\Actions\Action::make('history')
                    ->label('Lihat Riwayat')
                    ->icon('heroicon-o-clock')
                    ->color('primary')
                    ->modalHeading(fn ($record) => 'Riwayat Presensi: ' . $record->peserta->user->name)
                    ->modalContent(fn ($record) => view('filament.tables.actions.presensi-history', ['record' => $record]))
                    ->modalSubmitAction(false) // View only
                    ->modalCancelAction(fn ($action) => $action->label('Tutup'))
                    ->visible(fn () => auth()->user()->role === 'admin_pembimbing'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_csv')
                    ->label('Export CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->visible(fn () => in_array(auth()->user()->role, ['admin_pusat', 'admin_opd', 'admin_pembimbing']))
                    ->action(function () {
                        return response()->streamDownload(function () {
                            $handle = fopen('php://output', 'w');
                            fputcsv($handle, ['Nama Peserta', 'Tanggal', 'Jam Masuk', 'Jam Keluar', 'Status', 'Keterangan']);
                            
                            $query = Presensi::query();
                            
                            // Apply same scoping as getEloquentQuery
                            if (auth()->user()->role === 'peserta') {
                                $query->whereHas('peserta', function ($q) {
                                    $q->where('user_id', auth()->id());
                                });
                            } elseif (auth()->user()->role === 'admin_opd') {
                                // Add scoping for admin_opd
                                $query->whereHas('peserta.bidang', function ($q) {
                                        $q->where('opd_id', auth()->user()->opd_id);
                                });
                            } elseif (auth()->user()->role === 'admin_pembimbing') {
                                // Admin Pembimbing typically sees their Bidang's presensi?
                                // Need to check Pembimbing logic.
                                $pembimbing = \App\Models\Pembimbing::where('user_id', auth()->id())->first();
                                if ($pembimbing) {
                                    $query->whereHas('peserta', function($q) use ($pembimbing) {
                                        $q->where('bidang_id', $pembimbing->bidang_id);
                                    });
                                }
                            }
                            
                            $query->with(['peserta.user'])
                                ->orderBy('date', 'desc')
                                ->chunk(100, function ($rows) use ($handle) {
                                    foreach ($rows as $row) {
                                        fputcsv($handle, [
                                            $row->peserta->user->name ?? '-',
                                            $row->date,
                                            $row->check_in,
                                            $row->check_out,
                                            $row->status,
                                            $row->notes,
                                        ]);
                                    }
                                });

                            fclose($handle);
                        }, 'presensi-' . date('Y-m-d') . '.csv');
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => false), // Disable Delete for everyone
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
            'index' => Pages\ListPresensis::route('/'),
            'create' => Pages\CreatePresensi::route('/create'),
            'edit' => Pages\EditPresensi::route('/{record}/edit'),
        ];
    }
}
