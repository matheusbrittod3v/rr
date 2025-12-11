<?php

namespace App\Filament\Resources\FilaPrestadores;

use App\Filament\Resources\FilaPrestadores\Pages\CreateFilaPrestador;
use App\Filament\Resources\FilaPrestadores\Pages\EditFilaPrestador;
use App\Filament\Resources\FilaPrestadores\Pages\ListFilaPrestadores;
use App\Filament\Resources\FilaPrestadores\Schemas\FilaPrestadorForm;
use App\Filament\Resources\FilaPrestadores\Schemas\FilaPrestadorInfolist;
use App\Filament\Resources\FilaPrestadores\Tables\FilaPrestadoresTable;
use App\Models\FilaPrestador;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class FilaPrestadorResource extends Resource
{
    protected static ?string $model = FilaPrestador::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $recordTitleAttribute = 'id';
    
    protected static UnitEnum|string|null $navigationGroup = 'Queue Management';

    public static function form(Schema $schema): Schema
    {
        return FilaPrestadorForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FilaPrestadorInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FilaPrestadoresTable::configure($table);
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
            'index' => ListFilaPrestadores::route('/'),
            'create' => CreateFilaPrestador::route('/create'),
            'edit' => EditFilaPrestador::route('/{record}/edit'),
        ];
    }
}
