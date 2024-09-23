<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCadastrarAtivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cadastrar_ativos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id'); // Adiciona o campo para armazenar o ID do usuário
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // Define a chave estrangeira para a tabela 'users'
            $table->string('ticker')->unique();
            $table->string('nome', 50);
            $table->string('setor', 30);
            $table->string('industria', 50);
            $table->integer('quantidade');
            $table->float('preco_compra');
            $table->string('observacoes', 150)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cadastrar_ativos');
    }
}

