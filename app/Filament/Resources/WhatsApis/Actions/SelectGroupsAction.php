<?php

namespace App\Filament\Resources\WhatsApis\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class SelectGroupsAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'selectGroups';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Selecionar Grupos')
            ->icon('heroicon-o-user-group')
            ->modalHeading('Selecionar Grupos do WhatsApp')
            ->modalWidth('4xl')
            
            /**
             * Em Filament 3, CSS customizado no modal
             * deve ser feito colocando um Blade customizado
             * dentro de modalContent()
             */
            ->modalContent(function ($record) {
                return view('filament.whatsapp.groups-selection', [
                    'record' => $record
                ]);
            })
            
            /**
             * Remove botão de submit,
             * deixando somente o botão de fechar
             */
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Fechar')
            
            ->action(function ($record) {
                Log::info('Abrindo SelectGroupsAction.', [
                    'user_id' => auth()->id(),
                    'record_id' => $record->id ?? null,
                    'timestamp' => now()->toISOString(),
                ]);

                return [];
            });
    }
}