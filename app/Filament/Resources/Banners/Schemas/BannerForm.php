<?php

namespace App\Filament\Resources\Banners\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nome')
                    ->label('Nome do Banner')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Ex: Banner Principal'),

                TextInput::make('ordem')
                    ->label('Ordem de Exibição')
                    ->numeric()
                    ->default(0)
                    ->helperText('Quanto menor o número, mais acima aparece'),

                FileUpload::make('imagens')
                    ->label('Imagens do Banner')
                    ->image()
                    ->multiple()
                    ->reorderable()
                    ->disk('public')
                    ->visibility('public')
                    ->directory('banners')
                    ->helperText('Faça upload de múltiplas imagens. Arraste para reordenar.')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                    ->maxSize(5120), // 5MB

                TextInput::make('tempo_rotacao')
                    ->label('Tempo de Rotação (milissegundos)')
                    ->numeric()
                    ->default(5000)
                    ->minValue(1000)
                    ->maxValue(30000)
                    ->suffix('ms')
                    ->helperText('Tempo entre cada troca de imagem (1000ms = 1 segundo). Mínimo: 1s, Máximo: 30s'),

                Toggle::make('ativo')
                    ->label('Banner Ativo')
                    ->default(true)
                    ->helperText('Desative para esconder o banner sem excluí-lo')
                    ->onColor('success')
                    ->offColor('danger'),
            ]);
    }
}
