<?php

namespace App\Filament\Resources\PengajuanCutiResource\Pages;

use App\Filament\Resources\PengajuanCutiResource;
use App\Models\Absensi;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditPengajuanCuti extends EditRecord
{
    protected static string $resource = PengajuanCutiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $record = $this->record;

        DB::transaction(function () use ($record) {
            $tanggalMulai = \Carbon\Carbon::parse($record->tanggal_mulai);
            $tanggalSelesai = \Carbon\Carbon::parse($record->tanggal_selesai);

            if ($record->status === 'disetujui') {
                for ($tanggal = $tanggalMulai->copy(); $tanggal->lte($tanggalSelesai); $tanggal->addDay()) {
                    \App\Models\Absensi::updateOrCreate(
                        [
                            'user_id' => $record->user_id,
                            'tanggal' => $tanggal->format('Y-m-d'),
                        ],
                        [
                            'status' => 'cuti',
                            'keterangan' => $record->alasan,
                        ]
                    );
                }
            } else {
                Absensi::where('user_id', $record->user_id)
                    ->whereBetween('tanggal', [$tanggalMulai->format('Y-m-d'), $tanggalSelesai->format('Y-m-d')])
                    ->where('status', 'cuti')
                    ->delete();
            }
        });
    }

}
