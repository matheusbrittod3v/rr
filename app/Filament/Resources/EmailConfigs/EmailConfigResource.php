<?php

namespace App\Filament\Resources\EmailConfigs;

use App\Filament\Resources\EmailConfigs\Pages\CreateEmailConfig;
use App\Filament\Resources\EmailConfigs\Pages\EditEmailConfig;
use App\Filament\Resources\EmailConfigs\Pages\ListEmailConfigs;
use App\Filament\Resources\EmailConfigs\Pages\ViewEmailConfig;
use App\Filament\Resources\EmailConfigs\Schemas\EmailConfigForm;
use App\Filament\Resources\EmailConfigs\Schemas\EmailConfigInfolist;
use App\Filament\Resources\EmailConfigs\Tables\EmailConfigsTable;
use App\Models\EmailConfig;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EmailConfigResource extends Resource
{
    protected static ?string $model = EmailConfig::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'host';

    public static function form(Schema $schema): Schema
    {
        return EmailConfigForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EmailConfigInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmailConfigsTable::configure($table);
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
            'index' => ListEmailConfigs::route('/'),
            'create' => CreateEmailConfig::route('/create'),
            'view' => ViewEmailConfig::route('/{record}'),
            'edit' => EditEmailConfig::route('/{record}/edit'),
        ];
    }
    
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }
    
    public static function canCreate(): bool
    {
        // Only allow creation if user doesn't already have an email config
        return !EmailConfig::userHasConfig(auth()->id());
    }

    public static function getNavigationGroup(): ?string
    {
        return 'API Configurations';
    }
}