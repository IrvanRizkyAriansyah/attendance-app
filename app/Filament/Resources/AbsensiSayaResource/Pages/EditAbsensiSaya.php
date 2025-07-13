<?php

namespace App\Filament\Resources\AbsensiSayaResource\Pages;

use App\Filament\Resources\AbsensiSayaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAbsensiSaya extends EditRecord
{
    protected static string $resource = AbsensiSayaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
