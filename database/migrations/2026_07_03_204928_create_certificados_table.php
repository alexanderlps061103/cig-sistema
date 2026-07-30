<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')->constrained('personas')->cascadeOnDelete();
            $table->enum('tipo', ['estudiante_actividad', 'docente_sesion'])->nullable();
            
            // CORREGIDO: Relación explícita apuntando a 'id_actividad' de la tabla 'actividades'
            $table->unsignedBigInteger('actividad_id')->nullable();
            $table->foreign('actividad_id')->references('id_actividad')->on('actividades')->onDelete('set null');
            
            // CORREGIDO: Relación explícita apuntando a 'id_tema' de la nueva tabla 'temas'
            $table->unsignedBigInteger('tema_id')->nullable();
            $table->foreign('tema_id')->references('id_tema')->on('temas')->onDelete('set null');
            
            $table->uuid('codigo_verificacion')->unique();
            $table->date('fecha_emision')->nullable();
            $table->string('archivo')->nullable();
            $table->string('firma_sello')->nullable(); // ruta de imagen o referencia
            $table->string('qr_data')->nullable(); // datos embebidos en QR (json o url)
            $table->foreignId('aprobado_por')->nullable()->constrained('personas')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            // CORREGIDO: Actualizamos el índice de búsqueda con 'tema_id'
            $table->index(['persona_id', 'actividad_id', 'tema_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificados');
    }
};
