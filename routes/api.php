<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsWebhookController;
use App\Http\Controllers\OrcamentoStatusController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Api\ChatMessageController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// WhatsApp webhook endpoint
Route::post('/webhooks/whatsapp', [WhatsWebhookController::class, 'receive']);

// Orçamento status endpoint (for real-time budget updates)
Route::get('/orcamentos/{orcamento}/status', [OrcamentoStatusController::class, 'getStatus']);

// Payment endpoints
Route::post('/payments/orcamento-pix', [PaymentController::class, 'generateOrcamentoPixPayment']);
Route::get('/payments/{payment}/status', [PaymentController::class, 'getPaymentStatus']);

// Chat endpoints - autenticação via sessão de chat
Route::prefix('chat/{roomCode}')->group(function () {
    Route::post('/messages', [ChatMessageController::class, 'sendMessage']);
    Route::get('/messages', [ChatMessageController::class, 'getMessages']);
    Route::post('/typing', [ChatMessageController::class, 'sendTypingIndicator']);
});