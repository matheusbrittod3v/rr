<?php

namespace App\Filament\Resources\WhatsApis\Tables;

use App\Filament\Resources\WhatsApis\Actions\ConnectPipelineAction;
use App\Filament\Resources\WhatsApis\Actions\SelectGroupsWhenConnectedAction;
use App\Filament\Resources\WhatsApis\Actions\CheckConnectionStatusAction;
use App\Filament\Resources\WhatsApis\Actions\CheckConnectionStatusAndUpdateAction;
use App\Filament\Resources\WhatsApis\Actions\TerminateInstanceAction;
use App\Filament\Resources\WhatsApis\Actions\GenerateQrCodeAction;
use App\Filament\Resources\WhatsApis\Actions\SelectGroupsAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;

class WhatsApisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('host')
                    ->label('Host')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('authenticate')
                    ->label('Auth Type')
                    ->searchable()
                    ->sortable(),
                BadgeColumn::make('connection_status')
                    ->label('Connection Status')
                    ->colors([
                        'success' => 'connected',
                        'warning' => 'connecting',
                        'danger' => 'disconnected',
                    ])
                    ->icons([
                        'heroicon-o-check-circle' => 'connected',
                        'heroicon-o-clock' => 'connecting',
                        'heroicon-o-x-circle' => 'disconnected',
                    ])
                    ->searchable()
                    ->sortable(),
                TextColumn::make('instance_name')
                    ->label('Instance Name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('numero_instancia')
                    ->label('Número da Instância')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                ConnectPipelineAction::make(),
                GenerateQrCodeAction::make(),
              
                SelectGroupsAction::make(),
                TerminateInstanceAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}