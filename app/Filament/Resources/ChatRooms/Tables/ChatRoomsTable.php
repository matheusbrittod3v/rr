<?php

namespace App\Filament\Resources\ChatRooms\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;

class ChatRoomsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                    
                TextColumn::make('room_code')
                    ->label('Código da Sala')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Código copiado!')
                    ->icon('heroicon-o-link')
                    ->color('primary')
                    ->weight('bold'),
                    
                TextColumn::make('payment.email')
                    ->label('Email do Cliente')
                    ->searchable()
                    ->icon('heroicon-o-envelope')
                    ->default('N/A')
                    ->limit(30),
                    
                TextColumn::make('payment.number_whatsapp')
                    ->label('WhatsApp Cliente')
                    ->searchable()
                    ->icon('heroicon-o-phone')
                    ->default('N/A'),
                    
                TextColumn::make('payment.orcamento.filaOrcamento.prestador.name')
                    ->label('Prestador')
                    ->searchable()
                    ->icon('heroicon-o-user')
                    ->default('Não atribuído')
                    ->limit(25)
                    ->wrap(),
                    
                TextColumn::make('payment.orcamento.service.nome_servico')
                    ->label('Serviço')
                    ->searchable()
                    ->badge()
                    ->color('success')
                    ->default('N/A'),
                    
                TextColumn::make('payment.orcamento.service.marca.nome')
                    ->label('Marca')
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->default('N/A'),
                    
                TextColumn::make('payment.valor')
                    ->label('Valor')
                    ->money('BRL')
                    ->sortable()
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success'),
                    
                TextColumn::make('payment.status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pago', 'success' => 'success',
                        'processando' => 'warning',
                        'nao pago' => 'danger',
                        'refund' => 'gray',
                        'contestacao' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                    
                TextColumn::make('messages_count')
                    ->label('Msgs')
                    ->counts('messages')
                    ->sortable()
                    ->icon('heroicon-o-chat-bubble-left')
                    ->color('primary')
                    ->badge(),
                    
                TextColumn::make('created_at')
                    ->label('Criada em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->icon('heroicon-o-clock')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('payment.status')
                    ->label('Status Pagamento')
                    ->options([
                        'pago' => 'Pago',
                        'success' => 'Success',
                        'processando' => 'Processando',
                        'nao pago' => 'Não Pago',
                        'refund' => 'Refund',
                        'contestacao' => 'Contestação',
                    ]),
                    
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('Criada a partir de'),
                        DatePicker::make('created_until')
                            ->label('Criada até'),
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
            ->recordActions([
                ViewAction::make()
                    ->label('Ver'),
                    
                Action::make('open_chat')
                    ->label('Abrir Chat')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn ($record): string => route('chat.room', $record->room_code))
                    ->openUrlInNewTab()
                    ->color('primary'),
            ])
            ->toolbarActions([
                // Sem ações de criação/exclusão
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s'); // Auto-refresh a cada 30 segundos
    }
}
