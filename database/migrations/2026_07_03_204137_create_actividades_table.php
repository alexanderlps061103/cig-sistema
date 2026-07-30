<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('actividades', function (Blueprint $table) {
            $table->id('id_actividad');
            
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->date('fecha');
            $table->date('fecha_inscripcion_inicio');
            $table->date('fecha_inscripcion_fin');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->string('estado')->default('pendiente');
            
            // Relaciones existentes en tu tabla de actividades:
            $table->unsignedBigInteger('id_trimestre');
            $table->unsignedBigInteger('id_salon');
            $table->unsignedBigInteger('id_modalidad');
            $table->unsignedBigInteger('id_tipo_actividad');
            $table->unsignedBigInteger('id_tipo_documento');
            
            // Relación circular hacia temas
            $table->unsignedBigInteger('id_tema')->nullable();
            $table->foreign('id_tema')->references('id_tema')->on('temas')->onDelete('restrict');

            // NUEVO: Relación opcional con el Organizador (Persona)
            $table->unsignedBigInteger('id_organizador')->nullable();
            $table->foreign('id_organizador')->references('id')->on('personas')->onDelete('set null');

            $table->timestamps();
        });

        // 2. AHORA QUE AMBAS EXISTEN: Alteramos la tabla 'temas' para inyectar su llave foránea
        Schema::table('temas', function (Blueprint $table) {
            $table->foreign('id_actividad')
                  ->references('id_actividad')
                  ->on('actividades')
                  ->onDelete('cascade');
        });
    }

    public function down(): void {
        // Al deshacer las migraciones, quitamos primero la relación foránea para no tener errores de restricción
        if (Schema::hasTable('temas')) {
            Schema::table('temas', function (Blueprint $table) {
                $table->dropForeign(['id_actividad']);
            });
        }
        Schema::dropIfExists('actividades');
    }
};