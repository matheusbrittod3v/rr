<?php

namespace App\Filament\Resources\Banners\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class BannerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('nome')
                    ->label('Nome do Banner'),

                TextEntry::make('ordem')
                    ->label('Ordem de Exibição')
                    ->badge()
                    ->color('info'),

                TextEntry::make('ativo')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Ativo' : 'Inativo')
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger'),

                ImageEntry::make('imagens')
                    ->label('Imagens do Banner')
                    ->disk('public')
                    ->circular()
                    ->stacked()
                    ->limit(5),

                TextEntry::make('quantidade_imagens')
                    ->label('Total de Imagens')
                    ->badge()
                    ->color('success'),

                TextEntry::make('tempo_rotacao')
                    ->label('Tempo de Rotação')
                    ->suffix(' ms')
                    ->badge()
                    ->color('warning'),

                TextEntry::make('tempo_rotacao_segundos')
                    ->label('Tempo em Segundos')
                    ->suffix(' segundos')
                    ->badge()
                    ->color('info'),

                TextEntry::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-'),

                TextEntry::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-'),
            ]);
    }
}
