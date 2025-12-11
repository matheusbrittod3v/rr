<?php

namespace App\Filament\Resources\Services\Pages;

use App\Filament\Resources\Services\ServicesResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditServices extends EditRecord
{
    protected static string $resource = ServicesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
