<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('trimestres', function (Blueprint $table) {
            $table->id('id_trimestre');
            $table->string('nombre');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            
            // Relación con Planificación
            $table->unsignedBigInteger('id_planificacion');
            $table->foreign('id_planificacion')
                  ->references('id_planificacion')
                  ->on('planificaciones')
                  ->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('trimestres');
    }
};