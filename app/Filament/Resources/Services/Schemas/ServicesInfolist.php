<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ServicesInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('marca.nome')
                    ->label('Marca'),
                ImageEntry::make('photo_patch')
                    ->disk('public')
                    ->label('Imagem do Serviço')
                    ->placeholder('-'),
                TextEntry::make('nome_servico')
                    ->label('Nome do Serviço'),
                TextEntry::make('descricao')
                    ->label('Descrição')
                    ->html(),
                RepeatableEntry::make('customFields')
                    ->label('Campos Personalizados')
                    ->schema([
                        TextEntry::make('parametros_campo.field_name')
                            ->label('Nome do Campo'),
                        TextEntry::make('parametros_campo.field_type')
                            ->label('Tipo do Campo'),
                    ])
                    ->columns(2),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}