<?php

namespace App\Filament\Resources\WhatsApis\Pages;

use App\Filament\Resources\WhatsApis\Actions\TestApiAction;
use App\Filament\Resources\WhatsApis\WhatsApiResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditWhatsApi extends EditRecord
{
    protected static string $resource = WhatsApiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            TestApiAction::make(),
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}