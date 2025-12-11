<?php

namespace App\Filament\Resources\EmailConfigs\Pages;

use App\Filament\Resources\EmailConfigs\Actions\TestEmailAction;
use App\Filament\Resources\EmailConfigs\EmailConfigResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEmailConfigs extends ListRecords
{
    protected static string $resource = EmailConfigResource::class;

    protected function getHeaderActions(): array
    {
        // Check if user already has an email configuration
        if (\App\Models\EmailConfig::userHasConfig(auth()->id())) {
            // If they do, show the Test Email action
            return [
                TestEmailAction::make()
                    ->label('Test Email Configuration')
                    ->icon('heroicon-o-paper-airplane'),
            ];
        } else {
            // If they don't, show the Create action
            return [
                CreateAction::make(),
            ];
        }
    }
}
