<?php

namespace App\Filament\Resources\Banners\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class BannersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ordem')
                    ->label('Ordem')
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('nome')
                    ->label('Nome')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                ImageColumn::make('imagens')
                    ->label('Preview')
                    ->disk('public')
                    ->getStateUsing(static function ($record) {
                        return is_array($record->imagens)
                            ? $record->imagens
                            : [];
                    })
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText('+ :count')
                    ->size(48)
                    ->extraImgAttributes([
                        'loading' => 'lazy',
                        'referrerpolicy' => 'no-referrer',
                    ]),

                TextColumn::make('quantidade_imagens')
                    ->label('Qtd. Imagens')
                    ->badge()
                    ->color('info')
                    ->alignCenter(),

                TextColumn::make('tempo_rotacao')
                    ->label('Rotação')
                    ->suffix(' ms')
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->alignCenter(),

                IconColumn::make('ativo')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter(),

                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('ordem', 'asc')
            ->filters([
                TernaryFilter::make('ativo')
                    ->label('Status')
                    ->trueLabel('Apenas Ativos')
                    ->falseLabel('Apenas Inativos')
                    ->placeholder('Todos'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('ordem')
            ->emptyStateHeading('Nenhum banner cadastrado')
            ->emptyStateDescription('Crie seu primeiro banner rotativo clicando no botão acima.')
            ->emptyStateIcon('heroicon-o-photo');
    }
}
