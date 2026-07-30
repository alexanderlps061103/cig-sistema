<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inducciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_id')->constrained('solicitudes_induccion')->cascadeOnDelete();
            
            // CORREGIDO: Declaración explícita para relacionar con 'id_actividad'
            $table->unsignedBigInteger('actividad_id');
            $table->foreign('actividad_id')->references('id_actividad')->on('actividades')->cascadeOnDelete();
            
            $table->foreignId('estudiante_id')->constrained('estudiantes')->cascadeOnDelete();
            $table->foreignId('tutor_id')->nullable()->constrained('docentes')->nullOnDelete();
            $table->boolean('aprobada')->default(false);
            $table->unsignedSmallInteger('horas_completadas')->nullable();
            $table->unsignedSmallInteger('duracion_minutos')->nullable();
            $table->timestamp('fecha_aprobacion')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index(['actividad_id', 'estudiante_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inducciones');
    }
};
