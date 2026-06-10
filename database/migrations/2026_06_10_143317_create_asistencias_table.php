<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesion_id')->constrained()->cascadeOnDelete();
            $table->foreignId('persona_id')->constrained()->cascadeOnDelete(); // quien asistió (estudiante o docente como alumno)
            $table->dateTime('fecha_hora');
            $table->string('metodo'); // 'QR', 'manual'
            $table->foreignId('registrado_por')->nullable()->constrained('personas'); // docente/admin que tomó manual
            $table->timestamps();
            $table->unique(['sesion_id', 'persona_id']); // solo una asistencia por sesión/persona
        });
    }

    public function down()
    {
        Schema::dropIfExists('asistencias');
    }
};
