<?php

namespace App\Filament\Resources\EmailConfigs\Pages;

use App\Filament\Resources\EmailConfigs\Actions\TestEmailAction;
use App\Filament\Resources\EmailConfigs\EmailConfigResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditEmailConfig extends EditRecord
{
    protected static string $resource = EmailConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            TestEmailAction::make(),
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
