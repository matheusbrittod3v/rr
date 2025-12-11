<?php

namespace App\Filament\Resources\EmailConfigs\Pages;

use App\Filament\Resources\EmailConfigs\EmailConfigResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use App\Models\EmailConfig;

class CreateEmailConfig extends CreateRecord
{
    protected static string $resource = EmailConfigResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        
        return $data;
    }
}