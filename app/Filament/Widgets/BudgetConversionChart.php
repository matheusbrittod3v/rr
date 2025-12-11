<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class BudgetConversionChart extends ChartWidget
{
    protected ?string $heading = 'Proporção: Pagos vs Não Pagos';
    
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 1; 

    protected function getData(): array
    {
        $user = auth()->user();
        $canViewAll = $user && ($user->can('view_all_data') || $user->hasRole('admin'));

        $queryPagos = Payment::whereIn('status', ['pago', 'success']);
        $queryOutros = Payment::whereNotIn('status', ['pago', 'success']);
        
        if (!$canViewAll) {
            $queryPagos->where('email', $user->email);
            $queryOutros->where('email', $user->email);
        }

        $pagos = $queryPagos->count();
        $outros = $queryOutros->count();
        
        return [
            'labels' => ['Pagos', 'Pendentes/Cancelados'],
            'datasets' => [
                [
                    'label' => 'Orçamentos',
                    'data' => [$pagos, $outros],
                    'backgroundColor' => [
                        '#10b981', // Emerald 500 (Success)
                        '#f43f5e', // Rose 500 (Danger)
                    ],
                    'hoverOffset' => 4,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
