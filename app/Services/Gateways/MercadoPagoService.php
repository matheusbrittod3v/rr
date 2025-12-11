<?php

namespace App\Services\Gateways;

use App\Models\GatewayPagamento;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MercadoPagoService
{
    protected $accessToken;
    protected $baseUrl;
    protected $notificationUrl;

    public function __construct()
    {
        // Get the Mercado Pago gateway from the database
        $gateway = GatewayPagamento::where('name', 'Mercado Pago')->first();
        
        if ($gateway) {
            $this->accessToken = $gateway->token;
            $this->baseUrl = $gateway->url ?? 'https://api.mercadopago.com';
        } else {
            // Fallback to environment variables if gateway not found
            $this->accessToken = config('services.mercadopago.access_token');
            $this->baseUrl = config('services.mercadopago.base_url', 'https://api.mercadopago.com');
        }
        
        $this->notificationUrl = config('services.mercadopago.notification_url');
        
        // Log the configuration
        Log::info('MercadoPagoService initialized', [
            'access_token' => $this->accessToken ? 'Set (last 4 chars: ' . substr($this->accessToken, -4) . ')' : 'Not set',
            'base_url' => $this->baseUrl,
            'notification_url' => $this->notificationUrl,
        ]);
    }

    /**
     * Cria um pagamento via Pix
     *
     * @param array $paymentData
     * @return array
     */
    public function createPixPayment(array $paymentData)
    {
        // Ensure we have an access token
        if (empty($this->accessToken)) {
            Log::error('MercadoPagoService: Access token not configured');
            return [
                'success' => false,
                'error' => 'Access token not configured',
            ];
        }

        // Generate an idempotency key
        $idempotencyKey = Str::uuid()->toString();
        
        // Log the request details
        Log::info('MercadoPagoService: Creating Pix payment', [
            'url' => "{$this->baseUrl}/v1/payments",
            'access_token' => $this->accessToken ? 'Set (last 4 chars: ' . substr($this->accessToken, -4) . ')' : 'Not set',
            'idempotency_key' => $idempotencyKey,
            'payment_data' => $paymentData,
        ]);

        try {
            // Ensure transaction_amount is numeric (float). Some callers may pass formatted strings.
            $transactionAmount = 0.0;
            if (isset($paymentData['amount'])) {
                // Normalize comma decimals and remove thousands separators
                $raw = $paymentData['amount'];
                if (!is_numeric($raw)) {
                    $normalized = str_replace(['.', ','], ['', '.'], (string)$raw);
                    // If the above replaced both separators, fallback to floatval
                    $transactionAmount = floatval($normalized);
                } else {
                    $transactionAmount = (float)$raw;
                }
            }

            $response = Http::withToken($this->accessToken)
                ->withHeaders([
                    'X-Idempotency-Key' => $idempotencyKey,
                ])
                ->post("{$this->baseUrl}/v1/payments", [
                    'transaction_amount' => $transactionAmount,
                    'description' => $paymentData['description'],
                    'payment_method_id' => 'pix',
                    'payer' => [
                        'email' => $paymentData['payer_email'] ?? null,
                        'first_name' => $paymentData['payer_first_name'] ?? null,
                        'last_name' => $paymentData['payer_last_name'] ?? null,
                    ],
                    'notification_url' => $this->notificationUrl,
                    'external_reference' => $paymentData['external_reference'] ?? null,
                ]);

            // Log the response
            Log::info('MercadoPagoService: Payment response', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body' => $response->body(),
                'transaction_amount_sent' => $transactionAmount,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to create payment',
                'details' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('MercadoPagoService: Pix Payment Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get payment information from Mercado Pago
     *
     * @param string $paymentId
     * @return array
     */
    public function getPaymentInfo(string $paymentId)
    {
        // Ensure we have an access token
        if (empty($this->accessToken)) {
            Log::error('MercadoPagoService: Access token not configured for getPaymentInfo');
            return [
                'success' => false,
                'error' => 'Access token not configured',
            ];
        }

        // Log the request details
        Log::info('MercadoPagoService: Getting payment info', [
            'url' => "{$this->baseUrl}/v1/payments/{$paymentId}",
            'access_token' => $this->accessToken ? 'Set (last 4 chars: ' . substr($this->accessToken, -4) . ')' : 'Not set',
            'payment_id' => $paymentId,
        ]);

        try {
            $response = Http::withToken($this->accessToken)
                ->get("{$this->baseUrl}/v1/payments/{$paymentId}");

            // Log the response
            Log::info('MercadoPagoService: Payment info response', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get payment info',
                'details' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('MercadoPagoService: Payment Info Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Gera o QR Code do Pix
     *
     * @param array $paymentData
     * @return array
     */
    public function generatePixQrCode(array $paymentData)
    {
        Log::info('MercadoPagoService: Generating Pix QR Code', [
            'payment_data' => $paymentData,
        ]);
        
        $paymentResult = $this->createPixPayment($paymentData);

        if (!$paymentResult['success']) {
            Log::error('MercadoPagoService: Failed to create payment for QR Code', [
                'error' => $paymentResult['error'],
                'details' => $paymentResult['details'] ?? null,
            ]);
            return $paymentResult;
        }

        $payment = $paymentResult['data'];

        // Verifica se o pagamento tem as informações do Pix
        if (isset($payment['point_of_interaction']['transaction_data'])) {
            $transactionData = $payment['point_of_interaction']['transaction_data'];
            
            Log::info('MercadoPagoService: Pix QR Code generated successfully', [
                'payment_id' => $payment['id'],
            ]);
            
            return [
                'success' => true,
                'qr_code' => $transactionData['qr_code'],
                'qr_code_base64' => $transactionData['qr_code_base64'],
                'ticket_url' => $transactionData['ticket_url'],
                'payment_id' => $payment['id'],
            ];
        }

        Log::error('MercadoPagoService: Pix data not found in payment response', [
            'payment_response' => $payment,
        ]);

        return [
            'success' => false,
            'error' => 'Pix data not found in payment response',
        ];
    }

    /**
     * Verifica se um pagamento foi aprovado
     *
     * @param string $paymentId
     * @return bool
     */
    public function isPaymentApproved(string $paymentId)
    {
        $paymentInfo = $this->getPaymentInfo($paymentId);

        if (!$paymentInfo['success']) {
            return false;
        }

        $status = $paymentInfo['data']['status'] ?? null;
        return $status === 'approved';
    }

    /**
     * Processa a notificação de pagamento
     *
     * @param string $topic
     * @param string $id
     * @return array
     */
    public function handleNotification(string $topic, string $id)
    {
        if ($topic === 'payment') {
            return $this->getPaymentInfo($id);
        }

        return [
            'success' => false,
            'error' => 'Unsupported notification topic',
        ];
    }
}