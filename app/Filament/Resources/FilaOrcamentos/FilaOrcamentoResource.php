<?php

namespace App\Filament\Resources\FilaOrcamentos;

use App\Filament\Resources\FilaOrcamentos\Pages\CreateFilaOrcamento;
use App\Filament\Resources\FilaOrcamentos\Pages\EditFilaOrcamento;
use App\Filament\Resources\FilaOrcamentos\Pages\ListFilaOrcamentos;
use App\Filament\Resources\FilaOrcamentos\Schemas\FilaOrcamentoForm;
use App\Filament\Resources\FilaOrcamentos\Schemas\FilaOrcamentoInfolist;
use App\Filament\Resources\FilaOrcamentos\Tables\FilaOrcamentosTable;
use App\Models\FilaOrcamento;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FilaOrcamentoResource extends Resource
{
    protected static ?string $model = FilaOrcamento::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'id';
    
    protected static UnitEnum|string|null $navigationGroup = 'Queue Management';

    public static function form(Schema $schema): Schema
    {
        return FilaOrcamentoForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FilaOrcamentoInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FilaOrcamentosTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFilaOrcamentos::route('/'),
            'create' => CreateFilaOrcamento::route('/create'),
            'edit' => EditFilaOrcamento::route('/{record}/edit'),
        ];
    }
}
