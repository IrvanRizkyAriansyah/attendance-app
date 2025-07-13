<?php

namespace App\Filament\Resources\DaftarAbsensiResource\Pages;

use App\Filament\Resources\DaftarAbsensiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDaftarAbsensi extends EditRecord
{
    protected static string $resource = DaftarAbsensiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
