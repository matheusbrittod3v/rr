<?php

namespace App\Filament\Resources\GatewayPagamentos\Pages;

use App\Filament\Resources\GatewayPagamentos\GatewayPagamentoResource;
use App\Models\GatewayPagamento;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateGatewayPagamento extends CreateRecord
{
    protected static string $resource = GatewayPagamentoResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        
        return $data;
    }
}