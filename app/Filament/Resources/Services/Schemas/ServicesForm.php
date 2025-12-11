<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

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
                    ->directory('services')
                    ->storeFiles(true)
                    ->saveUploadedFileUsing(function (\Livewire\Features\SupportFileUploads\TemporaryUploadedFile $file) {
                        // Store in the default location (storage/app/public/services)
                        $storedPath = $file->store('services', 'public');
                        
                        // Also copy to public/images directory for direct access
                        $filename = basename($storedPath);
                        $sourcePath = storage_path('app/public/' . $storedPath);
                        $destinationPath = public_path('images/' . $filename);
                        
                        // Create directory if it doesn't exist
                        if (!file_exists(public_path('images'))) {
                            mkdir(public_path('images'), 0755, true);
                        }
                        
                        // Copy the file
                        copy($sourcePath, $destinationPath);
                        
                        // Return path relative to images directory
                        return $filename;
                    }),
                TextInput::make('nome_servico')
                    ->label('Nome do Serviço')
                    ->required(),
                Textarea::make('descricao')
                    ->label('Descrição')
                    ->columnSpanFull(),
                Repeater::make('customFields')
                    ->label('Campos Personalizados')
                    ->relationship()
                    ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                        // On create, structure the data correctly
                        $data['parametros_campo'] = [
                            'field_name' => $data['field_name'] ?? '',
                            'field_type' => $data['field_type'] ?? 'text',
                        ];
                        // Remove the individual fields as they're now in parametros_campo
                        unset($data['field_name'], $data['field_type']);

                        return $data;
                    })
                    ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                        // On update, structure the data correctly
                        $data['parametros_campo'] = [
                            'field_name' => $data['field_name'] ?? '',
                            'field_type' => $data['field_type'] ?? 'text',
                        ];
                        // Remove the individual fields as they're now in parametros_campo
                        unset($data['field_name'], $data['field_type']);

                        return $data;
                    })
                    ->mutateRelationshipDataBeforeFillUsing(function (array $data): array {
                        // Extract field data from parametros_campo for the form
                        if (isset($data['parametros_campo'])) {
                            $params = is_array($data['parametros_campo']) ? $data['parametros_campo'] : json_decode($data['parametros_campo'], true);
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
                            ->default('text')
                            ->required(),
                    ])
                    ->addActionLabel('Adicionar Campo')
                    ->itemLabel(fn (array $state): ?string => $state['field_name'] ?? null)
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull()
                    ->defaultItems(0),
            ]);
    }
}
