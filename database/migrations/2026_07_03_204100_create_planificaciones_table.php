<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('planificaciones', function (Blueprint $table) {
            $table->id('id_planificacion');
            $table->string('titulo');
            $table->integer('anio'); // <-- NUEVO: Para filtrar fácilmente por el año actual (ej. 2026)
            $table->date('fecha_creacion');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('planificaciones');
    }
};