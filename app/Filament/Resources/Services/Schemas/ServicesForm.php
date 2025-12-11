<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Models\Marca;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ServicesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('marca_id')
                    ->label('Marca')
                    ->relationship('marca', 'nome')
                    ->required(),
                FileUpload::make('photo_patch')
                    ->label('Imagem do Serviço')
                    ->image()
                    ->directory('services'),
                TextInput::make('nome_servico')
                    ->label('Nome do Serviço')
                    ->required(),
                Textarea::make('descricao')
                    ->label('Descrição')
                    ->columnSpanFull(),
                Repeater::make('customFields')
                    ->label('Campos Personalizados')
                    ->relationship()
                    ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                        // Preserve existing parametros_campo data and update only the field_name and field_type
                        $parametrosCampo = $data['parametros_campo'] ?? [];
                        $parametrosCampo['field_name'] = $data['field_name'] ?? '';
                        $parametrosCampo['field_type'] = $data['field_type'] ?? 'text';
                        $data['parametros_campo'] = $parametrosCampo;
                        return $data;
                    })
                    ->mutateRelationshipDataBeforeFillUsing(function (array $data): array {
                        // Extract field data from parametros_campo for the form
                        if (isset($data['parametros_campo'])) {
                            $params = $data['parametros_campo'];
                            $data['field_name'] = $params['field_name'] ?? '';
                            $data['field_type'] = $params['field_type'] ?? 'text';
                        }
                        return $data;
                    })
                    ->schema([
                        TextInput::make('field_name')
                            ->label('Nome do Campo')
                            ->required(),
                        Select::make('field_type')
                            ->label('Tipo do Campo')
                            ->options([
                                'text' => 'Texto',
                                'number' => 'Numérico',
                            ])
                            ->required(),
                    ])
                    ->addActionLabel('Adicionar Campo')
                    ->itemLabel(fn (array $state): string => $state['field_name'] ?? '')
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull(),
            ]);
    }
}