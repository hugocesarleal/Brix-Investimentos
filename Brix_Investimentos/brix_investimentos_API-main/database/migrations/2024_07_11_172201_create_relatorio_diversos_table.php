<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRelatorioDiversosTable extends Migration
{
    public function up()
    {
        Schema::create('relatorio_diversos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Adicionado para associar relatórios a usuários
            $table->double('posicao_inicial');
            $table->double('posicao_final');
            $table->double('movimentacao');
            $table->double('rentabilidade_periodo');
            $table->double('volatilidade');
            $table->double('resultado_projetado');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('relatorio_diversos');
    }
}
