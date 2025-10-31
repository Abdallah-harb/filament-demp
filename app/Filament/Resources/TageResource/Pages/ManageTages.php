<?php

namespace App\Filament\Resources\TageResource\Pages;

use App\Filament\Resources\TageResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTages extends ManageRecords
{
    protected static string $resource = TageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
