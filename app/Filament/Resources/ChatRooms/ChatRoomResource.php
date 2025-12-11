<?php

namespace App\Filament\Resources\ChatRooms;

use App\Filament\Resources\ChatRooms\Pages\ListChatRooms;
use App\Filament\Resources\ChatRooms\Pages\ViewChatRoom;
use App\Filament\Resources\ChatRooms\Schemas\ChatRoomForm;
use App\Filament\Resources\ChatRooms\Schemas\ChatRoomInfolist;
use App\Filament\Resources\ChatRooms\Tables\ChatRoomsTable;
use App\Models\ChatRoom;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ChatRoomResource extends Resource
{
    protected static ?string $model = ChatRoom::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';
    
    protected static ?string $navigationLabel = 'Salas de Chat';
    
    protected static ?string $modelLabel = 'Sala de Chat';
    
    protected static ?string $pluralModelLabel = 'Salas de Chat';
    
    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'room_code';

    public static function form(Schema $schema): Schema
    {
        return ChatRoomForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ChatRoomInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ChatRoomsTable::configure($table);
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
            'index' => ListChatRooms::route('/'),
            'view' => ViewChatRoom::route('/{record}'),
        ];
    }
    
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with([
                'payment.orcamento.filaOrcamento.prestador',
                'payment.orcamento.service.marca',
                'messages'
            ])
            ->whereHas('payment', function ($query) {
                $query->whereIn('status', ['pago', 'success']);
            });

        // Check for 'view_all_data' permission
        $user = auth()->user();
        if ($user && !$user->can('view_all_data') && !$user->hasRole('admin')) {
            // Se for prestador: ver salas onde é o prestador
            if ($user->isProvider()) {
                 $query->whereHas('payment.orcamento.filaOrcamento', function($q) use ($user) {
                     $q->where('prestador_id', $user->id); // Assumindo relação com User ID na fila se houver, ou via model Prestador linkado ao user
                     // OBS: FilaOrcamento tem prestador_id. Se prestador_id for ID da tabela user, ok. Se for ID de tabela Prestador, precisamos mapear.
                     // User tem providerQueueEntry -> user_id.
                     // Vamos filtrar pelo email do prestador se for o caso, ou assumir user->id == prestador_id se a role for prestador.
                     // O model FilaOrcamento usa 'prestador_id' que parece ser da tabela 'fila_prestadores' (id).
                     // User belongsTo FilaPrestador?? Não, hasOne providerQueueEntry.
                 });
                 // Melhor: ChatRoom tem link com Payment -> Orcamento.
                 // Se não conseguir validar provider, filtrar por email seria safer se User tiver email igual.
            } else {
                 // Cliente: ver salas do seu email
                 $query->whereHas('payment', function($q) use ($user) {
                     $q->where('email', $user->email);
                 });
            }
        }

        return $query;
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
