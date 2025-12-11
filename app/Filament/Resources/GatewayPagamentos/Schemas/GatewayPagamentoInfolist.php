<?php

namespace App\Filament\Resources\GatewayPagamentos\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class GatewayPagamentoInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('url'),
                TextEntry::make('token')
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
