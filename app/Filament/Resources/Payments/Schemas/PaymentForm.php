<?php

namespace App\Filament\Resources\Payments\Schemas;

use App\Models\Payment;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('tx_id')
                    ->required(),
                TextInput::make('reference_id')
                    ->label('Reference ID'),
                TextInput::make('valor')
                    ->required()
                    ->numeric(),
                TextInput::make('gateway_id')
                    ->required()
                    ->numeric(),
                Select::make('status')
                    ->options(Payment::getStatusOptions())
                    ->default('nao pago')
                    ->required(),
                TextInput::make('tool_id')
                    ->required()
                    ->numeric(),
                TextInput::make('number_whatsapp')
                    ->default(null),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->default(null),
                Checkbox::make('send_notification')
                    ->label('Enviar notificação de atualização de status')
                    ->default(false)
                    ->helperText('Marque esta opção para enviar uma notificação ao usuário informando sobre a mudança de status.')
            ]);
    }
}