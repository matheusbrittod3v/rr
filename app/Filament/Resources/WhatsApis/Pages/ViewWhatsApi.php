<?php

namespace App\Filament\Resources\WhatsApis\Pages;

use App\Filament\Resources\WhatsApis\Actions\TestApiAction;
use App\Filament\Resources\WhatsApis\WhatsApiResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewWhatsApi extends ViewRecord
{
    protected static string $resource = WhatsApiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            TestApiAction::make(),
            EditAction::make(),
        ];
    }
}