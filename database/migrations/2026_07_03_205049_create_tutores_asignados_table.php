<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tutores_asignados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('induccion_id')->constrained('inducciones')->cascadeOnDelete();
            $table->foreignId('docente_id')->constrained('docentes')->cascadeOnDelete();
            $table->foreignId('estudiante_id')->constrained('estudiantes')->cascadeOnDelete();
            $table->timestamp('fecha_asignacion')->useCurrent();
            $table->boolean('activo')->default(true);
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->unique(['induccion_id', 'docente_id'], 'uniq_induccion_docente');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutores_asignados');
    }
};
