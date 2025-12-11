<?php

namespace App\Filament\Resources\EmailConfigs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class EmailConfigForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('host')
                    ->label('Host')
                    ->required(),
                TextInput::make('port')
                    ->label('Port')
                    ->required()
                    ->numeric(),
                Select::make('encryption_type')
                    ->label('Encryption Type')
                    ->options([
                        'ssl' => 'SSL',
                        'tls' => 'TLS',
                    ])
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required(),
                Select::make('type')
                    ->label('Type')
                    ->options([
                        'smtp' => 'SMTP',
                    ])
                    ->required()
                    ->default('smtp'),
            ]);
    }
}