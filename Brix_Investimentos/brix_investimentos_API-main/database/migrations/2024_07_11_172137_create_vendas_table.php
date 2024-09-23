<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_ticker');
            $table->unsignedBigInteger('id_compra')->nullable(); // Permitir valores nulos
            $table->integer('quantidade');
            $table->decimal('valor_unitario', 10, 2); // Usar decimal
            $table->decimal('valor_total', 10, 2);    // Usar decimal
            $table->timestamps();

            $table->foreign('id_ticker')->references('id')->on('cadastrar_ativos')->onDelete('cascade');
            $table->foreign('id_compra')->references('id')->on('compras')->onDelete('set null'); // Permitir valores nulos
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendas');
    }
}
