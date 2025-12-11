<?php

namespace App\Filament\Resources\GatewayPagamentos;

use App\Filament\Resources\GatewayPagamentos\Pages\CreateGatewayPagamento;
use App\Filament\Resources\GatewayPagamentos\Pages\EditGatewayPagamento;
use App\Filament\Resources\GatewayPagamentos\Pages\ListGatewayPagamentos;
use App\Filament\Resources\GatewayPagamentos\Pages\ViewGatewayPagamento;
use App\Filament\Resources\GatewayPagamentos\Schemas\GatewayPagamentoForm;
use App\Filament\Resources\GatewayPagamentos\Schemas\GatewayPagamentoInfolist;
use App\Filament\Resources\GatewayPagamentos\Tables\GatewayPagamentosTable;
use App\Models\GatewayPagamento;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GatewayPagamentoResource extends Resource
{
    protected static ?string $model = GatewayPagamento::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return GatewayPagamentoForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GatewayPagamentoInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GatewayPagamentosTable::configure($table);
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
            'index' => ListGatewayPagamentos::route('/'),
            'create' => CreateGatewayPagamento::route('/create'),
            'view' => ViewGatewayPagamento::route('/{record}'),
            'edit' => EditGatewayPagamento::route('/{record}/edit'),
        ];
    }

     public static function getNavigationGroup(): ?string
    {
        return 'API Configurations';
    }
}