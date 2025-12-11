<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use App\Models\Role;
use App\Models\RegistrationLink;
use Filament\Notifications\Notification;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('manual')
                    ->label('Manual')
                    ->icon('heroicon-o-pencil-square')
                    ->url(fn() => static::$resource::getUrl('create')),
                
                Action::make('link')
                    ->label('Generate Link')
                    ->icon('heroicon-o-link')
                    ->form([
                        Select::make('roles')
                            ->label('Roles')
                            ->multiple()
                            ->options(Role::all()->pluck('name', 'id'))
                            ->required()
                            ->helperText('Users will be assigned these roles upon registration'),
                        
                        Toggle::make('is_provider')
                            ->label('Is Provider')
                            ->helperText('Mark users as providers')
                            ->default(false),
                        
                        DateTimePicker::make('expires_at')
                            ->label('Expires At')
                            ->helperText('Leave empty for no expiration')
                            ->nullable(),
                        
                        TextInput::make('max_uses')
                            ->label('Max Uses')
                            ->numeric()
                            ->helperText('Leave empty for unlimited uses')
                            ->nullable(),
                    ])
                    ->action(function (array $data) {
                        $link = RegistrationLink::create([
                            'token' => RegistrationLink::generateToken(),
                            'roles' => $data['roles'],
                            'is_provider' => $data['is_provider'] ?? false,
                            'created_by' => auth()->id(),
                            'expires_at' => $data['expires_at'] ?? null,
                            'max_uses' => $data['max_uses'] ?? null,
                        ]);

                        $url = url('/register/' . $link->token);

                        Notification::make()
                            ->success()
                            ->title('Registration Link Generated!')
                            ->body("Copy this link and share: {$url}")
                            ->persistent()
                            ->send();
                    }),
            ])
                ->label('New User')
                ->icon('heroicon-o-user-plus')
                ->button(),
        ];
    }
}