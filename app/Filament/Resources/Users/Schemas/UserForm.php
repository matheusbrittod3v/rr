<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Role;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('numero')
                    ->label('Phone Number')
                    ->tel()
                    ->extraInputAttributes(['class' => 'phone-mask-input'])
                    ->nullable(),
                Toggle::make('is_provider')
                    ->label('Este usuário é um técnico?')
                    ->inline(false),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->helperText(fn (string $operation): ?string => $operation === 'edit' ? 'Deixe em branco para manter a senha atual. Digite apenas se desejar alterá-la.' : null),
                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->options(Role::all()->pluck('name', 'id'))
            ]);
    }
}