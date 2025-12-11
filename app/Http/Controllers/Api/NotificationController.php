<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use App\Services\IdempotencyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Send a test notification
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendTestNotification(Request $request)
    {
        try {
            // Validate request data
            $request->validate([
                'phone_number' => 'required|string',
                'email' => 'required|email',
                'message' => 'required|string',
                'subject' => 'nullable|string'
            ]);

            // Get the authenticated user ID
            $userId = Auth::id();
            
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Send notification
            $result = $this->notificationService->sendNotification(
                $userId,
                $request->phone_number,
                $request->email,
                $request->message,
                $request->subject ?? 'Test Notification'
            );

            return response()->json([
                'success' => true,
                'message' => 'Notification sent successfully',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending test notification', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error sending notification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send a WhatsApp-only notification
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendWhatsAppNotification(Request $request)
    {
        try {
            // Validate request data
            $request->validate([
                'phone_number' => 'required|string',
                'message' => 'required|string'
            ]);

            // Get the authenticated user ID
            $userId = Auth::id();
            
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Generate idempotency key for this notification
            $idempotencyKey = IdempotencyService::createKey('api_whatsapp_notification', [
                'user_id' => $userId,
                'phone_number' => $request->phone_number,
                'message_hash' => md5($request->message)
            ]);

            // Send WhatsApp notification
            $result = $this->notificationService->sendWhatsAppNotification(
                $userId,
                $request->phone_number,
                $request->message,
                null,
                $idempotencyKey
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'WhatsApp notification sent successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error sending WhatsApp notification', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error sending WhatsApp notification: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send an email-only notification
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendEmailNotification(Request $request)
    {
        try {
            // Validate request data
            $request->validate([
                'email' => 'required|email',
                'subject' => 'required|string',
                'message' => 'required|string'
            ]);

            // Get the authenticated user ID
            $userId = Auth::id();
            
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Send email notification
            $result = $this->notificationService->sendEmailNotification(
                $userId,
                $request->email,
                $request->subject,
                $request->message
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Email notification sent successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error sending email notification', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error sending email notification: ' . $e->getMessage()
            ], 500);
        }
    }
}