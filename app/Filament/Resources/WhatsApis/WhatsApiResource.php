<?php

namespace App\Filament\Resources\WhatsApis;

use App\Filament\Resources\WhatsApis\Pages\CreateWhatsApi;
use App\Filament\Resources\WhatsApis\Pages\EditWhatsApi;
use App\Filament\Resources\WhatsApis\Pages\ListWhatsApis;
use App\Filament\Resources\WhatsApis\Pages\ViewWhatsApi;
use App\Filament\Resources\WhatsApis\Schemas\WhatsApiForm;
use App\Filament\Resources\WhatsApis\Schemas\WhatsApiInfolist;
use App\Filament\Resources\WhatsApis\Tables\WhatsApisTable;
use App\Models\WhatsApi;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WhatsApiResource extends Resource
{
    protected static ?string $model = WhatsApi::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return WhatsApiForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return WhatsApiInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WhatsApisTable::configure($table);
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
            'index' => ListWhatsApis::route('/'),
            'view' => ViewWhatsApi::route('/{record}'),
        ];
    }
    
    public static function getNavigationGroup(): ?string
    {
        return 'API Configurations';
    }
    
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        // If user can view all data, return query without user_id scope
        if (auth()->user() && auth()->user()->canViewAllData()) {
            return parent::getEloquentQuery();
        }

        // Otherwise, filter by user_id
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }

    public static function getModelLabel(): string
    {
        return 'API WHATSAPP';
    }

    public static function getPluralModelLabel(): string
    {
        return 'API WHATSAPP';
    }
    
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }
}