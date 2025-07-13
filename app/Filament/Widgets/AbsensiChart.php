<?php

namespace App\Filament\Widgets;

use App\Models\Absensi;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class AbsensiChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Absensi';
    protected static ?string $maxHeight = '230px';
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $labels = [];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $tanggal = Carbon::today()->subDays($i);
            $labels[] = $tanggal->format('d M');

            $jumlah = Absensi::whereDate('tanggal', $tanggal)->count();
            $data[] = $jumlah;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Absensi',
                    'data' => $data,
                    // 'backgroundColor' => 'rgba(59,130,246,0.5)',
                    // 'borderColor' => 'rgba(59,130,246,1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
