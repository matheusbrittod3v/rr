<?php

namespace App\Filament\Resources\FilaOrcamentos\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FilaOrcamentosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('orcamento.id')
                    ->label('ID do Orçamento')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('prestador.name')
                    ->label('Prestador')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }
}
