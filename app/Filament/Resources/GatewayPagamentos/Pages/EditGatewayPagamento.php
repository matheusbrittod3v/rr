<?php

namespace App\Filament\Resources\GatewayPagamentos\Pages;

use App\Filament\Resources\GatewayPagamentos\GatewayPagamentoResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditGatewayPagamento extends EditRecord
{
    protected static string $resource = GatewayPagamentoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Remover o user_id do formulário de edição para evitar alterações
        unset($data['user_id']);
        
        return $data;
    }
}