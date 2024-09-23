<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComprasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_ticker');
            $table->integer('quantidade');
            $table->decimal('valor_unitario', 10, 2); // Usar decimal
            $table->decimal('valor_total', 10, 2);    // Usar decimal
            $table->timestamps();

            $table->foreign('id_ticker')->references('id')->on('cadastrar_ativos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('compras');
    }
}
