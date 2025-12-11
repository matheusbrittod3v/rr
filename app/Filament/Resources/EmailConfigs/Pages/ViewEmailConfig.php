<?php

namespace App\Filament\Resources\EmailConfigs\Pages;

use App\Filament\Resources\EmailConfigs\Actions\TestEmailAction;
use App\Filament\Resources\EmailConfigs\EmailConfigResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewEmailConfig extends ViewRecord
{
    protected static string $resource = EmailConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            TestEmailAction::make(),
            EditAction::make(),
        ];
    }
}
