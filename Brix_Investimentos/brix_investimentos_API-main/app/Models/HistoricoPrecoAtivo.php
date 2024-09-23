<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoricoPrecoAtivo extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticker',
        'data_ativo',
        'open',
        'low',
        'high',
        'close',
        'volume'
    ];

    protected $casts = [
        'open' => 'float',
        'low' => 'float',
        'high' => 'float',
        'close' => 'float',
        'volume' => 'integer',
        'data_ativo' => 'datetime', // Adicionado o cast para data_ativo
    ];

    public static function rules()
    {
        return [
            'ticker' => 'required',
            'data_ativo' => 'required|date|unique:historico_preco_ativos,data_ativo,NULL,id,ticker,:ticker',
            'open' => 'required|numeric|min:0',
            'low' => 'required|numeric|min:0',
            'high' => 'required|numeric|min:0',
            'close' => 'required|numeric|min:0',
            'volume' => 'required|integer|min:0',
        ];
    }
}
