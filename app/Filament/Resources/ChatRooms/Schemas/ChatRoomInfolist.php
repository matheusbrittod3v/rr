<?php

namespace App\Filament\Resources\ChatRooms\Schemas;

use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ChatRoomInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informações da Sala')
                    ->schema([
                        TextEntry::make('room_code')
                            ->label('Código da Sala')
                            ->copyable()
                            ->icon('heroicon-o-link')
                            ->color('primary'),
                        TextEntry::make('client_password')
                            ->label('Senha do Cliente')
                            ->icon('heroicon-o-key'),
                        TextEntry::make('provider_password')
                            ->label('Senha do Prestador')
                            ->icon('heroicon-o-key'),
                        TextEntry::make('created_at')
                            ->label('Criada em')
                            ->dateTime('d/m/Y H:i:s')
                            ->icon('heroicon-o-clock'),
                    ])
                    ->columns(2),
                    
                Section::make('Dados do Cliente')
                    ->schema([
                        TextEntry::make('payment.email')
                            ->label('Email')
                            ->icon('heroicon-o-envelope')
                            ->default('N/A'),
                        TextEntry::make('payment.number_whatsapp')
                            ->label('WhatsApp')
                            ->icon('heroicon-o-phone')
                            ->default('N/A'),
                    ])
                    ->columns(2),
                    
                Section::make('Dados do Prestador')
                    ->schema([
                        TextEntry::make('payment.orcamento.filaOrcamento.prestador.name')
                            ->label('Nome')
                            ->icon('heroicon-o-user')
                            ->default('Não atribuído'),
                        TextEntry::make('payment.orcamento.filaOrcamento.prestador.email')
                            ->label('Email')
                            ->icon('heroicon-o-envelope')
                            ->default('N/A'),
                    ])
                    ->columns(2),
                    
                Section::make('Informações do Orçamento')
                    ->schema([
                        TextEntry::make('payment.orcamento.service.nome_servico')
                            ->label('Serviço')
                            ->badge()
                            ->color('success')
                            ->default('N/A'),
                        TextEntry::make('payment.orcamento.service.marca.nome')
                            ->label('Marca')
                            ->badge()
                            ->color('info')
                            ->default('N/A'),
                        TextEntry::make('payment.orcamento.modelo')
                            ->label('Modelo')
                            ->default('N/A'),
                        TextEntry::make('payment.orcamento.descricao')
                            ->label('Descrição')
                            ->default('N/A')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
                    
                Section::make('Informações do Pagamento')
                    ->schema([
                        TextEntry::make('payment.valor')
                            ->label('Valor')
                            ->money('BRL')
                            ->icon('heroicon-o-currency-dollar')
                            ->color('success'),
                        TextEntry::make('payment.status')
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
                        TextEntry::make('payment.tx_id')
                            ->label('ID da Transação')
                            ->copyable()
                            ->default('N/A'),
                    ])
                    ->columns(3),
                    
                Section::make('Estatísticas do Chat')
                    ->schema([
                        TextEntry::make('messages_count')
                            ->label('Total de Mensagens')
                            ->state(fn ($record) => $record->messages()->count())
                            ->icon('heroicon-o-chat-bubble-left')
                            ->badge()
                            ->color('primary'),
                        TextEntry::make('last_message')
                            ->label('Última Mensagem')
                            ->state(fn ($record) => $record->messages()->latest()->first()?->created_at?->diffForHumans() ?? 'Nenhuma mensagem')
                            ->icon('heroicon-o-clock'),
                    ])
                    ->columns(2),
            ]);
    }
}
