<?php

namespace App\Filament\Resources\GatewayPagamentos\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GatewayPagamentoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('url')
                    ->url()
                    ->required(),
                TextInput::make('token')
                    ->default(null),
            ]);
    }
}
