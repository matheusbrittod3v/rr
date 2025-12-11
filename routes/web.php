<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Api\MercadoPagoController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Api\DhruController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\WhatsApiController;

Route::get('/', [HomeController::class, 'index']);

Route::get('/sitemap.xml', [SitemapController::class, 'index']);

Route::get('/register', function () {

    return json_encode("hello word ");
})->name('register');

// WebSocket test page
Route::get('/websocket-test', function () {
    return view('websocket-test');
});

// Test route for WebSocket connectivity
Route::get('/test-websocket', function () {
    // Fire a test event
    event(new App\Events\PaymentStatusUpdated(
        new App\Models\Payment(), 
        'test', 
        'WebSocket connection test'
    ));
    
    return response()->json(['success' => true, 'message' => 'Test event fired']);
});

// Rota para solicitar orçamento de serviço
Route::post('/request-service-quote', [HomeController::class, 'requestServiceQuote']);

// Rotas da API do Mercado Pago
Route::prefix('api/mercadopago')->group(function () {
    Route::post('/create-pix-payment', [MercadoPagoController::class, 'createPixPayment']);
    Route::get('/check-payment-status/{paymentId}', [MercadoPagoController::class, 'checkPaymentStatus']);
    Route::get('/notification', [MercadoPagoController::class, 'handleNotification'])->middleware('verify.mercadopago');

    
});

// Rotas da API do DHRU
Route::prefix('api/dhru')->group(function () {
    Route::get('/account-info', [DhruController::class, 'getAccountInfo']);
    Route::get('/services', [DhruController::class, 'getServices']);
    Route::get('/file-services', [DhruController::class, 'getFileServices']);
    Route::post('/place-imei-order', [DhruController::class, 'placeImeiOrder']);
    Route::post('/place-bulk-imei-order', [DhruController::class, 'placeBulkImeiOrder']);
});

// Rotas da API de Notificações
Route::prefix('api/notifications')->group(function () {
    Route::post('/send-test', [NotificationController::class, 'sendTestNotification']);
    Route::post('/send-whatsapp', [NotificationController::class, 'sendWhatsAppNotification']);
    Route::post('/send-email', [NotificationController::class, 'sendEmailNotification']);
});

// Rota para processar pagamentos Pix
Route::post('/process-pix-payment', [PaymentController::class, 'processPixPayment']);

// Rota para processar solicitações de reembolso
Route::post('/process-refund-request', [PaymentController::class, 'processRefundRequest']);

// Rota para histórico de pagamentos
Route::get('/history', [PaymentController::class, 'showHistory'])->name('payment.history');

// Rotas para chat
Route::prefix('chat')->group(function () {
    Route::get('/{roomCode}', [ChatController::class, 'showLogin'])->name('chat.login');
    Route::post('/{roomCode}/authenticate', [ChatController::class, 'authenticate'])->name('chat.authenticate');
    Route::get('/{roomCode}/room', [ChatController::class, 'showRoom'])->name('chat.room');
    Route::post('/{roomCode}/send-message', [ChatController::class, 'sendMessage'])->name('chat.send-message');
    Route::get('/{roomCode}/messages', [ChatController::class, 'getMessages'])->name('chat.get-messages');
});

// WhatsApp QR Code endpoint (única instância - uso interno)
Route::get('/whatsapp/qr-code', [WhatsApiController::class, 'getQrCode'])->middleware('auth');

// WhatsApp Connection Status endpoint (única instância - uso interno)
Route::get('/whatsapp/connection-status', [WhatsApiController::class, 'getConnectionStatus'])->middleware('auth');

// WhatsApp Groups endpoints (única instância - uso interno)
Route::get('/whatsapp/groups', [WhatsApiController::class, 'getGroups'])->middleware('auth');
Route::post('/whatsapp/groups/save', [WhatsApiController::class, 'saveSelectedGroups'])->middleware('auth');