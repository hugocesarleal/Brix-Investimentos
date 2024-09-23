<?php

namespace App\Http\Controllers;

use App\Models\CadastrarAtivo;
use App\Models\HistoricoPrecoAtivo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class CadastrarAtivoController extends Controller
{
    protected $cadastrarAtivo;

    public function __construct(CadastrarAtivo $cadastrarAtivo)
    {
        $this->cadastrarAtivo = $cadastrarAtivo;
    }

    public function index()
    {
        try {
            $ativos = CadastrarAtivo::where('user_id', Auth::id())->get();
            return response()->json($ativos, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao listar os ativos: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate(CadastrarAtivo::rules());

            $validatedData['user_id'] = Auth::id();

            $ativo = CadastrarAtivo::create($validatedData);

            // Criar histórico de preços inicial
            $historicoData = [
                'ticker' => $ativo->ticker,
                'data_ativo' => now(),
                'open' => $request->input('preco_compra'),
                'low' => $request->input('preco_compra'),
                'high' => $request->input('preco_compra'),
                'close' => $request->input('preco_compra'),
                'volume' => $request->input('quantidade'),
            ];

            HistoricoPrecoAtivo::create($historicoData);

            return response()->json($ativo, Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação.',
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (QueryException $e) {
            return response()->json(['message' => 'Erro ao cadastrar o ativo: ' . $e->getMessage()], Response::HTTP_CONFLICT);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao cadastrar o ativo: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $ativo = $this->cadastrarAtivo->find($id);

            if (!$ativo) {
                return response()->json(['message' => 'Ativo não encontrado'], Response::HTTP_NOT_FOUND);
            }

            return response()->json($ativo, Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao buscar o ativo: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $ativo = $this->cadastrarAtivo->find($id);

            if (!$ativo) {
                return response()->json(['message' => 'Ativo não encontrado'], Response::HTTP_NOT_FOUND);
            }

            // Ajustar as regras de validação para permitir a atualização do ticker atual
            $rules = CadastrarAtivo::rules();
            $rules['ticker'] = 'required|string|max:10|unique:cadastrar_ativos,ticker,' . $ativo->id;

            $validatedData = $request->validate($rules);

            $ativo->update($validatedData);

            return response()->json($ativo, Response::HTTP_OK);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação.',
                'errors' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (QueryException $e) {
            return response()->json(['message' => 'Erro ao atualizar o ativo: ' . $e->getMessage()], Response::HTTP_CONFLICT);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao atualizar o ativo: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $ativo = CadastrarAtivo::find($id);

            if (!$ativo) {
                return response()->json(['message' => 'Ativo não encontrado'], Response::HTTP_NOT_FOUND);
            }

            HistoricoPrecoAtivo::where('ticker', $ativo->ticker)->delete();

            $ativo->delete();

            return response()->json(['message' => 'Ativo deletado com sucesso!'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao deletar o ativo: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
