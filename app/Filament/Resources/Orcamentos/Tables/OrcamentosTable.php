<?php

namespace App\Filament\Resources\Orcamentos\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrcamentosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('numero')
                    ->label('Telefone/Número')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('valor')
                    ->label('Valor')
                    ->money('BRL')
                    ->sortable(),
                BadgeColumn::make('aceito')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => $state === 'sim' ? 'Aceito' : 'Não Aceito')
                    ->colors([
                        'success' => 'sim',
                        'danger' => 'nao',
                    ])
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('aceito')
                    ->label('Status')
                    ->options([
                        'sim' => 'Aceito',
                        'nao' => 'Não Aceito',
                    ])
                    ->placeholder('Todos os status'),
                Filter::make('valor_range')
                    ->label('Intervalo de Valor')
                    ->form([
                        \Filament\Forms\Components\TextInput::make('valor_min')
                            ->label('Valor Mínimo')
                            ->numeric()
                            ->prefix('R$'),
                        \Filament\Forms\Components\TextInput::make('valor_max')
                            ->label('Valor Máximo')
                            ->numeric()
                            ->prefix('R$'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['valor_min'] ?? null,
                                fn (Builder $query, $value) => $query->where('valor', '>=', $value)
                            )
                            ->when(
                                $data['valor_max'] ?? null,
                                fn (Builder $query, $value) => $query->where('valor', '<=', $value)
                            );
                    }),
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }
}
