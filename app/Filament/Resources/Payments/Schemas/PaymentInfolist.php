<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PaymentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('reference_id')
                    ->label('Reference ID'),
                TextEntry::make('valor')
                    ->numeric(),
                TextEntry::make('gateway_id')
                    ->numeric(),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('orcamento.prestador.name')
                    ->label('Prestador')
                    ->placeholder('-'),
                TextEntry::make('number_whatsapp')
                    ->placeholder('-'),
                TextEntry::make('email')
                    ->label('Email address')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}