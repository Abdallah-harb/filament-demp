<?php

namespace App\Filament\Resources;

use App\Enum\PaymentStatusEnum;
use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?int $navigationSort = 5;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Order Information')
                    ->schema([
                        Forms\Components\TextInput::make('order_number')
                            ->label('Order Number')
                            ->disabled(),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Customer')
                            ->disabled(),
                        Forms\Components\Select::make('payment_method')
                            ->label('Payment Method')
                            ->disabled(),
                        Forms\Components\Select::make('payment_status')
                            ->label('Payment Status')
                            ->disabled(),
                        Forms\Components\Select::make('shipping_status')
                            ->label('Shipping Status')
                            ->disabled(),
                        Forms\Components\Textarea::make('shipping_address')
                            ->label('Shipping Address')
                            ->disabled()
                            ->rows(2),
                    ])->columns(2),

                Forms\Components\Section::make('Order Summary')
                    ->schema([
                        Forms\Components\TextInput::make('sub_total_amount')
                            ->label('Subtotal')
                            ->prefix('EGP')
                            ->disabled(),
                        Forms\Components\TextInput::make('discount_amount')
                            ->label('Discount')
                            ->prefix('EGP')
                            ->disabled(),
                        Forms\Components\TextInput::make('total_amount')
                            ->label('Total Amount')
                            ->prefix('EGP')
                            ->disabled(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('order_number')->sortable()->searchable()->badge(),
                Tables\Columns\TextColumn::make('user.name')->label('User')->searchable(),
                Tables\Columns\TextColumn::make('total_quantity')
                    ->label('Quantity')
                    ->alignCenter()
                    ->getStateUsing(fn ($record) => $record->orderDetails->sum('quantity')),
                Tables\Columns\TextColumn::make('total_amount')->label('Total')->money('EGP')->sortable(),
                Tables\Columns\TextColumn::make('discount_amount')->label('Discount')->money('EGP')->sortable(),
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
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Payment Method')->alignCenter()->badge(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Order Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('order_number')
                            ->label('Order Number')
                            ->badge()
                            ->color('success'),
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Customer'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Order Date')
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('payment_method')
                            ->label('Payment Method')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                        Infolists\Components\TextEntry::make('payment_status')
                            ->label('Payment Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'paid' => 'success',
                                'failed' => 'danger',
                                'refunded' => 'info',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                        Infolists\Components\TextEntry::make('shipping_status')
                            ->label('Shipping Status')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state))),
                        Infolists\Components\TextEntry::make('shipping_address')
                            ->label('Shipping Address')
                            ->columnSpanFull(),
                    ])->columns(3),

                Infolists\Components\Section::make('Order Items')
                    ->schema([
                        Infolists\Components\ViewEntry::make('orderDetails')
                            ->label('')
                            ->view('filament.infolists.order-items-table'),
                    ]),

                Infolists\Components\Section::make('Order Summary')
                    ->schema([
                        Infolists\Components\TextEntry::make('sub_total_amount')
                            ->label('Subtotal')
                            ->money('EGP'),
                        Infolists\Components\TextEntry::make('discount_amount')
                            ->label('Discount')
                            ->money('EGP'),
                        Infolists\Components\TextEntry::make('total_amount')
                            ->label('Total Amount')
                            ->money('EGP')
                            ->weight('bold')
                            ->size('lg'),
                    ])->columns(3),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
