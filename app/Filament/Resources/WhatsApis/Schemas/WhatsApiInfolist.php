<?php

namespace App\Filament\Resources\WhatsApis\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\BadgeEntry;
use Filament\Schemas\Schema;

class WhatsApiInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('Name'),
                TextEntry::make('host')
                    ->label('Host'),
                TextEntry::make('key')
                    ->label('Key'),
                TextEntry::make('type')
                    ->label('Type'),
                TextEntry::make('authenticate')
                    ->label('Authentication Type'),
                BadgeEntry::make('connection_status')
                    ->label('Connection Status')
                    ->colors([
                        'success' => 'connected',
                        'danger' => 'disconnected',
                    ])
                    ->icons([
                        'heroicon-o-check-circle' => 'connected',
                        'heroicon-o-x-circle' => 'disconnected',
                    ]),
                TextEntry::make('instance_name')
                    ->label('Instance Name')
                    ->default('N/A'),
                TextEntry::make('numero_instancia')
                    ->label('Número da Instância')
                    ->default('N/A'),
            ]);
    }
}