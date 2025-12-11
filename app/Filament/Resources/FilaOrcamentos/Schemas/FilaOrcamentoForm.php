<?php

namespace App\Filament\Resources\FilaOrcamentos\Schemas;

use App\Models\Orcamento;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class FilaOrcamentoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('orcamento_id')
                    ->label('Orçamento')
                    ->relationship('orcamento', 'id')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('prestador_id')
                    ->label('Prestador')
                    ->relationship('prestador', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }
}
