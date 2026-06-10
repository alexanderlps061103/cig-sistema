<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('carrera')->nullable(); // Ej: 'Ciencias y Cultura de la Alimentación' o null si no aplica
            $table->string('carnet_estudiantil')->nullable(); // ruta imagen
            $table->string('carta_aprobacion_induccion')->nullable(); // ruta PDF (opcional, histórico)
            $table->boolean('es_regular')->default(false); // true si es de la carrera UNEY
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('estudiantes');
    }
};
