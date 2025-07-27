<?php

namespace App\Filament\Resources\SlipGajiResource\Pages;

use App\Filament\Resources\SlipGajiResource;
use App\Models\SlipGaji;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateSlipGaji extends CreateRecord
{
    protected static string $resource = SlipGajiResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $exists = SlipGaji::where('user_id', $data['user_id'])
            ->where('bulan', $data['bulan'])
            ->where('tahun', $data['tahun'])
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'user_id' => 'Slip gaji untuk bulan dan tahun ini sudah dibuat.',
            ]);
        }

        return $data;
    }
}
