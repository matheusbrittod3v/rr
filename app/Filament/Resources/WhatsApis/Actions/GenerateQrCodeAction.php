<?php

namespace App\Filament\Resources\WhatsApis\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class GenerateQrCodeAction extends Action
{
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'Gerar QR Code')
            ->label('Gerar QR Code')
            ->icon('heroicon-o-qr-code')
            ->modalHeading('QR Code da Instância')
            ->modalWidth('lg')

            ->action(function ($record) {
                Log::info('🚀 INICIANDO GenerateQrCodeAction (apenas abrir modal)', [
                    'user_id' => auth()->id(),
                    'timestamp' => now()->toISOString(),
                ]);

                // Apenas abre o modal, não precisa passar nada
                return [];
            })
            
            // Formulário do modal
            ->form([
                ViewField::make('qrCode')
                    ->view('filament.whatsapp.qrcode')
                    ->label('QR Code para conexão')
            ])

            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Fechar');
    }
}