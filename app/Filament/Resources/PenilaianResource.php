<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenilaianResource\Pages;
use App\Filament\Resources\PenilaianResource\RelationManagers;
use App\Models\Penilaian;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PenilaianResource extends Resource
{
    protected static ?string $model = Penilaian::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationLabel = 'Penilaian';
    protected static ?string $modelLabel = 'Penilaian';
    protected static ?string $pluralModelLabel = 'Penilaian';
    protected static ?string $navigationGroup = 'Evaluasi & Surat';
    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->role === 'peserta') {
            $query->whereHas('peserta', function ($q) {
                $q->where('user_id', auth()->id());
            });
        }

        return $query;
    }

    public static function canViewAny(): bool
    {
        // Allow all Admins
        if (in_array(auth()->user()->role, ['admin_pembimbing'])) {
            return true;
        }
        
        return auth()->user()->role === 'peserta' && \App\Models\Peserta::where('user_id', auth()->id())->exists();
    }
    
    public static function canCreate(): bool
    {
        return auth()->user()->role !== 'peserta';
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
                        ->required(),
                    // Logic for Pembimbing ID and Name
                    Forms\Components\Select::make('pembimbing_id')
                        ->label('Pembimbing')
                        ->relationship('pembimbing.user', 'name')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->visible(fn () => auth()->user()->role !== 'admin_pembimbing')
                        ->default(null),
                        
                    Forms\Components\Hidden::make('pembimbing_id')
                        ->default(function () {
                            return \App\Models\Pembimbing::where('user_id', auth()->id())->value('id');
                        })
                        ->visible(fn () => auth()->user()->role === 'admin_pembimbing'),
                    
                    Forms\Components\TextInput::make('pembimbing_name')
                        ->label('Nama Pembimbing')
                        ->default(fn () => auth()->user()->name)
                        ->disabled()
                        ->dehydrated(false)
                        ->visible(fn () => auth()->user()->role === 'admin_pembimbing'),
                    Forms\Components\Section::make('Penilaian')->schema([
                        Forms\Components\TextInput::make('attendance_score')
                            ->label('Nilai Kehadiran')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('discipline_score')
                            ->label('Nilai Kedisipilan')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('task_completion_score')
                            ->label('Nilai Penyelesaian Tugas')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('deadline_accuracy_score')
                            ->label('Nilai Ketepatan Waktu')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('independence_score')
                            ->label('Nilai Kemandirian')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('final_score')
                            ->label('Nilai Akhir')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),
                    Forms\Components\Textarea::make('comments')
                        ->label('Komentar/Catatan')
                        ->columnSpanFull(),
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
                    ->sortable(),
                Tables\Columns\TextColumn::make('pembimbing.user.name')
                    ->label('Pembimbing')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('final_score')
                    ->label('Nilai Akhir')
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
                Tables\Actions\EditAction::make()
                    ->visible(fn () => in_array(auth()->user()->role, ['admin_pusat', 'admin_opd', 'admin_pembimbing'])),
            ])
            ->headerActions([
                 Tables\Actions\Action::make('export_csv')
                    ->label('Export CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->visible(fn () => in_array(auth()->user()->role, ['admin_pusat', 'admin_opd', 'admin_pembimbing']))
                    ->action(function () {
                        return response()->streamDownload(function () {
                            $handle = fopen('php://output', 'w');
                            fputcsv($handle, ['Nama Peserta', 'Pembimbing', 'Nilai Kehadiran', 'Nilai Kedisiplinan', 'Nilai Tugas', 'Nilai Ketepatan Waktu', 'Nilai Kemandirian', 'Nilai Akhir', 'Komentar']);
                            
                            $query = Penilaian::query();
                            
                            // Apply scoping
                            if (auth()->user()->role === 'peserta') {
                                $query->whereHas('peserta', function ($q) {
                                    $q->where('user_id', auth()->id());
                                });
                            } elseif (auth()->user()->role === 'admin_opd') {
                                $query->whereHas('peserta.bidang', function ($q) {
                                     $q->where('opd_id', auth()->user()->opd_id);
                                });
                            } elseif (auth()->user()->role === 'admin_pembimbing') {
                                $pembimbing = \App\Models\Pembimbing::where('user_id', auth()->id())->first();
                                if ($pembimbing) {
                                    // Usually Pembimbing sees penilaian they created OR for their bidang? 
                                    // Assuming they see what they are allowed to see in table.
                                    // If scope is stricter (only created by them), adjust here.
                                    // But typically Pembimbing manages their students.
                                     $query->whereHas('peserta', function($q) use ($pembimbing) {
                                        $q->where('bidang_id', $pembimbing->bidang_id);
                                    });
                                }
                            }
                            
                            $query->with(['peserta.user', 'pembimbing.user'])
                                ->orderBy('created_at', 'desc')
                                ->chunk(100, function ($rows) use ($handle) {
                                    foreach ($rows as $row) {
                                        fputcsv($handle, [
                                            $row->peserta->user->name ?? '-',
                                            $row->pembimbing->user->name ?? '-',
                                            $row->attendance_score,
                                            $row->discipline_score,
                                            $row->task_completion_score,
                                            $row->deadline_accuracy_score,
                                            $row->independence_score,
                                            $row->final_score,
                                            $row->comments,
                                        ]);
                                    }
                                });

                            fclose($handle);
                        }, 'penilaian-' . date('Y-m-d') . '.csv');
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => in_array(auth()->user()->role, ['admin_pusat', 'admin_opd', 'admin_pembimbing'])),
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
            'index' => Pages\ListPenilaians::route('/'),
            'create' => Pages\CreatePenilaian::route('/create'),
            'edit' => Pages\EditPenilaian::route('/{record}/edit'),
        ];
    }
}
