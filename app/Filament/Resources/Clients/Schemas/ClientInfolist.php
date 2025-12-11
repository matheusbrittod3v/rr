<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;

class ClientInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados do Cliente')
                    ->schema([
                        TextEntry::make('email')
                            ->label('Email')
                            ->icon('heroicon-o-envelope'),
                        TextEntry::make('numero')
                            ->label('WhatsApp / Telefone')
                            ->icon('heroicon-o-phone'),
                    ])
                    ->columns(2),
                    
                Section::make('Última Atividade')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Último Orçamento Criado')
                            ->dateTime('d/m/Y H:i:s'),
                        TextEntry::make('id')
                            ->label('ID do Orçamento Referência'),
                    ])
                    ->columns(2),
            ]);
    }
}
