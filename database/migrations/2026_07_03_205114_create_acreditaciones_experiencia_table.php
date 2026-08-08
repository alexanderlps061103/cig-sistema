<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acreditaciones_experiencia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('estudiantes')->cascadeOnDelete();
            $table->text('motivos_solicitud');
            $table->string('archivo_formato_1'); // Solicitud digital
            $table->string('archivo_evidencias_digitales'); // Justificativos PDF
            $table->enum('estado_proceso', ['en_revision', 'aprobado', 'insuficiente'])->default('en_revision');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acreditaciones_experiencia');
    }
};
