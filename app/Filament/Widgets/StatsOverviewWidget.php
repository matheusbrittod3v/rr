<?php

namespace App\Filament\Widgets;

use App\Models\ChatRoom;
use App\Models\Orcamento;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $user = auth()->user();
        $canViewAll = $user && ($user->can('view_all_data') || $user->hasRole('admin'));

        // Helpers de Query
        $paymentQuery = Payment::query();
        $chatQuery = ChatRoom::query();
        $orcamentoQuery = Orcamento::query();
        
        if (!$canViewAll) {
            $paymentQuery->where('email', $user->email);
            // ChatRoom Filter
            $chatQuery->whereHas('payment', fn($q) => $q->where('email', $user->email));
            // Orcamento Filter
            $orcamentoQuery->where('email', $user->email);
        }

        // 1. Total Geral de Orçamentos
        $totalOrcamentos = (clone $orcamentoQuery)->count();
        
        // 2. Orçamentos Pagos (Sucesso)
        $orcamentosPagos = (clone $paymentQuery)->whereIn('status', ['pago', 'success'])->count();
        
        // 3. Orçamentos Não Pagos (Pendentes/Falhas)
        // Aproximação baseada em contagem filtrada
        $orcamentosNaoPagos = $totalOrcamentos - $orcamentosPagos;
        if ($orcamentosNaoPagos < 0) $orcamentosNaoPagos = 0;
        
        // 4. Taxa de Conversão
        $taxaConversao = $totalOrcamentos > 0 ? ($orcamentosPagos / $totalOrcamentos) * 100 : 0;
        
        return [
            Stat::make('Total de Orçamentos', $totalOrcamentos)
                ->description('Todos os orçamentos gerados')
                ->descriptionIcon('heroicon-m-document-duplicate')
                ->color('primary'),

            Stat::make('Orçamentos Pagos', $orcamentosPagos)
                ->description('Convertidos em venda')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Pendentes / Não Pagos', $orcamentosNaoPagos)
                ->description('Aguardando pagamento')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger'),
                
            Stat::make('Taxa de Conversão', number_format($taxaConversao, 1) . '%')
                ->description('Proporção Pagos vs Total')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color($taxaConversao > 50 ? 'success' : 'warning'),
        ];
    }
}
