<?php

namespace App\Filament\Resources\Clients\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-envelope')
                    ->copyable(),
                    
                TextColumn::make('numero')
                    ->label('WhatsApp / Telefone')
                    ->searchable()
                    ->icon('heroicon-o-phone')
                    ->formatStateUsing(fn ($state) => $state ? $state : 'N/A'),
                    
                TextColumn::make('created_at')
                    ->label('Último Orçamento')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->icon('heroicon-o-clock'),
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('Ativo a partir de'),
                        DatePicker::make('created_until')
                            ->label('Ativo até'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                // Apenas visualização se necessário (não implementado View page ainda, mas pode ser adicionado)
                // ViewAction::make(), 
            ])
            ->bulkActions([
                // Sem ações em massa
            ])
            ->defaultSort('created_at', 'desc');
    }
}
