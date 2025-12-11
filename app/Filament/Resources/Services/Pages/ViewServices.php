<?php

namespace App\Filament\Resources\Services\Pages;

use App\Filament\Resources\Services\ServicesResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewServices extends ViewRecord
{
    protected static string $resource = ServicesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
