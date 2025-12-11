<?php

namespace App\Filament\Resources\FilaPrestadores\Pages;

use App\Filament\Resources\FilaPrestadores\FilaPrestadorResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFilaPrestadores extends ListRecords
{
    protected static string $resource = FilaPrestadorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
