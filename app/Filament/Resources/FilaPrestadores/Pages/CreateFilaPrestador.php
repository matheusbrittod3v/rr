<?php

namespace App\Filament\Resources\FilaPrestadores\Pages;

use App\Filament\Resources\FilaPrestadores\FilaPrestadorResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateFilaPrestador extends CreateRecord
{
    protected static string $resource = FilaPrestadorResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
