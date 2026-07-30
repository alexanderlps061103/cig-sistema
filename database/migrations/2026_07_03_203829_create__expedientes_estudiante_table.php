<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expedientes_estudiante', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->unique()->constrained('estudiantes')->cascadeOnDelete();
            $table->foreignId('carrera_id')->nullable()->constrained('carreras')->nullOnDelete();
            $table->string('ruta_carnet')->nullable();
            $table->timestamp('carnet_verificado_at')->nullable();
            $table->string('ruta_notas_certificadas')->nullable();
            $table->timestamp('notas_verificadas_at')->nullable();
            $table->enum('tipo_solicitud', ['induccion', 'equivalencia'])->nullable();
            $table->enum('estado_solicitud', ['pendiente', 'aprobado', 'rechazado', 'en_pasantias', 'acreditado'])->default('pendiente');
            $table->unsignedBigInteger('tutor_asignado_id')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('tutor_asignado_id')->references('id')->on('docentes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expedientes_estudiante');
    }
};
