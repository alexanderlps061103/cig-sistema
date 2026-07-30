<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes_empleo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')->constrained('personas')->cascadeOnDelete();
            $table->text('mensaje')->nullable();
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada'])->default('pendiente');
            $table->foreignId('revisado_por')->nullable()->constrained('personas')->nullOnDelete();
            $table->timestamp('fecha_revision')->nullable();
            $table->timestamps();

            $table->index(['persona_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_empleo');
    }
};
