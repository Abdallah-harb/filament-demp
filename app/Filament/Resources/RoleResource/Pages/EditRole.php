<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use App\Models\Permission;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['permissions'] = $this->record->permissions->pluck('id')->toArray();

        return $data;
    }

    protected function afterSave(): void
    {
        if (isset($this->data['permissions'])) {
            $permissions = Permission::whereIn('id', $this->data['permissions'])->get();
            $this->record->syncPermissions($permissions);
        }
    }
}
