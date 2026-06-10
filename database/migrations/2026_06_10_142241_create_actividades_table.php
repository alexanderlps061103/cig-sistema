<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('actividades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trimestre_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tipo_actividad_id')->constrained('tipos_actividad');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->dateTime('fecha_inicio_inscripcion');
            $table->dateTime('fecha_fin_inscripcion');
            $table->date('fecha_actividad')->nullable(); // fecha principal, puede ser un rango en sesiones
            $table->integer('duracion_total_minutos')->nullable();
            $table->integer('cupos');
            $table->foreignId('espacio_id')->constrained('espacios');
            $table->string('estado')->default('planificada'); // planificada, activa, finalizada, cancelada
            $table->string('qr_asistencia')->nullable(); // código único
            $table->foreignId('creado_por')->constrained('personas');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('actividades');
    }
};
