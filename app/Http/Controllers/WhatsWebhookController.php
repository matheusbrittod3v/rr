<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessWhatsReplyJob;
use App\Models\FilaOrcamento as FilaOrcamentoModel;
use App\Models\Orcamento;
use App\Models\FilaPrestador;
use App\Jobs\NotifyProviderNewBudgetJob;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class WhatsWebhookController extends Controller
{
    /**
     * Receive incoming webhook from WhatsApp provider
     */
    public function receive(Request $request)
    {
        $payload = $request->all();

        // Raw body (unparsed) for debugging and auditing
        $raw = $request->getContent();
        Log::info('WhatsApp webhook received raw', ['raw' => $raw]);

        // Log the parsed payload for convenience
        Log::info('WhatsApp webhook received', ['payload' => $payload]);

        // Only process if it's a reply_message
        if (($payload['dataType'] ?? null) !== 'reply_message') {
            Log::debug('Webhook is not a reply_message, skipping', ['dataType' => $payload['dataType'] ?? null]);

            return response()->json(['status' => 'ok']);
        }

        // Extract message container (some payloads nest real message under _data)
        $messageContainer = $payload['data']['message'] ?? $payload['message'] ?? null;
        $inner = null;
        if (is_array($messageContainer) && array_key_exists('_data', $messageContainer) && is_array($messageContainer['_data'])) {
            $inner = $messageContainer['_data'];
        } elseif (is_array($messageContainer)) {
            $inner = $messageContainer;
        }

        // Extract quoted message (original message with ID) from the inner structure
        $quotedBody = $inner['quotedMsg']['body'] ?? $inner['quotedMessage']['body'] ?? null;
        // Extract reply message (current message with sim/não and valor)
        $replyBody = $inner['body'] ?? $inner['text'] ?? null;

        Log::info('Extracted message bodies', [
            'quoted_length' => strlen($quotedBody ?? ''),
            'reply_length' => strlen($replyBody ?? ''),
            'inner_keys' => is_array($inner) ? array_keys($inner) : null,
        ]);

        // Extract ID from quoted message
        $uniqueId = null;
        if ($quotedBody) {
            // Procura por padrão: *ID: KULZUUMB59* ou ID: KULZUUMB59
            if (preg_match('/\*?\s*ID\s*:\s*([A-Z0-9]+)\s*\*?/i', $quotedBody, $m)) {
                $extracted = trim($m[1]);
                // Valida: deve ter letras e números, mínimo 8 caracteres
                if (strlen($extracted) >= 8 && preg_match('/[A-Z]/i', $extracted) && preg_match('/\d/', $extracted)) {
                    $uniqueId = strtoupper($extracted);
                    Log::info('ID extracted from quoted message', ['uniqueId' => $uniqueId]);
                }
            }
            // Fallback: procura por padrão como KULZUUMB59
            if (! $uniqueId && preg_match('/\b([A-Z]+\d+)\b/i', $quotedBody, $m)) {
                $uniqueId = strtoupper($m[1]);
                Log::info('ID extracted via fallback', ['uniqueId' => $uniqueId]);
            }
        }

        // Extract status (sim/não/proximo) and valor from reply message
        $isYes = false;
        $isNo = false;
        $isNext = false;
        $valor = null;

        if ($replyBody) {
            $lower = mb_strtolower($replyBody);

            // Check for 'sim' (yes)
            if (preg_match('/\bsim\b/i', $lower) || preg_match('/^sim\s/i', $replyBody)) {
                $isYes = true;

                // Extract number after 'sim': "Sim 150" ou "Sim 150.50"
                if (preg_match('/sim\s+(\d+[\.,]?\d{0,2})/i', $replyBody, $mnum)) {
                    $valor = floatval(str_replace(',', '.', $mnum[1]));
                }
            }
            // Check for 'nao' / 'não'
            if (preg_match('/\b(nao|não)\b/i', $lower)) {
                $isNo = true;
            }
            // Check for 'proximo' / 'próximo'
            if (preg_match('/\b(proximo|próximo)\b/i', $lower)) {
                $isNext = true;
            }
        }

        // Handle "proximo" command
        if ($isNext && $uniqueId) {
            Log::info('Processing "proximo" command for budget', ['uniqueId' => $uniqueId]);
            
            // Find the budget/orcamento by unique ID
            $orcamento = Orcamento::where('id_orcamento', $uniqueId)->first();
            
            if ($orcamento) {
                // Get the current queue entry
                $filaOrcamento = $orcamento->filaOrcamento;
                
                if ($filaOrcamento) {
                    // Get the next provider in the queue
                    $nextProvider = FilaPrestador::orderBy('position')->first();
                    
                    if ($nextProvider) {
                        // Assign the budget to the next provider
                        $filaOrcamento->prestador_id = $nextProvider->user_id;
                        $filaOrcamento->save();
                        
                        // Move the provider to the end of the queue
                        $maxPosition = FilaPrestador::max('position');
                        $nextProvider->position = $maxPosition + 1;
                        $nextProvider->save();
                        
                        // Dispatch notification to the next provider
                        NotifyProviderNewBudgetJob::dispatch($filaOrcamento->id);
                        
                        Log::info('Budget reassigned to next provider', [
                            'orcamento_id' => $orcamento->id,
                            'unique_id' => $uniqueId,
                            'new_provider_id' => $nextProvider->user_id
                        ]);
                        
                        return response()->json(['status' => 'ok']);
                    } else {
                        Log::warning('No next provider found in queue', ['uniqueId' => $uniqueId]);
                    }
                } else {
                    Log::warning('No queue entry found for budget', ['uniqueId' => $uniqueId]);
                }
            } else {
                Log::warning('Budget not found for unique ID', ['uniqueId' => $uniqueId]);
            }
        }

        // Determine final status for regular replies
        $status = 'sem_resposta';
        if ($isYes && ! $isNo && ! $isNext) {
            $status = 'sim';
            
            // Link prestador to orcamento explicitly when confirmed "YES"
            if ($uniqueId) {
                try {
                    $orcamento = Orcamento::where('id_orcamento', $uniqueId)->first();
                    if ($orcamento) {
                         // Load the queue entry to get the provider
                         $fila = $orcamento->filaOrcamento; // Using the relationship
                         if ($fila && $fila->prestador_id) {
                             $orcamento->prestador_id = $fila->prestador_id;
                             $orcamento->save();
                             Log::info('Prestador linked to Orcamento via Webhook YES', [
                                 'orcamento_id' => $orcamento->id,
                                 'prestador_id' => $fila->prestador_id
                             ]);
                         } else {
                             Log::warning('Webhook YES but no prestador in fila for orcamento', ['uniqueId' => $uniqueId]);
                         }
                    }
                } catch (\Exception $e) {
                    Log::error('Error linking prestador in webhook', ['error' => $e->getMessage()]);
                }
            }
        } elseif ($isNo && ! $isYes && ! $isNext) {
            $status = 'nao';
        }

        Log::info('Extracted reply info from webhook', [
            'id' => $uniqueId,
            'status' => $status,
            'valor' => $valor,
            'isYes' => $isYes,
            'isNo' => $isNo,
            'isNext' => $isNext,
        ]);

        // Dispatch job with extracted info only
        if ($uniqueId && !$isNext) {
            ProcessWhatsReplyJob::dispatch($uniqueId, $status, $valor);
        } elseif (!$uniqueId) {
            Log::warning('Could not extract ID from webhook, job not dispatched');
        }

        return response()->json(['status' => 'ok']);
    }
}