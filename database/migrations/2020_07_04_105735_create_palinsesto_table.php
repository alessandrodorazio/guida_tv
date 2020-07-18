<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePalinsestoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('palinsesto', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('canale_id')->unsigned();
            $table->bigInteger('programma_id')->unsigned();
            $table->dateTime('ora_inizio')->nullable();
            $table->dateTime('ora_fine')->nullable();
            $table->timestamps();
            $table->foreign('canale_id')->references('id')->on('canali');
            $table->foreign('programma_id')->references('id')->on('programmi');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('in_onda');
    }
}
