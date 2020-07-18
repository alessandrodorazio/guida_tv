<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgrammiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('programmi', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->integer('tipologia')->default(1); //1=film, 2=serie tv
            $table->text('descrizione')->nullable();
            $table->string('immagine')->nullable();
            $table->string('link_approfondimento')->nullable();
            $table->integer('numero_stagione')->nullable();
            $table->integer('numero_puntata')->nullable();
            $table->bigInteger('genere_id')->unsigned()->nullable();
            $table->bigInteger('serie_id')->unsigned()->nullable();
            $table->timestamps();
            $table->foreign('genere_id')->references('id')->on('generi');
            $table->foreign('serie_id')->references('id')->on('serie');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('programmas');
    }
}
