<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class OrderChart extends ChartWidget
{
    protected static ?string $heading = 'Monthly Revenue (Paid Orders)';

    protected static ?int $sort = 2;

    public ?string $filter = null;

    protected function getData(): array
    {
        // Get the selected year or default to current year
        $year = $this->filter ?? now()->year;

        // Initialize arrays for labels and data
        $labels = [];
        $data = [];

        // Loop through each month
        for ($month = 1; $month <= 12; $month++) {
            $monthName = Carbon::create($year, $month, 1)->format('M');
            $labels[] = $monthName;

            // Get total paid orders for this month
            $total = Order::query()
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->where('payment_status', 'paid')
                ->sum('total_amount');

            $data[] = $total;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (EGP)',
                    'data' => $data,
                    'borderColor' => 'rgb(75, 192, 192)',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        $currentYear = now()->year;
        $years = [];

        for ($year = 2020; $year <= $currentYear; $year++) {
            $years[$year] = $year;
        }

        return $years;
    }
}
