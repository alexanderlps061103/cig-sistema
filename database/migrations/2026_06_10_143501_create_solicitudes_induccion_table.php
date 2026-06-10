<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('solicitudes_induccion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('personas')->cascadeOnDelete();
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada'])->default('pendiente');
            $table->text('observacion')->nullable();
            $table->dateTime('fecha_solicitud');
            $table->dateTime('fecha_respuesta')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('solicitudes_induccion');
    }
};
