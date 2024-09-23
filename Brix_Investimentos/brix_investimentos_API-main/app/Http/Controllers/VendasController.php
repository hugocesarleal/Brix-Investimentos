<?php

namespace App\Http\Controllers;

use App\Models\Vendas;
use App\Models\CadastrarAtivo;
use App\Models\HistoricoPrecoAtivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class VendasController extends Controller
{
    public function index()
    {
        $vendas = Vendas::select('id_ticker', 'id_compra', 'quantidade', 'valor_unitario', 'valor_total', 'created_at', 'updated_at')->get();
        return response()->json($vendas);
    }

    public function store(Request $request)
    {
        // Log the incoming request data
        Log::info('Request Data:', $request->all());

        // Validate the incoming request
        $validator = Validator::make($request->all(), Vendas::rules());

        if ($validator->fails()) {
            Log::info('Validation failed:', $validator->errors()->toArray());
            return response()->json($validator->errors(), 400);
        }

        DB::beginTransaction();
        try {
            // Verificar se o ativo existe
            $ativo = CadastrarAtivo::find($request->id_ticker);
            if (!$ativo) {
                return response()->json(['error' => 'Ativo não encontrado'], 404);
            }

            // Verificar quantidade disponível
            if ($ativo->quantidade < $request->quantidade) {
                return response()->json(['error' => 'Quantidade insuficiente'], 400);
            }

            // Criação da venda
            $venda = Vendas::create([
                'id_ticker' => $request->id_ticker,
                'id_compra' => $request->id_compra,
                'quantidade' => $request->quantidade,
                'valor_unitario' => $request->valor_unitario,
                'valor_total' => $request->valor_total
            ]);

            // Atualizar o ativo
            $ativo->quantidade -= $request->quantidade;
            $ativo->save();

            // Criação do histórico de preço
            HistoricoPrecoAtivo::create([
                'ticker' => $ativo->ticker,
                'data_ativo' => now(),
                'open' => $request->valor_unitario,
                'low' => $request->valor_unitario,
                'high' => $request->valor_unitario,
                'close' => $request->valor_unitario,
                'volume' => -$request->quantidade,
            ]);

            DB::commit();
            return response()->json($venda, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error during transaction:', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
