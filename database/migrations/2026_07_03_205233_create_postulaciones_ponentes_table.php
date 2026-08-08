<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('postulaciones_ponentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')->constrained('personas')->cascadeOnDelete();
            $table->string('area_especialidad');
            $table->text('resumen_propuesta')->nullable();
            $table->string('archivo_curriculum')->nullable();
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada'])->default('pendiente');
            $table->foreignId('evaluado_por')->nullable()->constrained('personas')->nullOnDelete(); // Coordinador General
            $table->text('observaciones_coordinador')->nullable();
            $table->timestamps();

            $table->index(['persona_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postulaciones_ponentes');
    }
};
