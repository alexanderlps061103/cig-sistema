<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trazabilidad_kits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('estudiantes')->cascadeOnDelete();
            $table->foreignId('actividad_id')->references('id_actividad')->on('actividades');
            $table->string('email_enviado');
            $table->timestamp('fecha_envio')->useCurrent();
            $table->boolean('confirmado_abierto')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trazabilidad_kits');
    }
};
