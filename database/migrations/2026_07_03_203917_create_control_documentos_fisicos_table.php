<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('control_documentos_fisicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('estudiantes')->cascadeOnDelete();

            // Checklist según requerimientos
            $table->boolean('carta_aceptacion_empresa')->default(false);
            $table->boolean('evaluacion_tutor_empresarial')->default(false);
            $table->boolean('evaluacion_tutor_institucional')->default(false);
            $table->boolean('carta_culminacion_empresa')->default(false);
            $table->boolean('constancia_trabajo_acreditacion')->default(false); // Solo para modalidad acreditación

            $table->foreignId('verificado_por')->nullable()->constrained('personas')->nullOnDelete(); // Delegada
            $table->timestamp('fecha_verificacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('control_documentos_fisicos');
    }
};
