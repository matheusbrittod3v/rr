<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class PaymentsChart extends ChartWidget
{
    protected ?string $heading = 'Status dos Pagamentos';
    
    protected static ?int $sort = 4;
    
    protected function getData(): array
    {
        $user = auth()->user();
        $canViewAll = $user && ($user->can('view_all_data') || $user->hasRole('admin'));

        $query = Payment::select('status', DB::raw('count(*) as total'))
            ->groupBy('status');
            
        if (!$canViewAll) {
            if ($user->isProvider()) {
                $query->whereHas('orcamento', fn($q) => $q->where('prestador_id', $user->id));
            } else {
                $query->where('email', $user->email);
            }
        }
        
        $data = $query->get();

        return [
            'datasets' => [
                [
                    'label' => 'Pagamentos',
                    'data' => $data->pluck('total')->toArray(),
                    'backgroundColor' => [
                        '#10b981', // pago - success
                        '#eab308', // processando - warning
                        '#ef4444', // nao pago - danger
                        '#6b7280', // refund/outros - gray
                    ],
                ],
            ],
            'labels' => $data->pluck('status')->map(fn($s) => ucfirst($s))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
