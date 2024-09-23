<?php

namespace App\Http\Controllers;

use App\Models\Compras;
use App\Models\CadastrarAtivo;
use App\Models\HistoricoPrecoAtivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ComprasController extends Controller
{
    public function index()
    {
        $compras = Compras::all(['id_ticker', 'quantidade', 'valor_unitario', 'valor_total', 'created_at', 'updated_at']);
        return response()->json($compras);
    }

    public function store(Request $request)
    {
        // Log the incoming request data
        Log::info('Request Data:', $request->all());

        $validator = Validator::make($request->all(), Compras::rules());

        if ($validator->fails()) {
            Log::info('Validation failed:', $validator->errors()->toArray());
            return response()->json($validator->errors(), 400);
        }

        DB::beginTransaction();
        try {
            // Check if the id_ticker exists
            $ativo = CadastrarAtivo::find($request->id_ticker);
            if (!$ativo) {
                return response()->json(['error' => 'Ativo não encontrado'], 404);
            }

            // Criação da compra
            $compra = Compras::create([
                'id_ticker' => $request->id_ticker,
                'quantidade' => $request->quantidade,
                'valor_unitario' => $request->valor_unitario,
                'valor_total' => $request->valor_total,
                'created_at' => now(), // Adiciona a data da compra
                'updated_at' => now(),
            ]);

            // Atualizar o ativo
            $quantidade_antiga = $ativo->quantidade;
            $ativo->quantidade += $request->quantidade;
            $ativo->preco_compra = ($ativo->preco_compra * $quantidade_antiga + $request->valor_total) / $ativo->quantidade;
            $ativo->save();

            // Criação do histórico de preço
            HistoricoPrecoAtivo::create([
                'ticker' => $ativo->ticker,
                'data_ativo' => now(),
                'open' => $request->valor_unitario,
                'low' => $request->valor_unitario,
                'high' => $request->valor_unitario,
                'close' => $request->valor_unitario,
                'volume' => $request->quantidade,
            ]);

            DB::commit();
            return response()->json($compra, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error during transaction:', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Compras $compra)
    {
        $validator = Validator::make($request->all(), Compras::rules());

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $compra->update($request->all());

        return response()->json($compra->only(['id_ticker', 'quantidade', 'valor_unitario', 'valor_total', 'created_at', 'updated_at']), 200);
    }

    public function destroy(Compras $compra)
    {
        $compra->delete();

        return response()->json(null, 204);
    }
}
