<?php

namespace App\Filament\Resources\GatewayPagamentos\Pages;

use App\Filament\Resources\GatewayPagamentos\GatewayPagamentoResource;
use App\Models\GatewayPagamento;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListGatewayPagamentos extends ListRecords
{
    protected static string $resource = GatewayPagamentoResource::class;

    protected function getHeaderActions(): array
    {
        // Verificar se o usuário já tem um gateway registrado
        $hasGateway = GatewayPagamento::where('user_id', Auth::id())->exists();
        
        // Se já tiver um gateway, não mostrar o botão de criação
        if ($hasGateway) {
            return [];
        }
        
        return [
            CreateAction::make(),
        ];
    }
}