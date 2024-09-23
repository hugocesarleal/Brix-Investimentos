<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CadastrarAtivo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'ticker',
        'setor',
        'industria',
        'quantidade',
        'preco_compra',
        'observacoes',
        'user_id', // Novo campo para armazenar o ID do usuário
    ];

    protected $casts = [
        'quantidade' => 'integer',
        'preco_compra' => 'float'
    ];

    public static function rules()
    {
        return [
            'nome' => 'required|string|max:50',
            'ticker' => 'required|string|max:10|unique:cadastrar_ativos,ticker',
            'setor' => 'required|string|max:30',
            'industria' => 'required|string|max:50',
            'quantidade' => 'required|integer',
            'preco_compra' => 'required|numeric',
            'observacoes' => 'nullable|string|max:150',
        ];
    }

    public function historicoPrecoAtivos()
    {
        return $this->hasMany(HistoricoPrecoAtivo::class, 'id_ticker', 'id');
    }

    public function compras()
    {
        return $this->hasMany(Compras::class, 'id_ticker', 'id');
    }

    public function vendas()
    {
        return $this->hasMany(Vendas::class, 'id_ticker', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
