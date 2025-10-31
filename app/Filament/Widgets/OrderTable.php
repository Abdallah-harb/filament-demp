<?php

namespace App\Filament\Widgets;

use App\Enum\PaymentStatusEnum;
use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class OrderTable extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Recent Orders';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()
                    ->with(['user', 'orderDetails'])
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Order #')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_quantity')
                    ->label('Items')
                    ->alignCenter()
                    ->getStateUsing(fn ($record) => $record->orderDetails->sum('quantity')),

                Tables\Columns\TextColumn::make('sub_total_amount')
                    ->label('Subtotal')
                    ->money('EGP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('discount_amount')
                    ->label('Discount')
                    ->money('EGP')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('EGP')
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Payment')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Payment Status')
                    ->badge()
                    ->alignCenter()
                    ->color(fn (string $state): string => match ($state) {
                        PaymentStatusEnum::PENDING->value => 'warning',
                        PaymentStatusEnum::PAID->value => 'success',
                        PaymentStatusEnum::FAILED->value => 'danger',
                        PaymentStatusEnum::REFUNDED->value => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                Tables\Columns\TextColumn::make('shipping_status')
                    ->label('Shipping Status')
                    ->badge()
                    ->alignCenter()
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state))),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50]);
    }
}
