<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CardsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalOrders = Order::count();
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total_amount');
        $totalRefund = Order::where('payment_status', 'refunded')->sum('total_amount');
        $totalProducts = Product::count();

        return [
            Stat::make('Total Orders', $totalOrders)
                ->description('All orders placed')
                ->icon('heroicon-o-shopping-bag')
                ->color('primary'),

            Stat::make('Total Revenue', number_format($totalRevenue, 2) . ' EGP')
                ->description('Total paid orders')
                ->icon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make('Total Refunds', number_format($totalRefund, 2) . ' EGP')
                ->description('Refunded order amount')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('danger'),

            Stat::make('Total Products', $totalProducts)
                ->description('Products available')
                ->icon('heroicon-o-cube')
                ->color('info'),
        ];
    }
}
