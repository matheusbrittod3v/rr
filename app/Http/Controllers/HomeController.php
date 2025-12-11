<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Orcamento;
use App\Models\FilaOrcamento;
use App\Jobs\AssignBudgetToProviderJob;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index()
    {
        // Try to get cached services first
        $services = cache()->remember('all_services_with_relations', 300, function () {
            // Fetch all services from the database with their custom fields and marca
            return Service::with(['customFields', 'marca'])->get();
        });
        
        // Pass the services data to the welcome view
        return view('welcome', compact('services'));
    }
    
    public function requestServiceQuote(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'service_id' => 'required|exists:services,id',
            'email' => 'required|email',
            'phone' => 'required|string',
            'additional_fields' => 'sometimes|array',
        ]);
        
        try {
            // Get the service with its custom fields and marca
            $service = Service::with(['customFields', 'marca'])->find($validatedData['service_id']);
            
            // Prepare the information to be saved
            $informacoesAdicionais = [];
            
            // Add service information to the additional info
            $informacoesAdicionais[] = [
                'name' => 'Serviço',
                'value' => $service->nome_servico ?? 'Serviço desconhecido'
            ];
            
            if ($service->marca) {
                $informacoesAdicionais[] = [
                    'name' => 'Marca',
                    'value' => $service->marca->nome ?? 'Marca desconhecida'
                ];
            }
            
            // If additional fields were sent, process them
            if (isset($validatedData['additional_fields']) && is_array($validatedData['additional_fields'])) {
                foreach ($validatedData['additional_fields'] as $fieldKey => $fieldValue) {
                    // Extract the field ID from the key (e.g., custom_field_123 -> 123)
                    if (preg_match('/custom_field_(\d+)/', $fieldKey, $matches)) {
                        $fieldId = $matches[1];
                        
                        // Find the corresponding custom field for this service
                        $customField = $service->customFields->firstWhere('id', $fieldId);
                        
                        if ($customField) {
                            // Save both the field name and value
                            $fieldName = $customField->field_name ?? 'Campo desconhecido';
                            $informacoesAdicionais[] = [
                                'name' => $fieldName,
                                'value' => $fieldValue
                            ];
                        } else {
                            // If field not found, save with generic name
                            $informacoesAdicionais[] = [
                                'name' => $fieldKey,
                                'value' => $fieldValue
                            ];
                        }
                    }
                }
            }
            
            // Create a new Orcamento record
            $orcamento = new Orcamento();
            $orcamento->email = $validatedData['email'];
            $orcamento->numero = $validatedData['phone'];
            $orcamento->service_id = $validatedData['service_id']; // Save the service_id
            $orcamento->informacoes_adicionais = !empty($informacoesAdicionais) ? json_encode($informacoesAdicionais) : null;
            $orcamento->valor = null;
            $orcamento->aceito = 'nao';
            $orcamento->save();
            
            Log::info('Created orcamento', ['orcamento_id' => $orcamento->id, 'email' => $orcamento->email]);
            
            // Create a FilaOrcamento entry to queue this budget for assignment
            // This is now handled automatically by the Orcamento model's created event
            // $filaOrcamento = new FilaOrcamento();
            // $filaOrcamento->orcamento_id = $orcamento->id;
            // $filaOrcamento->save();
            
            // Log::info('Created FilaOrcamento entry', ['id' => $filaOrcamento->id, 'orcamento_id' => $orcamento->id]);
            
            // Trigger the budget assignment workflow
            // This is now handled automatically by the Orcamento model's created event
            // AssignBudgetToProviderJob::dispatch($filaOrcamento->id);
            
            // Log::info('Dispatched AssignBudgetToProviderJob', ['fila_orcamento_id' => $filaOrcamento->id]);
            
            return response()->json([
                'success' => true,
                'message' => 'Solicitação de orçamento enviada com sucesso! Entraremos em contato em breve.',
                'orcamento_id' => $orcamento->id,
                'unique_id' => null
            ]);
        } catch (\Exception $e) {
            Log::error('Error in requestServiceQuote', [
                'message' => $e->getMessage(),
                'exception' => $e
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar solicitação de orçamento: ' . $e->getMessage()
            ], 500);
        }
    }
}