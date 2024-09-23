<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoricoPrecoAtivosTable extends Migration
{
    public function up()
    {
        Schema::create('historico_preco_ativos', function (Blueprint $table) {
            $table->id();
            $table->string('ticker');
            $table->dateTime('data_ativo'); // Ajustado para dateTime para corresponder ao tipo DATETIME do MySQL
            $table->decimal('open', 10, 2);
            $table->decimal('low', 10, 2);
            $table->decimal('high', 10, 2);
            $table->decimal('close', 10, 2);
            $table->integer('volume');
            $table->timestamps();
            $table->unique(['ticker', 'data_ativo']); // Garantindo que a combinação de ticker e data seja única
        });
    }

    public function down()
    {
        Schema::dropIfExists('historico_preco_ativos');
    }
}
