<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('preguntas_encuesta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('encuesta_id')->constrained()->cascadeOnDelete();
            $table->text('texto');
            $table->string('tipo'); // 'seleccion', 'texto', 'numerico', etc.
            $table->json('opciones')->nullable(); // para selección múltiple / única
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('preguntas_encuesta');
    }
};
