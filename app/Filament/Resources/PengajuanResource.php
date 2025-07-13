<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengajuanResource\Pages;
use App\Filament\Resources\PengajuanResource\RelationManagers;
use App\Models\Pengajuan;
use App\Models\PengajuanCuti;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;

class PengajuanResource extends Resource
{
    protected static ?string $model = PengajuanCuti::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Pengajuan Cuti';
    protected static ?string $navigationGroup = 'Karyawan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                DatePicker::make('tanggal_mulai')
                    ->label('Tanggal Mulai')
                    ->minDate(Carbon::tomorrow())
                    ->reactive()
                    ->required(),
                DatePicker::make('tanggal_selesai')
                    ->label('Tanggal Selesai')
                    ->minDate(fn (callable $get) => Carbon::parse($get('tanggal_mulai')))
                    ->maxDate(fn (callable $get) => Carbon::parse($get('tanggal_mulai'))->addDays(7))
                    ->disabled(fn (callable $get) => !$get('tanggal_mulai')) 
                    ->required(),

                Textarea::make('alasan')
                    ->label('Alasan')
                    ->required()
                    ->rows(4),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('tanggal_mulai')->date()->label('Mulai'),
                TextColumn::make('tanggal_selesai')->date()->label('Selesai'),
                TextColumn::make('alasan')->limit(30)->label('Alasan'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pending' => 'warning',
                        'disetujui' => 'success',
                        'ditolak' => 'danger',
                    }),
                TextColumn::make('approver.name')
                    ->label('Disetujui Oleh')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('tanggal_disetujui')
                    ->label('Tanggal Disetujui')
                    ->dateTime()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                ])
            ->filters([
                //
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
            'index' => Pages\ListPengajuans::route('/'),
            'create' => Pages\CreatePengajuan::route('/create'),
            'edit' => Pages\EditPengajuan::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Gate::allows('akses-manager') || Gate::allows('akses-karyawan');
    }
}
