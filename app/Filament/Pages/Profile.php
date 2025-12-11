<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class Profile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationLabel = 'Profile';

    protected static ?string $title = 'My Profile';

    protected static ?int $navigationSort = 100;

    protected static bool $shouldRegisterNavigation = false; // Hide from sidebar

    protected string $view = 'filament.pages.profile';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
            'numero' => auth()->user()->numero,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Personal Information')
                    ->description('Update your personal details here.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-user'),

                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->unique('users', 'email', ignoreRecord: true)
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-envelope'),

                        TextInput::make('numero')
                            ->label('Phone Number')
                            ->tel()
                            ->maxLength(20)
                            ->prefixIcon('heroicon-o-phone'),
                    ])
                    ->columns(2),

                Section::make('Change Password')
                    ->description('Leave blank if you don\'t want to change your password.')
                    ->schema([
                        TextInput::make('current_password')
                            ->label('Current Password')
                            ->password()
                            ->revealable()
                            ->dehydrated(false)
                            ->requiredWith('new_password')
                            ->currentPassword()
                            ->prefixIcon('heroicon-o-lock-closed'),

                        TextInput::make('new_password')
                            ->label('New Password')
                            ->password()
                            ->revealable()
                            ->dehydrated(false)
                            ->rule(Password::default())
                            ->confirmed()
                            ->prefixIcon('heroicon-o-key'),

                        TextInput::make('new_password_confirmation')
                            ->label('Confirm New Password')
                            ->password()
                            ->revealable()
                            ->dehydrated(false)
                            ->prefixIcon('heroicon-o-key'),
                    ])
                    ->columns(3)
                    ->collapsible()
                    ->collapsed(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $user = auth()->user();

        // Update basic info
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'numero' => $data['numero'] ?? null,
        ]);

        // Update password if provided
        if (! empty($data['new_password'])) {
            $user->update([
                'password' => Hash::make($data['new_password']),
            ]);
        }

        Notification::make()
            ->success()
            ->title('Profile Updated')
            ->body('Your profile has been updated successfully.')
            ->send();

        // Refresh form
        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
            'numero' => $user->numero,
        ]);
    }
}
