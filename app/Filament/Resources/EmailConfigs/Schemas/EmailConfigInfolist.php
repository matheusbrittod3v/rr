<?php

namespace App\Filament\Resources\EmailConfigs\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class EmailConfigInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('host'),
                TextEntry::make('port')
                    ->numeric(),
                TextEntry::make('encryption_type'),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('type'),
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
