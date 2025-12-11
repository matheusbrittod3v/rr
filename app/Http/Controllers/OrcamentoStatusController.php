<?php

namespace App\Http\Controllers;

use App\Models\Orcamento;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrcamentoStatusController extends Controller
{
    /**
     * Get the current status of an orçamento (budget)
     *
     * @param Orcamento $orcamento
     * @return JsonResponse
     */
    public function getStatus(Orcamento $orcamento): JsonResponse
    {
        // Check if the budget has been answered
        $status = $orcamento->status;
        $aceito = $orcamento->aceito;
        
        // Convert to the expected format for the frontend
        $responseStatus = null;
        if ($status === 'respondido') {
            $responseStatus = $aceito; // This will be 'sim' or 'nao'
        }
        
        return response()->json([
            'id' => $orcamento->id,
            'orcamento_id' => $orcamento->id,
            'status' => $responseStatus, // Will be null if not responded yet
            'aceito' => $aceito,
            'valor' => $orcamento->valor,
            'updated_at' => $orcamento->updated_at,
        ]);    }
}