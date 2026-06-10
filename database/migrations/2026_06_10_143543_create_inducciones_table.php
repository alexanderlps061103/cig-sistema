<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inducciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_id')->constrained('solicitudes_induccion')->cascadeOnDelete();
            $table->foreignId('actividad_id')->constrained('actividades')->cascadeOnDelete(); // actividad tipo inducción
            $table->boolean('aprobada')->default(false); // cumple los 90 min
            $table->dateTime('fecha_aprobacion')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('inducciones');
    }
};
