<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('temas', function (Blueprint $table) {
            $table->id('id_tema'); // Clave primaria
            $table->string('tema_sesion');
            $table->string('descripcion')->nullable();
            $table->integer('numero_de_sesion');
            $table->date('fecha');
            $table->time('horario_inicio');
            $table->time('horario_fin');
            $table->string('estado')->default('pendiente');
            
            // Relación con Docentes
            $table->unsignedBigInteger('id_docente')->nullable(); 
            $table->foreign('id_docente')->references('id')->on('docentes')->onDelete('set null');

            // Columna para relacionar con Actividades (Solo columna, sin restricción foreign de momento)
            $table->unsignedBigInteger('id_actividad')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('temas');
    }
};