<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('inscripciones', function (Blueprint $table) {
            $table->id('id_inscripcion');
            $table->date('fecha_registro');
            
            // Relaciones del negocio
            $table->unsignedBigInteger('id_publico_general')->nullable(); // Si aplica
            $table->unsignedBigInteger('id_estudiante')->nullable();       // Si aplica
            $table->unsignedBigInteger('id_asistencia')->nullable();
            $table->unsignedBigInteger('id_actividad');
            $table->unsignedBigInteger('id_documento')->nullable();

            // NUEVO: Columna estado de la inscripción
            $table->string('estado')->default('pendiente');

            $table->foreign('id_asistencia')->references('id_asistencia')->on('asistencias')->onDelete('set null');
            $table->foreign('id_actividad')->references('id_actividad')->on('actividades')->onDelete('restrict');
            $table->foreign('id_documento')->references('id_documento')->on('documentos')->onDelete('set null');
            
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('inscripciones');
    }
};