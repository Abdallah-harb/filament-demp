<?php

namespace App\Filament\Widgets;

use App\Enum\ShippingStatusEnum;
use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrderPieChart extends ChartWidget
{
    protected static ?string $heading = 'Orders by Shipping Status';

    protected static ?int $sort = 3;
    protected function getMaxHeight(): ?string
    {
        return '275px';
    }

    protected function getData(): array
    {
        // Get count of orders for each shipping status
        $pending = Order::where('shipping_status', ShippingStatusEnum::PENDING->value)->count();
        $inProgress = Order::where('shipping_status', ShippingStatusEnum::IN_PROGRESS->value)->count();
        $inTheWay = Order::where('shipping_status', ShippingStatusEnum::IN_THE_WAY->value)->count();
        $delivered = Order::where('shipping_status', ShippingStatusEnum::DELIVERED->value)->count();

        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => [$pending, $inProgress, $inTheWay, $delivered],
                    'backgroundColor' => [
                        'rgb(251, 191, 36)', // Yellow for Pending
                        'rgb(59, 130, 246)',  // Blue for In Progress
                        'rgb(249, 115, 22)',  // Orange for In The Way
                        'rgb(34, 197, 94)',   // Green for Delivered
                    ],
                    'borderColor' => [
                        'rgb(251, 191, 36)',
                        'rgb(59, 130, 246)',
                        'rgb(249, 115, 22)',
                        'rgb(34, 197, 94)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => ['Pending', 'In Progress', 'In The Way', 'Delivered'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
