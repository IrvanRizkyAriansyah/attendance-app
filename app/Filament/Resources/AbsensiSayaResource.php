<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbsensiSayaResource\Pages;
use App\Filament\Resources\AbsensiSayaResource\RelationManagers;
use App\Models\Absensi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Gate;

class AbsensiSayaResource extends Resource
{
    protected static ?string $model = Absensi::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Riwayat Absensi Saya';
    protected static ?string $navigationGroup = 'Karyawan';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),

                TextColumn::make('jam_masuk')
                    ->label('Jam Masuk'),

                TextColumn::make('jam_keluar')
                    ->label('Jam Pulang'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'hadir' => 'success',
                        'cuti' => 'warning',
                        'alfa' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(30),

            ])
            ->filters([
                //
            ])
            ->actions([
            ])
            ->bulkActions([
                
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
            'index' => Pages\ListAbsensiSayas::route('/'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Gate::allows('akses-manager') || Gate::allows('akses-karyawan');
    }
}
