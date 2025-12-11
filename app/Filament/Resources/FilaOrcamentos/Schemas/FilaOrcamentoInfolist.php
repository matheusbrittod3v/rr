<?php

namespace App\Filament\Resources\FilaOrcamentos\Schemas;

use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class FilaOrcamentoInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Fila de Orçamento')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id')
                            ->label('ID'),
                        TextEntry::make('orcamento.id')
                            ->label('ID do Orçamento'),
                        TextEntry::make('prestador.name')
                            ->label('Prestador'),
                        TextEntry::make('created_at')
                            ->label('Criado em')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Atualizado em')
                            ->dateTime(),
                    ]),
            ]);
    }
}
