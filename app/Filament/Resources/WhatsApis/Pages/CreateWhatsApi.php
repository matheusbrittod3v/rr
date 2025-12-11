<?php

namespace App\Filament\Resources\WhatsApis\Pages;

use App\Filament\Resources\WhatsApis\WhatsApiResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use App\Models\WhatsApi;

class CreateWhatsApi extends CreateRecord
{
    protected static string $resource = WhatsApiResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        
        return $data;
    }
    
    protected function beforeFill(): void
    {
        // Check if the user already has a WhatsApp API configuration
        if (WhatsApi::userHasConfig(Auth::id())) {
            $this->notify('warning', 'You already have a WhatsApp API configuration. You can only have one WhatsApp API configuration per user.');
        }
    }
}
