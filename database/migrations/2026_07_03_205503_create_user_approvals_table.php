<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')->constrained('personas')->cascadeOnDelete();
            $table->enum('tipo', ['docente', 'coordinador'])->default('docente');
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->foreignId('solicitado_por')->nullable()->constrained('personas')->nullOnDelete();
            $table->foreignId('aprobado_por')->nullable()->constrained('personas')->nullOnDelete();
            $table->text('motivo')->nullable();
            $table->timestamp('fecha_solicitud')->useCurrent();
            $table->timestamp('fecha_respuesta')->nullable();
            $table->timestamps();

            $table->index(['persona_id', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_approvals');
    }
};
