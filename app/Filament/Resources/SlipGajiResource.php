<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SlipGajiResource\Pages;
use App\Filament\Resources\SlipGajiResource\RelationManagers;
use App\Models\Absensi;
use App\Models\SlipGaji;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Collection;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SlipGajiResource extends Resource
{
    protected static ?string $model = SlipGaji::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Karyawan';

    public static function form(Form $form): Form
    {
        function calculateFields($set, $get)
        {
            $userId = $get('user_id');
            $bulan = $get('bulan');
            $tahun = $get('tahun');

            if (!$userId || !$bulan || !$tahun) return;

            $jumlah = Absensi::where('user_id', $userId)
                ->whereMonth('tanggal', $bulan)
                ->whereYear('tanggal', $tahun)
                ->where('status', 'terlambat')
                ->count();

            $set('jumlah_terlambat', $jumlah);

            $user = User::find($userId);
            if (!$user) return;

            $gaji_pokok = match ($user->departemen) {
                'Marketing' => 4500000,
                'Keuangan' => 4000000,
                'Purchasing' => 4500000,
                default => 0,
            };

            $tunj_jabatan = match ($user->departemen) {
                'Marketing', 'Keuangan' => 1000000,
                'Purchasing' => 800000,
                default => 0,
            };

            $tunj_makan = 350000;
            $tunj_transport = 350000;

            $total = $gaji_pokok + $tunj_jabatan + $tunj_makan + $tunj_transport;
            $potongan = $jumlah * 50000;
            $takehome = $total - $potongan;

            $set('potongan', $potongan);
            $set('total_gaji', $total);
            $set('take_home_pay', $takehome);
        }
        return $form
            ->schema([
                //
                Select::make('user_id')
            ->label('Karyawan')
            ->relationship('user', 'name')
            ->searchable()
            ->required()
            ->reactive()
            ->afterStateUpdated(fn ($set, $get) => calculateFields($set, $get)),

        Select::make('bulan')
            ->label('Bulan')
            ->options([
                'Januari' => 'Januari',
                'Februari' => 'Februari',
                'Maret' => 'Maret',
                'April' => 'April',
                'Mei' => 'Mei',
                'Juni' => 'Juni',
                'Juli' => 'Juli',
                'Agustus' => 'Agustus',
                'September' => 'September',
                'Oktober' => 'Oktober',
                'November' => 'November',
                'Desember' => 'Desember',
            ])
            ->required()
            ->reactive()
            ->afterStateUpdated(fn ($set, $get) => calculateFields($set, $get)),

        TextInput::make('tahun')
            ->label('Tahun')
            ->default(now()->year)
            ->required()
            ->reactive()
            ->afterStateUpdated(fn ($set, $get) => calculateFields($set, $get))
            ->dehydrated(true),

        TextInput::make('jumlah_terlambat')
            ->label('Jumlah Terlambat')
            ->disabled()
            ->dehydrated(true),

        TextInput::make('potongan')
            ->disabled()
            ->label('Potongan')
            ->dehydrated(true),

        TextInput::make('total_gaji')
            ->disabled()
            ->label('Total Gaji')
            ->dehydrated(true),

        TextInput::make('take_home_pay')
            ->disabled()
            ->label('Take Home Pay')
            ->dehydrated(true),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('user.name')->label('Nama'),
                TextColumn::make('user.departemen')->label('Departemen')->toggleable(),
                TextColumn::make('bulan'),
                TextColumn::make('tahun'),
                TextColumn::make('jumlah_terlambat')->toggleable(),
                TextColumn::make('potongan')->money('IDR'),
                TextColumn::make('total_gaji')->money('IDR'),
                TextColumn::make('take_home_pay')->money('IDR'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->visible(fn () => auth()->user()?->role === 'keuangan'),
                Tables\Actions\DeleteAction::make()
                ->visible(fn () => auth()->user()?->role === 'keuangan'),

                Action::make('export_pdf')
                ->label('Export PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function (SlipGaji $record) {
                    $pdf = Pdf::loadView('pdf.slip-gaji', ['slip' => $record]);
                    return response()->streamDownload(fn () => print($pdf->output()), 'SlipGaji-'.$record->user->name.'.pdf');
                    }),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn () => auth()->user()?->role === 'keuangan'),
                ]),

                BulkAction::make('export_all_pdf')
                    ->label('Export PDF Semua')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Collection $records) {
                        $zip = new \ZipArchive();
                        $zipPath = storage_path('app/slip_gaji_exports.zip');

                        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE)) {
                            foreach ($records as $record) {
                                $pdf = Pdf::loadView('pdf.slip-gaji', ['slip' => $record]);
                                $filename = 'SlipGaji-' . $record->user->name . '.pdf';
                                $zip->addFromString($filename, $pdf->output());
                            }
                            $zip->close();
                        }

                        return response()->download($zipPath)->deleteFileAfterSend(true);
                    }),

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
            'index' => Pages\ListSlipGajis::route('/'),
            'create' => Pages\CreateSlipGaji::route('/create'),
            'edit' => Pages\EditSlipGaji::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        if (in_array($user->role, ['karyawan', 'manager'])) {
            return parent::getEloquentQuery()
                ->where('user_id', $user->id);
        }

        return parent::getEloquentQuery();
    }
}
