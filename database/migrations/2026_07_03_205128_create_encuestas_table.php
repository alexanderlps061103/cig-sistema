<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('encuestas', function (Blueprint $table) {
            $table->id();
            
            // CORREGIDO: Declaración explícita para relacionar con 'id_actividad'
            $table->unsignedBigInteger('actividad_id');
            $table->foreign('actividad_id')->references('id_actividad')->on('actividades')->cascadeOnDelete();
            
            $table->text('descripcion')->nullable();
            $table->dateTime('fecha_limite')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encuestas');
    }
};
