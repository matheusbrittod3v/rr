<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class RevenueChart extends ChartWidget
{
    protected ?string $heading = 'Receita Diária (Últimos 15 Dias)';
    
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $user = auth()->user();
        $canViewAll = $user && ($user->can('view_all_data') || $user->hasRole('admin'));

        $query = Payment::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(valor) as total'))
            ->whereIn('status', ['pago', 'success'])
            ->where('created_at', '>=', now()->subDays(15));
            
        if (!$canViewAll) {
            if ($user->isProvider()) {
                $query->whereHas('orcamento', fn($q) => $q->where('prestador_id', $user->id));
            } else {
                $query->where('email', $user->email);
            }
        }

        $data = $query->groupBy('date')
            ->orderBy('date')
            ->get();

        $labels = [];
        $values = [];
        
        foreach ($data as $row) {
            $labels[] = Carbon::parse($row->date)->format('d/m');
            $values[] = $row->total;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Receita (R$)',
                    'data' => $values,
                    'borderColor' => '#10b981', // Emerald 500
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
