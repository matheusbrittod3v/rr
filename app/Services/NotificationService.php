<?php

namespace App\Services;

use App\Models\EmailConfig;
use App\Models\WhatsApi;
use App\Services\IdempotencyService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

class NotificationService
{
    /**
     * Send notification via WhatsApp
     *
     * @param int $userId
     * @param string $phoneNumber
     * @param string $message
     * @param WhatsApi|null $whatsApiInstance Optional WhatsApp API instance to use
     * @param string|null $idempotencyKey Optional idempotency key to prevent duplicate messages
     * @return array
     */
    public static function sendWhatsAppNotification(int $userId, string $phoneNumber, string $message, $whatsApiInstance = null, string $idempotencyKey = null): array
    {
        // Generate idempotency key if not provided
        if (!$idempotencyKey) {
            $idempotencyKey = IdempotencyService::createKey('whatsapp_notification', [
                'user_id' => $userId,
                'phone_number' => $phoneNumber,
                'message_hash' => md5($message)
            ]);
        }

        // Check if this notification has already been sent
        if (IdempotencyService::hasBeenProcessed($idempotencyKey, 'whatsapp_notification')) {
            Log::info('Idempotency check: WhatsApp notification already sent', [
                'idempotency_key' => $idempotencyKey,
                'user_id' => $userId,
                'phone_number' => $phoneNumber
            ]);
            
            return [
                'success' => true,
                'message' => 'WhatsApp notification already sent (idempotency check)'
            ];
        }

        try {
            // Get WhatsApp API configuration for the user
            $whatsApi = $whatsApiInstance ?: WhatsApi::where('user_id', $userId)->first();
            
            if (!$whatsApi) {
                return [
                    'success' => false,
                    'message' => 'WhatsApp API configuration not found for user'
                ];
            }
            
            // Build the URL using only the base host - the service will handle the rest
            // Remove any trailing slashes and append the standard endpoint
            $url = rtrim($whatsApi->host, '/') . '/client/sendMessage/' . $whatsApi->instance_name;
            
            // Prepare the request data
            $requestData = [
                'chatId' => $phoneNumber . '@c.us',
                'contentType' => $whatsApi->type,
                'content' => $message
            ];
            
            // Prepare headers - always include these standard headers
            $headers = [
                'accept' => 'application/json',
                'Content-Type' => 'application/json',
            ];
            
            // Add authentication headers based on authentication type
            switch ($whatsApi->authenticate) {
                case 'bearer':
                    $headers['Authorization'] = 'Bearer ' . $whatsApi->key;
                    break;
                case 'x-api-key':
                    $headers['x-api-key'] = $whatsApi->key;
                    break;
                case 'basic':
                    $headers['Authorization'] = 'Basic ' . base64_encode($whatsApi->key);
                    break;
            }
            
            // Make the HTTP request
            $response = Http::withHeaders($headers)->post($url, $requestData);
            
            if ($response->successful()) {
                Log::info('WhatsApp notification sent successfully', [
                    'user_id' => $userId,
                    'phone_number' => $phoneNumber,
                    'message' => $message
                ]);
                
                // Mark as processed
                IdempotencyService::markAsProcessed($idempotencyKey, 'whatsapp_notification', [
                    'user_id' => $userId,
                    'phone_number' => $phoneNumber,
                    'message' => $message,
                    'response_status' => $response->status()
                ]);
                
                return [
                    'success' => true,
                    'message' => 'WhatsApp notification sent successfully'
                ];
            } else {
                Log::error('Failed to send WhatsApp notification', [
                    'user_id' => $userId,
                    'phone_number' => $phoneNumber,
                    'status' => $response->status(),
                    'response_body' => $response->body()
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Failed to send WhatsApp notification. Status: ' . $response->status()
                ];
            }
        } catch (\Exception $e) {
            Log::error('Exception occurred while sending WhatsApp notification', [
                'user_id' => $userId,
                'phone_number' => $phoneNumber,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Exception occurred while sending WhatsApp notification: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Send notification via Email
     *
     * @param int $userId
     * @param string $emailAddress
     * @param string $subject
     * @param string $message
     * @param string|null $idempotencyKey Optional idempotency key to prevent duplicate emails
     * @return array
     */
    public function sendEmailNotification(int $userId, string $emailAddress, string $subject, string $message, string $idempotencyKey = null): array
    {
        // Generate idempotency key if not provided
        if (!$idempotencyKey) {
            $idempotencyKey = IdempotencyService::createKey('email_notification', [
                'user_id' => $userId,
                'email_address' => $emailAddress,
                'subject_hash' => md5($subject),
                'message_hash' => md5($message)
            ]);
        }

        // Check if this notification has already been sent
        if (IdempotencyService::hasBeenProcessed($idempotencyKey, 'email_notification')) {
            Log::info('Idempotency check: Email notification already sent', [
                'idempotency_key' => $idempotencyKey,
                'user_id' => $userId,
                'email_address' => $emailAddress
            ]);
            
            return [
                'success' => true,
                'message' => 'Email notification already sent (idempotency check)'
            ];
        }

        try {
            // Get email configuration for the user
            $emailConfig = EmailConfig::where('user_id', $userId)->first();
            
            if (!$emailConfig) {
                return [
                    'success' => false,
                    'message' => 'Email configuration not found for user'
                ];
            }
            
            // Configure mail settings dynamically
            config([
                'mail.mailers.smtp.host' => $emailConfig->host,
                'mail.mailers.smtp.port' => $emailConfig->port,
                'mail.mailers.smtp.username' => $emailConfig->username,
                'mail.mailers.smtp.password' => $emailConfig->password,
                'mail.mailers.smtp.encryption' => $emailConfig->encryption,
                'mail.from.address' => $emailConfig->from_address,
                'mail.from.name' => $emailConfig->from_name,
            ]);

            // Send the email
            Mail::raw($message, function (Message $mailMessage) use ($emailAddress, $subject, $emailConfig) {
                $mailMessage->to($emailAddress)
                    ->subject($subject)
                    ->from($emailConfig->from_address, $emailConfig->from_name);
            });
            
            Log::info('Email notification sent successfully', [
                'user_id' => $userId,
                'email_address' => $emailAddress,
                'subject' => $subject
            ]);
            
            // Mark as processed
            IdempotencyService::markAsProcessed($idempotencyKey, 'email_notification', [
                'user_id' => $userId,
                'email_address' => $emailAddress,
                'subject' => $subject,
                'message_hash' => md5($message)
            ]);
            
            return [
                'success' => true,
                'message' => 'Email notification sent successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Exception occurred while sending email notification', [
                'user_id' => $userId,
                'email' => $emailAddress,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Exception occurred while sending email notification: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Send notification via both WhatsApp and Email
     *
     * @param int $userId
     * @param string $phoneNumber
     * @param string $emailAddress
     * @param string $message
     * @param string $subject
     * @param string|null $idempotencyKey Optional idempotency key to prevent duplicate notifications
     * @return array
     */
    public function sendNotification(int $userId, string $phoneNumber, string $emailAddress, string $message, string $subject = 'Notification', string $idempotencyKey = null): array
    {
        // Generate idempotency key if not provided
        if (!$idempotencyKey) {
            $idempotencyKey = IdempotencyService::createKey('combined_notification', [
                'user_id' => $userId,
                'phone_number' => $phoneNumber,
                'email_address' => $emailAddress,
                'subject_hash' => md5($subject),
                'message_hash' => md5($message)
            ]);
        }

        $whatsappResult = $this->sendWhatsAppNotification($userId, $phoneNumber, $message, null, $idempotencyKey . '_whatsapp');
        $emailResult = $this->sendEmailNotification($userId, $emailAddress, $subject, $message, $idempotencyKey . '_email');
        
        return [
            'whatsapp' => $whatsappResult,
            'email' => $emailResult,
            'success' => $whatsappResult['success'] && $emailResult['success']
        ];
    }
}