<?php

namespace App\Filament\Resources\Clients;

use App\Filament\Resources\Clients\Pages\ListClients;
use App\Filament\Resources\Clients\Schemas\ClientForm;
use App\Filament\Resources\Clients\Schemas\ClientInfolist;
use App\Filament\Resources\Clients\Tables\ClientsTable;
use App\Models\Orcamento;
use BackedEnum;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ClientResource extends Resource
{
    protected static ?string $model = Orcamento::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationLabel = 'Clientes';
    
    protected static ?string $modelLabel = 'Cliente';
    
    protected static ?string $pluralModelLabel = 'Clientes';
    
    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'email';
    
    // Customização para parecer "Client"
    protected static ?string $slug = 'clients';

    public static function form(Schema $schema): Schema
    {
        return ClientForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ClientInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClientsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClients::route('/'),
        ];
    }
    
    public static function getEloquentQuery(): Builder
    {
        // Filtra para pegar apenas o último orçamento de cada email único (Cliente Único)
        $query = parent::getEloquentQuery();
        
        $user = auth()->user();
        if ($user && !$user->can('view_all_data') && !$user->hasRole('admin')) {
             $query->where('email', $user->email);
        }

        return $query->whereIn('id', function ($query) use ($user) {
                $subQuery = $query->select(DB::raw('MAX(id)'))
                      ->from('orcamentos')
                      ->whereNotNull('email')
                      ->groupBy('email');
                      
                if ($user && !$user->can('view_all_data') && !$user->hasRole('admin')) {
                    $subQuery->where('email', $user->email);
                }
            });
    }
    
    public static function canCreate(): bool
    {
        return false;
    }
    
    public static function canEdit($record): bool
    {
        return false;
    }
    
    public static function canDelete($record): bool
    {
        return false;
    }
    
    public static function canDeleteAny(): bool
    {
        return false;
    }
}
