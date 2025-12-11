<?php

namespace App\Filament\Resources\FilaOrcamentos\Pages;

use App\Filament\Resources\FilaOrcamentos\FilaOrcamentoResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditFilaOrcamento extends EditRecord
{
    protected static string $resource = FilaOrcamentoResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
