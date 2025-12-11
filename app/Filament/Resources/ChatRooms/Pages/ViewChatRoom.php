<?php

namespace App\Filament\Resources\ChatRooms\Pages;

use App\Filament\Resources\ChatRooms\ChatRoomResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;

class ViewChatRoom extends ViewRecord
{
    protected static string $resource = ChatRoomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('open_chat')
                ->label('Abrir Chat')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn (): string => route('chat.room', $this->record->room_code))
                ->openUrlInNewTab()
                ->color('primary'),
        ];
    }
}
