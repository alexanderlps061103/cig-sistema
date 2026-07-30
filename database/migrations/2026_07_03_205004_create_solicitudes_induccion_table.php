<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes_induccion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('estudiantes')->cascadeOnDelete();
            $table->foreignId('expediente_id')->nullable()->constrained('expedientes_estudiante')->nullOnDelete();
            $table->enum('tipo', ['induccion', 'equivalencia'])->default('induccion');
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada'])->default('pendiente');
            $table->text('observacion')->nullable();
            $table->timestamp('fecha_solicitud')->useCurrent();
            $table->timestamp('fecha_respuesta')->nullable();
            $table->unsignedTinyInteger('cartas_activas')->default(0);
            $table->timestamps();

            $table->index(['estudiante_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_induccion');
    }
};
