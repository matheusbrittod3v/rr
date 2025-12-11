<?php

namespace App\Filament\Resources\WhatsApis\Pages;

use App\Filament\Resources\WhatsApis\Actions\ConnectPipelineAction;
use App\Filament\Resources\WhatsApis\Actions\SelectGroupsWhenConnectedAction;
use App\Filament\Resources\WhatsApis\Actions\TerminateInstanceAction;
use App\Filament\Resources\WhatsApis\Actions\GenerateQrCodeAction;
use App\Filament\Resources\WhatsApis\Actions\TestApiAction;
use App\Filament\Resources\WhatsApis\WhatsApiResource;

use Filament\Resources\Pages\ListRecords;
use Filament\Actions\CreateAction;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;

class ListWhatsApis extends ListRecords
{
    protected static string $resource = WhatsApiResource::class;

    /**
     * Header actions
     */
    public function getHeaderActions(): array
    {
        if (\App\Models\WhatsApi::userHasConfig(auth()->id())) {
            return [
                TestApiAction::make()
                    ->label('Test API')
                    ->icon('heroicon-o-paper-airplane'),
            ];
        }

        return [
            CreateAction::make(),
        ];
    }

    /**
     * Ações por registro
     */
    protected function getTableRecordActions(): array
    {
        return [
            ActionGroup::make([
                ViewAction::make(),
                EditAction::make(),
                ConnectPipelineAction::make(),
                GenerateQrCodeAction::make(),
                TerminateInstanceAction::make(),
                TestApiAction::make(),
            ]),
        ];
    }
}