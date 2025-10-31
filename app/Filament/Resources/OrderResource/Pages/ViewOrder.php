<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Enum\ShippingStatusEnum;
use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('updateShippingStatus')
                ->label('Update Shipping Status')
                ->icon('heroicon-o-truck')
                ->color('warning')
                ->form([
                    Forms\Components\Select::make('shipping_status')
                        ->label('Shipping Status')
                        ->options([
                            ShippingStatusEnum::PENDING->value => 'Pending',
                            ShippingStatusEnum::IN_PROGRESS->value => 'In Progress',
                            ShippingStatusEnum::IN_THE_WAY->value => 'In The Way',
                            ShippingStatusEnum::DELIVERED->value => 'Delivered',
                        ])
                        ->default(fn () => $this->record->shipping_status)
                        ->required()
                        ->native(false),
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'shipping_status' => $data['shipping_status'],
                    ]);

                    Notification::make()
                        ->title('Shipping Status Updated')
                        ->success()
                        ->body('The shipping status has been updated to ' . ucwords(str_replace('_', ' ', $data['shipping_status'])))
                        ->send();
                }),

            Actions\Action::make('updatePaymentStatus')
                ->label('Update Payment Status')
                ->icon('heroicon-o-credit-card')
                ->color('success')
                ->form([
                    Forms\Components\Select::make('payment_status')
                        ->label('Payment Status')
                        ->options([
                            'pending' => 'Pending',
                            'paid' => 'Paid',
                            'failed' => 'Failed',
                            'refunded' => 'Refunded',
                        ])
                        ->default(fn () => $this->record->payment_status)
                        ->required()
                        ->native(false),
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'payment_status' => $data['payment_status'],
                    ]);

                    Notification::make()
                        ->title('Payment Status Updated')
                        ->success()
                        ->body('The payment status has been updated to ' . ucfirst($data['payment_status']))
                        ->send();
                }),

        ];
    }
}
