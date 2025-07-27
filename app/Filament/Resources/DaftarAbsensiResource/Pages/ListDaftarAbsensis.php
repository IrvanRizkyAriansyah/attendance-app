<?php

namespace App\Filament\Resources\DaftarAbsensiResource\Pages;

use App\Filament\Resources\DaftarAbsensiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDaftarAbsensis extends ListRecords
{
    protected static string $resource = DaftarAbsensiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
