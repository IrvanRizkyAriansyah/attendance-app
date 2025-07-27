<?php

namespace App\Filament\Resources\SlipGajiResource\Pages;

use App\Filament\Resources\SlipGajiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSlipGaji extends EditRecord
{
    protected static string $resource = SlipGajiResource::class;

    public static function canAccess(array $parameters = []): bool
    {
        return auth()->user()?->role === 'keuangan';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
