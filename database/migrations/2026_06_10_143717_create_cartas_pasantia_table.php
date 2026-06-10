<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cartas_pasantia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('personas')->cascadeOnDelete();
            $table->string('tipo'); // solicitud, acreditacion, otra
            $table->string('institucion_destino')->nullable();
            $table->date('fecha_emision');
            $table->string('archivo')->nullable(); // PDF generado
            $table->enum('estado', ['activa', 'vencida', 'anulada'])->default('activa');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cartas_pasantia');
    }
};
