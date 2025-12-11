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
                'messages',
            ])
            ->whereHas('payment', function ($query) {
                $query->whereIn('status', ['pago', 'success']);
            });

        // Check for 'view_all_data' permission
        $user = auth()->user();
        if ($user && ! $user->can('view_all_data') && ! $user->hasRole('admin')) {
            // Se for prestador: ver salas onde é o prestador vinculado ao orçamento
            if ($user->isProvider()) {
                $query->whereHas('payment.orcamento', function ($q) use ($user) {
                    $q->where('prestador_id', $user->id);
                });
            } else {
                // Cliente: ver salas do seu email
                $query->whereHas('payment', function ($q) use ($user) {
                    $q->where('email', $user->email);
                });
            }
        }

        return $query;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_chat_rooms');
    }

    public static function canCreate(): bool
    {
        return auth()->user()->can('create_chat_rooms');
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->can('edit_chat_rooms');
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->can('delete_chat_rooms');
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()->can('delete_chat_rooms');
    }
}
