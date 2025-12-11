<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsWebhookController;
use App\Http\Controllers\OrcamentoStatusController;
use App\Http\Controllers\PaymentController;

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