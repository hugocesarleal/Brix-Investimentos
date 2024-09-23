<?php

namespace App\Http\Controllers;

use App\Models\RelatorioDiverso;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class RelatorioDiversoController extends Controller
{
    public function index(Request $request)
{
    try {
        $user_id = Auth::id(); // Obtém o ID do usuário autenticado

        // Verifique se o usuário está autenticado
        if (!$user_id) {
            return response()->json(['message' => 'Usuário não autenticado'], Response::HTTP_UNAUTHORIZED);
        }

        // Obtém os parâmetros de filtragem
        $inicio = $request->query('inicio');
        $periodo_inicio = $request->query('periodo_inicio');
        $periodo_fim = $request->query('periodo_fim');
        $conta = $request->query('conta');
        $estrategia = $request->query('estrategia');
        $grupo = $request->query('grupo');

        $query = RelatorioDiverso::where('user_id', $user_id);

        // Aplicar filtros conforme os parâmetros
        if ($inicio) {
            $query->whereDate('created_at', '>=', $inicio);
        }

        if ($periodo_inicio && $periodo_fim) {
            $query->whereBetween('created_at', [$periodo_inicio, $periodo_fim]);
        }

        if ($conta) {
            $query->where('conta', $conta);
        }

        if ($estrategia) {
            $query->where('estrategia', $estrategia);
        }

        if ($grupo) {
            $query->where('grupo', $grupo);
        }

        $relatorios = $query->get();

        // Preparar dados para gráficos
        $rentabilidadeData = [
            'labels' => $relatorios->pluck('created_at')->map(function ($date) {
                return $date->format('d/m/Y');
            }),
            'data' => $relatorios->pluck('rentabilidade_periodo')
        ];

        $rentabilidadeMensalData = [
            'labels' => $relatorios->groupBy(function ($item) {
                return $item->created_at->format('F Y');
            })->keys(),
            'data' => $relatorios->groupBy(function ($item) {
                return $item->created_at->format('F Y');
            })->map(function ($items) {
                return $items->sum('rentabilidade_periodo');
            })
        ];

        return response()->json([
            'relatorios' => $relatorios,
            'rentabilidade' => $rentabilidadeData,
            'rentabilidade_mensal' => $rentabilidadeMensalData,
        ], Response::HTTP_OK);
    } catch (\Exception $e) {
        Log::error('Erro ao listar os relatórios: ' . $e->getMessage());
        return response()->json(['message' => 'Erro ao listar os relatórios'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}


    public function show($id)
    {
        try {
            $relatorio = RelatorioDiverso::where('id', $id)
                ->where('user_id', Auth::id()) // Garante que o relatório pertence ao usuário
                ->first();

            if (!$relatorio) {
                return response()->json(['message' => 'Relatório não encontrado'], Response::HTTP_NOT_FOUND);
            }

            return response()->json($relatorio, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar o relatório: ' . $e->getMessage());
            return response()->json(['message' => 'Erro ao buscar o relatório'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), RelatorioDiverso::rules());

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação.',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $relatorio = RelatorioDiverso::where('id', $id)
                ->where('user_id', Auth::id()) // Garante que o relatório pertence ao usuário
                ->first();

            if (!$relatorio) {
                return response()->json(['message' => 'Relatório não encontrado'], Response::HTTP_NOT_FOUND);
            }

            $relatorio->update($request->all());

            return response()->json($relatorio, Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar o relatório: ' . $e->getMessage());
            return response()->json(['message' => 'Erro ao atualizar o relatório'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $relatorio = RelatorioDiverso::where('id', $id)
                ->where('user_id', Auth::id()) // Garante que o relatório pertence ao usuário
                ->first();

            if (!$relatorio) {
                return response()->json(['message' => 'Relatório não encontrado'], Response::HTTP_NOT_FOUND);
            }

            $relatorio->delete();

            return response()->json(['message' => 'Relatório deletado com sucesso!'], Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Erro ao deletar o relatório: ' . $e->getMessage());
            return response()->json(['message' => 'Erro ao deletar o relatório'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
