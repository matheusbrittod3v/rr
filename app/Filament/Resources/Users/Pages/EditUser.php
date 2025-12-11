<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            // Removed DeleteAction to prevent deletion of users
        ];
    }
    
    // Prevent deletion of users
    public function delete(): never
    {
        throw new \Exception('User deletion is not allowed.');
    }
}