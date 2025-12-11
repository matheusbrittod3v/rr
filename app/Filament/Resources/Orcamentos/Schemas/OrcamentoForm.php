<?php

namespace App\Filament\Resources\Orcamentos\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class OrcamentoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required(),
                TextInput::make('numero')
                    ->label('Número/Telefone')
                    ->required(),
                TextInput::make('valor')
                    ->label('Valor')
                    ->numeric()
                    ->prefix('R$')
                    ->nullable(),
                Select::make('aceito')
                    ->label('Status')
                    ->options([
                        'sim' => 'Aceito',
                        'nao' => 'Não Aceito',
                    ])
                    ->default('nao')
                    ->required(),
                Textarea::make('informacoes_adicionais')
                    ->label('Informações Adicionais')
                    ->nullable(),
            ]);
    }
}
