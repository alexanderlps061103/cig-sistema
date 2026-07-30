<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cartas_pasantia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('estudiantes')->cascadeOnDelete();
            $table->string('tipo');
            $table->string('institucion_destino')->nullable();
            $table->date('fecha_emision')->nullable();
            $table->string('archivo')->nullable();
            $table->enum('estado', ['activa', 'vencida', 'anulada'])->default('activa');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cartas_pasantia');
    }
};
