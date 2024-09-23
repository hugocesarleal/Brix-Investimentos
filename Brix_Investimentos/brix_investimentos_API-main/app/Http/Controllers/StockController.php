<?php

namespace App\Http\Controllers;

use App\Models\HistoricoPrecoAtivo; // Importar o modelo HistoricoPrecoAtivo
use Illuminate\Http\Request;
use Illuminate\Http\Response; // Importar a classe Response
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class StockController extends Controller
{
    public function getStockHistory(Request $request)
    {
        $scriptPath = base_path('python_scripts/get_stock_history.py');
        $process = new Process(['python3', $scriptPath]);

        try {
            $process->run();

            if (!$process->isSuccessful()) {
                Log::error('Processo falhou: ' . $process->getErrorOutput());
                return response()->json(['error' => 'Erro ao processar os dados'], 500);
            }

            $output = $process->getOutput();
            $data = json_decode($output, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Erro ao decodificar JSON: ' . json_last_error_msg());
                return response()->json(['error' => 'Erro ao processar os dados'], 500);
            }

            return response()->json($data);
        } catch (ProcessFailedException $exception) {
            Log::error('Exceção do processo: ' . $exception->getMessage());
            return response()->json(['error' => 'Erro ao executar o script'], 500);
        }
    }

    // Função para obter todos os históricos de preços
    public function getAllStockHistories()
    {
        try {
            $historicos = HistoricoPrecoAtivo::all();
            return response()->json($historicos);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao listar o histórico de preços: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
