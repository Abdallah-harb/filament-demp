<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Profile Information Form --}}
        <x-filament::card>
            <form wire:submit.prevent="updateProfile">
                {{ $this->profileForm }}

                <div class="mt-6">
                    <x-filament::button type="submit" color="primary">
                        Update Profile
                    </x-filament::button>
                </div>
            </form>
        </x-filament::card>

        {{-- Update Password Form --}}
        <x-filament::card>
            <form wire:submit.prevent="updatePassword">
                {{ $this->passwordForm }}

                <div class="mt-6">
                    <x-filament::button type="submit" color="warning">
                        Update Password
                    </x-filament::button>
                </div>
            </form>
        </x-filament::card>
    </div>
</x-filament-panels::page>

