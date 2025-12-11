<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Gateways\MercadoPagoService;
use App\Services\NotificationService;
use App\Services\IdempotencyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Payment;
use App\Models\ChatRoom;
use App\Models\User;
use App\Jobs\SendClientPaymentNotificationJob;
use App\Jobs\SendProviderPaymentNotificationJob;
use Illuminate\Support\Facades\Redirect;
class MercadoPagoController extends Controller
{
    protected $mercadoPagoService;
    protected $notificationService;

    public function __construct(MercadoPagoService $mercadoPagoService, NotificationService $notificationService)
    {
        $this->mercadoPagoService = $mercadoPagoService;
        $this->notificationService = $notificationService;
    }

    /**
     * Check payment status and process approved payments
     *
     * @param int $paymentRecordId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function checkPaymentStatus($paymentRecordId, Request $request)
    {
        try {
            // Step 1: Validate payment exists
            $paymentModel = $this->getPaymentById($paymentRecordId);
            if (!$paymentModel) {
                // Always redirect back with error message
                return redirect()->back()->with('error', 'Payment not found');
            }

            // Step 2: Check if already processed
            if ($this->isPaymentAlreadyProcessed($paymentModel)) {
                // Redirect to chat room for already processed payments
                $existingChatRoom = ChatRoom::where('payment_id', $paymentModel->id)->first();
                if ($existingChatRoom) {
                    $this->setChatRoomSession($existingChatRoom);
                    return Redirect::to(route('chat.room', $existingChatRoom->room_code));
                }
                
                return redirect()->back()->with('error', 'Chat room not found');
            }

            // Step 3: Check payment approval status
            $isApproved = $this->checkPaymentApproval($paymentModel);
            if (!$isApproved) {
                // Redirect back with error for non-approved payments
                return redirect()->back()->with('error', 'Payment not approved');
            }

            // Step 4: Process approved payment
            return $this->processApprovedPayment($paymentModel, $request);
        } catch (\Exception $e) {
            Log::error('Error in checkPaymentStatus', [
                'payment_id' => $paymentRecordId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Always redirect back with error message
            return redirect()->back()->with('error', 'Internal server error');
        }
    }

    /**
     * Get payment by ID
     *
     * @param int $paymentId
     * @return Payment|null
     */
    private function getPaymentById($paymentId)
    {
        return Payment::find($paymentId);
    }

    /**
     * Check if payment has already been processed
     *
     * @param Payment $paymentModel
     * @return bool
     */
    private function isPaymentAlreadyProcessed($paymentModel)
    {
        if ($paymentModel->status === Payment::STATUS_PAGO) {
            Log::info('Payment already processed, skipping duplicate processing', [
                'payment_id' => $paymentModel->id,
                'current_status' => $paymentModel->status
            ]);
            
            // Still create chat room if it doesn't exist (for idempotency)
            $existingChatRoom = ChatRoom::where('payment_id', $paymentModel->id)->first();
            if (!$existingChatRoom) {
                $chatRoom = \App\Http\Controllers\ChatController::createChatRoomForPayment($paymentModel);
                Log::info('Created chat room for already processed payment', [
                    'payment_id' => $paymentModel->id,
                    'chat_room_id' => $chatRoom->id
                ]);
            }
            
            return true;
        }
        
        return false;
    }

    /**
     * Check if payment is approved
     *
     * @param Payment $paymentModel
     * @return bool
     */
    private function checkPaymentApproval($paymentModel)
    {
        // Simplified bypass - always approve payments in debug mode
        $isDebugMode = config('app.debug', false);
        
        if ($isDebugMode) {
            return true;
        }
        
        // Get payment info from Mercado Pago
        $result = $this->mercadoPagoService->getPaymentInfo($paymentModel->tx_id);
        
        if (!$result['success']) {
            Log::error('Failed to retrieve payment status', [
                'payment_id' => $paymentModel->id,
                'error' => $result['error']
            ]);
            return false;
        }
        
        return $this->mercadoPagoService->isPaymentApproved($paymentModel->tx_id);
    }

    /**
     * Process approved payment
     *
     * @param Payment $paymentModel
     * @param HttpRequest $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    private function processApprovedPayment($paymentModel, $request)
    {
        // Update payment status
        $paymentModel->status = Payment::STATUS_PAGO;
        $paymentModel->save();

        // Create chat room
        $chatRoom = \App\Http\Controllers\ChatController::createChatRoomForPayment($paymentModel);
        
        Log::info('Payment approved, creating chat room', [
            'payment_id' => $paymentModel->id,
            'orcamento_id' => $paymentModel->orcamento_id,
            'chat_room_id' => $chatRoom->id,
            'room_code' => $chatRoom->room_code
        ]);

        // Dispatch notification jobs
        SendClientPaymentNotificationJob::dispatch($paymentModel->id, $chatRoom->id);
        SendProviderPaymentNotificationJob::dispatch($paymentModel->id, $chatRoom->id);
        
        // Set session for auto-authentication
        $this->setChatRoomSession($chatRoom);

        // Generate redirect URL
        $redirectUrl = route('chat.room', $chatRoom->room_code);
        
        // Log the redirect URL for debugging
        Log::info('Payment approved, returning redirect URL as JSON', [
            'payment_id' => $paymentModel->id,
            'chat_room_id' => $chatRoom->id,
            'redirect_url' => $redirectUrl
        ]);
        
        // Return JSON response with redirect URL for frontend to handle
        return response()->json([
            'success' => true,
            'redirect_url' => $redirectUrl,
            'message' => 'Payment approved and processed successfully'
        ]);
    }

    /**
     * Set chat room session for auto-authentication
     *
     * @param ChatRoom $chatRoom
     * @return void
     */
    private function setChatRoomSession($chatRoom)
    {
        try {
            // Automatically inject session for client to bypass password entry
            session([
                'authenticated_chat_room' => $chatRoom->room_code,
                'authenticated_from_payment' => true
            ]);
            
            // Save the session immediately to ensure it's available
            session()->save();
            
            // Log for debugging
            Log::info('Chat room session set', [
                'room_code' => $chatRoom->room_code,
                'authenticated_chat_room' => session('authenticated_chat_room'),
                'authenticated_from_payment' => session('authenticated_from_payment')
            ]);
        } catch (\Exception $e) {
            Log::error('Error setting chat room session', [
                'chat_room_id' => $chatRoom->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Create success response
     *
     * @param string $message
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    private function successResponse($message, $data = [])
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Create error response
     *
     * @param string $message
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    private function errorResponse($message, $statusCode = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $statusCode);
    }
}