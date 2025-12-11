<?php

namespace App\Filament\Resources\FilaPrestadores\Schemas;

use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class FilaPrestadorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Fila de Prestador')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id')
                            ->label('ID'),
                        TextEntry::make('user.name')
                            ->label('Usuário'),
                        TextEntry::make('position')
                            ->label('Posição'),
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
