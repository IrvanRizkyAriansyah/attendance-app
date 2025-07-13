<?php

namespace App\Filament\Widgets;

use App\Models\Absensi;
use App\Models\PengajuanCuti;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected function getStats(): array
    {
        return [
            //
            Stat::make('Total Karyawan', User::where('role', 'karyawan')->count())
                ->icon('heroicon-o-user-group')
                ->color('primary'),

            Stat::make('Absensi Hari Ini', Absensi::whereDate('tanggal', now())->count())
                ->icon('heroicon-o-clipboard-document-check')
                ->color('success'),

            Stat::make('Cuti Pending', PengajuanCuti::where('status', 'pending')->count())
                ->icon('heroicon-o-clock')
                ->color('warning'),

        ];
    }
}
