<?php

namespace App\Filament\Resources\FilaOrcamentos\Pages;

use App\Filament\Resources\FilaOrcamentos\FilaOrcamentoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFilaOrcamentos extends ListRecords
{
    protected static string $resource = FilaOrcamentoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
