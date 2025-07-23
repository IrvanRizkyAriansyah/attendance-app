<?php

namespace App\Filament\Widgets;

use App\Models\Absensi;
use Filament\Widgets\ChartWidget;

class AbsensiStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Status Absensi';
    protected static ?string $maxHeight = '230px';
    protected static ?int $sort = 5;

    protected function getData(): array
    {
        $today = now()->toDateString();

        $statuses = ['hadir', 'alfa', 'cuti', 'terlambat'];

        $counts = collect($statuses)->mapWithKeys(function ($status) use ($today) {
            return [$status => Absensi::where('tanggal', $today)->where('status', $status)->count()];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Status Absensi',
                    'data' => $counts->values(),
                    'backgroundColor' => [
                        'rgba(34,197,94,0.7)',    // hadir - green
                        'rgba(239,68,68,0.7)',    // alfa - red
                        'rgba(251,191,36,0.7)',   // cuti - yellow
                        'rgba(59, 130, 246, 0.7)',   // terlambat - blue
                    ],
                ],
            ],
            'labels' => $counts->keys(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
