<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitas_centro', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_completo');
            $table->string('cedula')->nullable();
            $table->string('motivo_visita');
            $table->date('fecha_visita');
            
            // CORREGIDO: Apuntamos de manera explícita a la tabla 'salones' y a su PK 'id_salon'
            $table->unsignedBigInteger('id_salon')->nullable();
            $table->foreign('id_salon')->references('id_salon')->on('salones')->onDelete('set null');
            
            $table->foreignId('persona_id')->nullable()->constrained('personas')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitas_centro');
    }
};
