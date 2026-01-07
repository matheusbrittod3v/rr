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
                \Filament\Forms\Components\Toggle::make('debug_mode')
                    ->label('Modo Debug (Aprovação Automática)')
                    ->helperText('Quando ativo, pagamentos serão aprovados automaticamente sem validar na gateway.')
                    ->onColor('success')
                    ->offColor('danger')
                    ->default(false),
            ]);
    }
}
