<?php

namespace App\Filament\Resources\GatewayPagamentos\Pages;

use App\Filament\Resources\GatewayPagamentos\GatewayPagamentoResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewGatewayPagamento extends ViewRecord
{
    protected static string $resource = GatewayPagamentoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
