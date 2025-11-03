<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Mail\PasswordUserMail;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    //before saving to database
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $randomPassword = bin2hex(random_bytes(6));
        $data['email_verified_at'] = now();
        $data['password'] = $randomPassword;
        $this->randomPassword = $randomPassword;

        // Store role temporarily and remove from data array
        $this->roleId = $data['role'] ?? null;
        unset($data['role']);

        return $data;
    }

    protected function afterCreate(): void
    {
        if ($this->roleId) {
            $role = \App\Models\Role::find($this->roleId);
            if ($role) {
                $this->record->assignRole($role->name);
            }
        }

        // Send email with password
        Mail::to($this->record->email)->send(
            new PasswordUserMail($this->record, $this->randomPassword)
        );
    }
}
