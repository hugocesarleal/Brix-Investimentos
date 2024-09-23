<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelatorioDiverso extends Model
{
    use HasFactory;

    // Definindo a tabela associada ao modelo
    protected $table = 'relatorio_diversos';

    // Definindo os campos que podem ser atribuídos em massa
    protected $fillable = [
        'user_id',  // Adicionado para associar relatórios a usuários
        'posicao_inicial',
        'posicao_final',
        'movimentacao',
        'rentabilidade_periodo',
        'volatilidade',
        'resultado_projetado'
    ];

    // Definindo as regras de validação para o relatório
    public static function rules()
    {
        return [
            'user_id' => 'required|exists:users,id', // Validação para o campo user_id
            'posicao_inicial' => 'required|numeric',
            'posicao_final' => 'required|numeric',
            'movimentacao' => 'required|numeric',
            'rentabilidade_periodo' => 'required|numeric',
            'volatilidade' => 'required|numeric',
            'resultado_projetado' => 'required|numeric'
        ];
    }
}

