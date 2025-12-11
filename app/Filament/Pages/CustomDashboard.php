<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\BudgetConversionChart;
use App\Filament\Widgets\LatestBudgetsWidget;
use App\Filament\Widgets\PaymentsChart;
use App\Filament\Widgets\RevenueChart;
use App\Filament\Widgets\StatsOverviewWidget;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Gate;

class CustomDashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            StatsOverviewWidget::class,
            RevenueChart::class,
            BudgetConversionChart::class,
            PaymentsChart::class,
            LatestBudgetsWidget::class,
        ];
    }

    public function getColumns(): int | array
    {
        return 2;
    }
    
    public static function canAccess(): bool
    {
        return Gate::allows('view', CustomDashboard::class);
    }
}