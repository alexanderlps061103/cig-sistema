<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sesiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actividad_id')->constrained()->cascadeOnDelete();
            $table->integer('numero_sesion');
            $table->string('tema');
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->string('lugar')->nullable(); // si difiere del espacio de la actividad
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sesiones');
    }
};
