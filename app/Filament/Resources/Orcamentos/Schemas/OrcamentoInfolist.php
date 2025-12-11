<?php

namespace App\Filament\Resources\Orcamentos\Schemas;

use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OrcamentoInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informações do Orçamento')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id')
                            ->label('ID'),
                        TextEntry::make('numero')
                            ->label('Número/Telefone'),
                        TextEntry::make('email')
                            ->label('Email'),
                        TextEntry::make('valor')
                            ->label('Valor')
                            ->money('BRL'),
                        TextEntry::make('aceito')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state === 'sim' ? 'Aceito' : 'Não Aceito')
                            ->color(fn ($state) => $state === 'sim' ? 'success' : 'danger'),
                        TextEntry::make('created_at')
                            ->label('Criado em')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Atualizado em')
                            ->dateTime(),
                    ]),
                Section::make('Informações Adicionais')
                    ->schema([
                        TextEntry::make('informacoes_adicionais')
                            ->label('Dados Adicionais')
                            ->formatStateUsing(fn ($state) => is_string($state) ? $state : json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))
                            ->html(),
                    ])
                    ->collapsed(),
            ]);
    }
}
