<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ponente_sesion', function (Blueprint $table) {
            $table->id();
            
            // CORREGIDO: Ahora apunta a la tabla 'temas' usando su PK 'id_tema'
            $table->unsignedBigInteger('tema_id');
            $table->foreign('tema_id')->references('id_tema')->on('temas')->cascadeOnDelete();

            $table->foreignId('docente_id')->nullable()->constrained('docentes')->nullOnDelete();
            $table->foreignId('persona_id')->nullable()->constrained('personas')->nullOnDelete();
            $table->string('rol')->nullable();
            $table->timestamps();

            // CORREGIDO: Actualizamos los índices con 'tema_id'
            $table->index(['tema_id', 'docente_id']);
            $table->index(['tema_id', 'persona_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ponente_sesion');
    }
};
