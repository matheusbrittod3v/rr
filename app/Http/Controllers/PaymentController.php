<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use App\Models\Tool;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\GatewayPagamento;
use App\Models\WhatsApi;
use App\Services\IdempotencyService;
use App\Services\Gateways\MercadoPagoService;
use App\Services\NotificationService;
use App\Events\PaymentStatusUpdated;

class PaymentController extends Controller
{
    protected $mercadoPagoService;
    protected $notificationService;

    public function __construct(MercadoPagoService $mercadoPagoService = null, NotificationService $notificationService = null)
    {
        $this->mercadoPagoService = $mercadoPagoService ?? new MercadoPagoService();
        $this->notificationService = $notificationService;
    }

    /**
     * Processa o pagamento via Pix
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function processPixPayment(Request $request)
    {
        try {
            Log::info('PaymentController: Processing Pix payment request', [
                'request_data' => $request->all(),
            ]);
            
            // Validar os dados recebidos
            $request->validate([
                'tool_id' => 'required|exists:tools,id',
                'whatsapp' => 'required|string',
                'email' => 'required|email',
            ]);

            // Obter a ferramenta
            $tool = Tool::findOrFail($request->tool_id);
            Log::info('PaymentController: Tool found', [
                'tool_id' => $tool->id,
                'tool_name' => $tool->nome,
                'tool_price' => $tool->price,
            ]);

            // Obter o gateway de pagamento (Mercado Pago)
            $gateway = GatewayPagamento::where('name', 'Mercado Pago')->first();
            
            if (!$gateway) {
                Log::error('PaymentController: Gateway de pagamento não encontrado');
                return response()->json([
                    'success' => false,
                    'message' => 'Gateway de pagamento não encontrado',
                ], 404);
            }
            
            if (!$gateway->token) {
                Log::error('PaymentController: Gateway de pagamento não configurado corretamente - token não encontrado', [
                    'gateway_id' => $gateway->id,
                    'gateway_name' => $gateway->name,
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Gateway de pagamento não configurado corretamente',
                ], 404);
            }

            Log::info('PaymentController: Gateway found', [
                'gateway_id' => $gateway->id,
                'gateway_name' => $gateway->name,
                'gateway_url' => $gateway->url,
                'token_set' => !empty($gateway->token),
            ]);

            // Preparar os dados para o pagamento
            $paymentData = [
                'amount' => (float) $tool->price,
                'description' => "Aluguel da ferramenta: {$tool->nome}",
                'payer_email' => $request->email,
                'payer_first_name' => 'Cliente',
                'payer_last_name' => 'FRP Rent',
                'external_reference' => "TOOL_RENTAL_{$tool->id}_" . time(),
            ];

            Log::info('PaymentController: Preparing payment data', [
                'payment_data' => $paymentData,
            ]);

            // Gerar o pagamento via Pix usando o serviço do Mercado Pago
            $result = $this->mercadoPagoService->generatePixQrCode($paymentData);

            if (!$result['success']) {
                Log::error('PaymentController: Erro ao gerar pagamento via Pix', [
                    'error' => $result['error'],
                    'tool_id' => $tool->id,
                    'data' => $paymentData,
                    'details' => $result['details'] ?? null,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Falha ao gerar o pagamento via Pix',
                    'error' => $result['error'],
                ], 500);
            }

            // Criar o registro de pagamento no banco de dados
            $payment = Payment::create([
                'tx_id' => $result['payment_id'],
                'valor' => $tool->price,
                'gateway_id' => $gateway->id,
                'status' => Payment::STATUS_NAO_PAGO,
                'tool_id' => $tool->id,
                'number_whatsapp' => $request->whatsapp,
                'email' => $request->email,
            ]);

            Log::info('PaymentController: Payment record created', [
                'payment_id' => $payment->id,
                'tx_id' => $payment->tx_id,
            ]);

            // Send notification to user about payment initiation
            $this->sendPaymentInitiationNotification($payment, $gateway);

            // Retornar os dados do QR Code e cópia e cola
            return response()->json([
                'success' => true,
                'message' => 'Pagamento gerado com sucesso',
                'data' => [
                    'qr_code_base64' => $result['qr_code_base64'],
                    'qr_code' => $result['qr_code'],
                    'ticket_url' => $result['ticket_url'],
                    'payment_id' => $result['payment_id'],
                    'payment_record_id' => $payment->id,
                    'amount' => $tool->price,
                    'tool_name' => $tool->nome,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('PaymentController: Erro no processamento do pagamento', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno ao processar o pagamento',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Send notification when payment is initiated
     *
     * @param Payment $payment
     * @param GatewayPagamento $gateway
     * @return void
     */
    private function sendPaymentInitiationNotification(Payment $payment, GatewayPagamento $gateway)
    {
        try {
            if ($payment->number_whatsapp && $payment->email) {
                // Get user ID from gateway
                $userId = $gateway->user_id ?? 1; // Default to user ID 1 if not found
                
                $toolName = $payment->tool ? $payment->tool->nome : 'Unknown Tool';
               $message = "⚡ Seu pagamento de *R$" . number_format($payment->valor, 2, ',', '.') . "* para a ferramenta *{$toolName}* foi iniciado.\n⏳ Aguarde a aprovação para receber os dados de acesso.";

                // Generate idempotency key for this notification
                $idempotencyKey = IdempotencyService::createKey('payment_initiation_notification', [
                    'payment_id' => $payment->id,
                    'user_id' => $userId
                ]);

                $this->notificationService->sendNotification(
                    $userId,
                    $payment->number_whatsapp,
                    $payment->email,
                    $message,
                    'Pagamento Iniciado - FRP Rent',
                    $idempotencyKey
                );
            }
        } catch (\Exception $e) {
            Log::error('PaymentController: Error sending payment initiation notification', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

   /**
 * Processa a solicitação de reembolso
 *
 * @param Request $request
 * @return \Illuminate\Http\JsonResponse
 */
public function processRefundRequest(Request $request)
{
    try {
        Log::info('PaymentController: Processing refund request', [
            'request_data' => $request->all(),
        ]);

        // Validar os dados recebidos
        $request->validate([
            'numero' => 'required|string',
            'email' => 'required|email',
            'relato_problema' => 'required|string',
            'id_pedido' => 'required|string',
            'link' => 'required|url',
        ]);

        // Definir tool_id fixo como 1
        $toolId = 1;

        // Verificar se o pedido existe na tabela de pagamentos
        $payment = Payment::where('id', $request->id_pedido)->first();

        if (!$payment) {
            Log::warning('PaymentController: Refund order not found', [
                'order_id' => $request->id_pedido,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Pedido não encontrado. Verifique o ID do pedido.',
            ], 400);
        }

        // Verificar se o email e número correspondem aos do pagamento
        $errors = [];

        if (strtolower($request->email) !== strtolower($payment->email)) {
            $errors[] = 'O email informado não corresponde ao email do pedido original.';
        }

        if ($request->numero !== $payment->number_whatsapp) {
            $errors[] = 'O número informado não corresponde ao número do pedido original.';
        }

        if (!empty($errors)) {
            Log::warning('PaymentController: Refund validation failed', [
                'order_id' => $request->id_pedido,
                'errors' => $errors,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Dados não correspondem ao pedido original.',
                'errors' => $errors,
            ], 400);
        }

        // Criar o registro de reembolso
        $refund = Refund::create([
            'link' => $request->link,
            'id_pedido' => $request->id_pedido,
            'numero' => $request->numero,
            'email' => $request->email,
            'relato_problema' => $request->relato_problema,
            'tool_id' => $toolId, // FIXO
        ]);

        // Alterar status do pagamento para contestacao
        $payment->status = 'contestacao';
        $payment->save();

        Log::info('PaymentController: Refund record created and payment status updated', [
            'refund_id' => $refund->id,
            'payment_id' => $payment->id,
            'new_status' => $payment->status,
        ]);

        // Enviar notificação via WhatsApp
        try {
            $gateway = GatewayPagamento::where('name', 'Mercado Pago')->first();
            $userId = $gateway->user_id ?? 1;

            $whatsApi = WhatsApi::where('user_id', $userId)->first();

            if ($whatsApi && $whatsApi->numero_instancia) {
                $message = "Nova solicitação de reembolso:\n\n";
                $message .= "ID do Pedido: {$request->id_pedido}\n";
                $message .= "Link para vídeo comprobatório: {$request->link}\n";
                $message .= "Relato do problema: {$request->relato_problema}\n";
                $message .= "Valor: R$ " . number_format($payment->valor, 2, ',', '.') . "\n";
                $message .= "Número WhatsApp: {$request->numero}\n";
                $message .= "Email: {$request->email}";

                // Generate idempotency key for this refund notification
                $refundIdempotencyKey = IdempotencyService::createKey('refund_notification', [
                    'refund_id' => $refund->id,
                    'user_id' => $userId
                ]);

                $notificationResult = $this->notificationService->sendWhatsAppNotification(
                    $userId,
                    $whatsApi->numero_instancia,
                    $message,
                    null,
                    $refundIdempotencyKey
                );

                if ($notificationResult['success']) {
                    Log::info('PaymentController: Refund notification sent successfully', [
                        'refund_id' => $refund->id,
                        'instance_number' => $whatsApi->numero_instancia,
                    ]);
                } else {
                    Log::warning('PaymentController: Failed to send refund notification', [
                        'refund_id' => $refund->id,
                        'error' => $notificationResult['message'],
                    ]);
                }
            } else {
                Log::warning('PaymentController: WhatsApp API configuration or instance number not found', [
                    'user_id' => $userId,
                    'refund_id' => $refund->id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('PaymentController: Error sending refund notification', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Solicitação de reembolso enviada com sucesso. Nossa equipe irá analisar sua solicitação.',
            'data' => [
                'refund_id' => $refund->id,
            ],
        ]);
    } catch (\Exception $e) {
        Log::error('PaymentController: Erro no processamento da solicitação de reembolso', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'request' => $request->all(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Erro interno ao processar a solicitação de reembolso',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    /**
     * Generate PIX payment for a budget (orcamento)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateOrcamentoPixPayment(Request $request)
    {
        try {
            $validated = $request->validate([
                'orcamento_id' => 'required|exists:orcamentos,id',
            ]);

            $orcamento = \App\Models\Orcamento::findOrFail($validated['orcamento_id']);

            // Validate that the budget has a value
            if (!$orcamento->valor || $orcamento->valor <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Orçamento inválido. Valor não definido.',
                ], 422);
            }

            Log::info('PaymentController: Generating PIX payment for budget', [
                'orcamento_id' => $orcamento->id,
                'valor' => $orcamento->valor,
                'email' => $orcamento->email,
            ]);

            // Create payment data for Mercado Pago
            $paymentData = [
                'amount' => $orcamento->valor,
                'description' => "Serviço de Desbloqueio - Orçamento #{$orcamento->id}",
                'payer_email' => $orcamento->email,
                'payer_first_name' => 'Cliente',
                'payer_last_name' => 'renttool',
                'external_reference' => "orcamento_{$orcamento->id}",
            ];

            // Generate PIX QR Code via Mercado Pago
            $pixResponse = $this->mercadoPagoService->generatePixQrCode($paymentData);

            if (!$pixResponse['success']) {
                Log::error('PaymentController: Failed to generate PIX payment', [
                    'orcamento_id' => $orcamento->id,
                    'error' => $pixResponse['error'],
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao gerar pagamento PIX. Tente novamente.',
                ], 500);
            }

            // Get or create Mercado Pago gateway
            $gateway = GatewayPagamento::where('name', 'Mercado Pago')->first();
            if (!$gateway) {
                $gateway = GatewayPagamento::create([
                    'name' => 'Mercado Pago',
                    'url' => 'https://api.mercadopago.com',
                ]);
            }

            // Save payment record to database
            $payment = Payment::create([
                'orcamento_id' => $orcamento->id,
                'gateway_id' => $gateway->id,
                'tx_id' => $pixResponse['payment_id'],
                'valor' => $orcamento->valor,
                'status' => Payment::STATUS_NAO_PAGO,
                'number_whatsapp' => $orcamento->numero,
                'email' => $orcamento->email,
                'metadata' => json_encode([
                    'qr_code' => $pixResponse['qr_code'],
                    'ticket_url' => $pixResponse['ticket_url'] ?? null,
                    'tipo' => 'orcamento',
                ]),
            ]);

            Log::info('PaymentController: PIX payment generated successfully', [
                'orcamento_id' => $orcamento->id,
                'payment_id' => $payment->id,
                'mercado_pago_id' => $pixResponse['payment_id'],
                'valor' => $orcamento->valor,
            ]);

            return response()->json([
                'success' => true,
                'qr_code' => $pixResponse['qr_code_base64'],
                'qr_code_raw' => $pixResponse['qr_code'],
                'payment_id' => $payment->id,
                'mercado_pago_id' => $pixResponse['payment_id'],
                'valor' => number_format($orcamento->valor, 2, ',', '.'),
                'message' => 'QR Code gerado com sucesso. Escaneie ou copie o código para pagar.',
            ]);
        } catch (\Exception $e) {
            Log::error('PaymentController: Payment generation error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar pagamento. Por favor, tente novamente.',
            ], 500);
        }
    }

    /**
     * Get payment status
     *
     * @param Payment $payment
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPaymentStatus(Payment $payment)
    {
        try {
            Log::info('PaymentController: Checking payment status', [
                'payment_id' => $payment->id,
                'tx_id' => $payment->tx_id,
            ]);

            $paymentInfo = $this->mercadoPagoService->getPaymentInfo($payment->tx_id);

            if (!$paymentInfo['success']) {
                return response()->json([
                    'success' => false,
                    'status' => $payment->status,
                    'message' => 'Não foi possível verificar o status',
                ]);
            }

            $mpPayment = $paymentInfo['data'];
            $status = $mpPayment['status'] ?? 'unknown';

            // Map Mercado Pago status to our status
            $mappedStatus = $status;
            if ($status === 'approved') {
                $mappedStatus = 'pago'; // Changed from 'pagto' to match the constant
            } elseif ($status === 'pending') {
                $mappedStatus = 'pendente';
            } elseif ($status === 'rejected') {
                $mappedStatus = 'rejeitado';
            }

            // Update payment status in database if changed
            if ($mappedStatus !== $payment->status) {
                $oldStatus = $payment->status;
                $payment->update(['status' => $mappedStatus]);
                Log::info('PaymentController: Payment status updated', [
                    'payment_id' => $payment->id,
                    'old_status' => $oldStatus,
                    'new_status' => $mappedStatus,
                ]);
                
                // Dispatch event for payment status update
                event(new PaymentStatusUpdated($payment, $mappedStatus, 'Payment status updated from ' . $oldStatus . ' to ' . $mappedStatus));
                
                // Create chat room if payment is approved
                if ($mappedStatus === 'pago') {
                    \App\Http\Controllers\ChatController::createChatRoomForPayment($payment);
                    // Dispatch event for payment approval
                    event(new PaymentStatusUpdated($payment, 'approved', 'Payment approved and chat room created'));
                }
            }

            return response()->json([
                'success' => true,
                'status' => $mappedStatus,
                'payment_id' => $payment->id,
                'valor' => $payment->valor,
            ]);
        } catch (\Exception $e) {
            Log::error('PaymentController: Payment status check error', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao verificar status do pagamento.',
            ], 500);
        }
    }

    /**
     * Exibe o histórico de pagamentos do usuário
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function showHistory(Request $request)
    {
        // Obter o session_uuid do cookie
        $toolSession = $request->cookie('tool_session');
        
        if (!$toolSession) {
            // Se não houver cookie, retornar histórico vazio
            return view('history', ['payments' => new Collection()]);
        }
        
        // Decodificar os dados da sessão
        $sessionData = json_decode($toolSession, true);
        
        if (!isset($sessionData['session_id'])) {
            // Se não houver session_id no cookie, retornar histórico vazio
            return view('history', ['payments' => new Collection()]);
        }
        
        $sessionUuid = $sessionData['session_id'];
        
        // Obter os pagamentos com base no session_uuid
        $payments = Payment::where('session_uuid', $sessionUuid)
            ->with('tool') // Carregar a relação com a ferramenta
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('history', ['payments' => $payments]);
    }
}