<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Profile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'My Profile';
    protected static ?string $title = 'My Profile';
    protected static string $view = 'filament.pages.profile';
    protected static ?int $navigationSort = 100;

    public ?array $profileData = [];
    public ?array $passwordData = [];

    public function mount(): void
    {
        $this->fillForms();
    }

    protected function fillForms(): void
    {
        $user = Auth::user();

        $this->profileData = [
            'name' => $user->name,
            'email' => $user->email,
        ];

        $this->passwordData = [
            'current_password' => '',
            'password' => '',
            'password_confirmation' => '',
        ];
    }

    protected function getForms(): array
    {
        return [
            'profileForm',
            'passwordForm',
        ];
    }

    public function profileForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Profile Information')
                    ->description('Update your account profile information.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->unique(ignorable: Auth::user())
                            ->maxLength(255),
                    ])
            ])
            ->statePath('profileData');
    }

    public function passwordForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Update Password')
                    ->description('Ensure your account is using a long, random password to stay secure.')
                    ->schema([
                        Forms\Components\TextInput::make('current_password')
                            ->label('Current Password')
                            ->password()
                            ->required()
                            ->currentPassword()
                            ->revealable(),
                        Forms\Components\TextInput::make('password')
                            ->label('New Password')
                            ->password()
                            ->required()
                            ->minLength(8)
                            ->confirmed()
                            ->revealable()
                            ->validationMessages([
                                'min' => 'Password must be at least 8 characters.',
                            ]),
                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Confirm New Password')
                            ->password()
                            ->required()
                            ->revealable(),
                    ])
            ])
            ->statePath('passwordData');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('updateProfile')
                ->label('Update Profile')
                ->action('updateProfile'),
            Action::make('updatePassword')
                ->label('Update Password')
                ->action('updatePassword')
                ->color('warning'),
        ];
    }

    public function updateProfile(): void
    {
        $data = $this->profileForm->getState();

        $user = Auth::user();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->save();

        Notification::make()
            ->success()
            ->title('Profile Updated')
            ->body('Your profile information has been updated successfully.')
            ->send();
    }

    public function updatePassword(): void
    {
        $data = $this->passwordForm->getState();

        $user = Auth::user();

        if (!Hash::check($data['current_password'], $user->password)) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('The current password is incorrect.')
                ->send();
            return;
        }

        $user->password = Hash::make($data['password']);
        $user->save();

        // Clear the password form
        $this->passwordData = [
            'current_password' => '',
            'password' => '',
            'password_confirmation' => '',
        ];

        Notification::make()
            ->success()
            ->title('Password Updated')
            ->body('Your password has been updated successfully.')
            ->send();
    }
}

