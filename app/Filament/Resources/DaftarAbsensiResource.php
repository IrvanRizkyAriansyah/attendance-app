<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DaftarAbsensiResource\Pages;
use App\Filament\Resources\DaftarAbsensiResource\RelationManagers;
use App\Models\Absensi;
use App\Models\DaftarAbsensi;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Gate;

class DaftarAbsensiResource extends Resource
{
    protected static ?string $model = Absensi::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-date-range';
    protected static ?string $navigationLabel = 'Daftar Absensi Karyawan';
    protected static ?string $navigationGroup = 'Absensi';

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
                TextColumn::make('user.name')->label('Karyawan')->searchable(),
                TextColumn::make('tanggal')->label('Tanggal')->date(),
                TextColumn::make('jam_masuk')->label('Masuk')->sortable(),
                TextColumn::make('jam_keluar')->label('Pulang')->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'hadir' => 'success',
                        'cuti' => 'warning',
                        'alfa' => 'danger',
                        'terlambat' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('keterangan')->limit(20),
            ])
            ->filters([
                //
                SelectFilter::make('status')
                    ->options([
                        'hadir' => 'Hadir',
                        'cuti' => 'Cuti',
                        'alfa' => 'Alfa',
                    ]),
                SelectFilter::make('user_id')
                    ->label('Karyawan')
                    ->relationship('user', 'name'),
                Filter::make('tanggal_range')
                    ->form([
                        DatePicker::make('from')->label('Dari Tanggal'),
                        DatePicker::make('until')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'], fn ($q) => $q->whereDate('tanggal', '>=', $data['from']))
                            ->when($data['until'], fn ($q) => $q->whereDate('tanggal', '<=', $data['until']));
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['from'] && !$data['until']) {
                            return null;
                        }

                        return 'Dari ' . ($data['from'] ? 'tanggal ' . \Carbon\Carbon::parse($data['from'])->format('d M Y') : '') .
                            ($data['until'] ? ' sampai ' . \Carbon\Carbon::parse($data['until'])->format('d M Y') : '');
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListDaftarAbsensis::route('/'),
            'create' => Pages\CreateDaftarAbsensi::route('/create'),
            'edit' => Pages\EditDaftarAbsensi::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Gate::allows('akses-manager') || Gate::allows('akses-superadmin');
    }
}
