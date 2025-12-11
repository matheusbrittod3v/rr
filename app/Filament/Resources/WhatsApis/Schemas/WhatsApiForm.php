<?php

namespace App\Filament\Resources\WhatsApis\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class WhatsApiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Name')
                    ->required(),
                TextInput::make('host')
                    ->label('Host')
                    ->default('http://127.0.0.1:3000/')
                    ->required(),
                TextInput::make('key')
                    ->label('Key')
                    ->required(),
                Select::make('type')
                    ->label('Type')
                    ->options([
                        'string' => 'String',
                    ])
                    ->default('string')
                    ->required(),
                Select::make('authenticate')
                    ->label('Authentication Type')
                    ->options([
                        'bearer' => 'Bearer Token',
                        'x-api-key' => 'X-API-Key',
                        'basic' => 'Basic Auth',
                        'oauth' => 'OAuth',
                    ])
                    ->default('x-api-key')
                    ->required(),
                Hidden::make('instance_name'),
                Hidden::make('numero_instancia'),
                Hidden::make('connection_status'),
                // user_id is automatically set in the CreateWhatsApi page
            ]);
    }
}