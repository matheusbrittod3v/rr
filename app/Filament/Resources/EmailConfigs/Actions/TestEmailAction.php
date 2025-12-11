<?php

namespace App\Filament\Resources\EmailConfigs\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use App\Models\EmailConfig;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TestEmailAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'testEmail';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Test Email')
            ->icon('heroicon-o-paper-airplane')
            ->form([
                TextInput::make('test_email')
                    ->label('Test Email Address')
                    ->email()
                    ->required()
                    ->placeholder('Enter email address to send test message'),
            ])
            ->action(function (array $data, EmailConfig $record): void {
                // Log the start of the test
                Log::info('Starting email configuration test', [
                    'user_id' => auth()->id(),
                    'email_config_id' => $record->id,
                    'test_email' => $data['test_email']
                ]);
                
                try {
                    // Configure mail settings dynamically
                    config([
                        'mail.mailers.smtp.host' => $record->host,
                        'mail.mailers.smtp.port' => $record->port,
                        'mail.mailers.smtp.encryption' => $record->encryption_type,
                        'mail.mailers.smtp.username' => $record->email,
                        'mail.mailers.smtp.password' => $record->password,
                    ]);
                    
                    // Send a test email
                    Mail::mailer('smtp')->raw(
                        'This is a test email from renttool.com to verify your email configuration.',
                        function ($message) use ($data, $record) {
                            $message->to($data['test_email'])
                                ->subject('Test Email Configuration')
                                ->from($record->email, 'Techunion Solutions');
                        }
                    );
                    
                    Notification::make()
                        ->title('Success')
                        ->body('Test email sent successfully to ' . $data['test_email'])
                        ->success()
                        ->send();
                    
                    Log::info('Test email sent successfully');
                } catch (\Exception $e) {
                    $errorMessage = 'Failed to send test email: ' . $e->getMessage();
                    Notification::make()
                        ->title('Error')
                        ->body($errorMessage)
                        ->danger()
                        ->send();
                    
                    Log::error('Exception occurred while sending test email', [
                        'exception' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            });
    }
}