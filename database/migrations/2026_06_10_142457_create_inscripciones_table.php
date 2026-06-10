<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inscripciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')->constrained()->cascadeOnDelete(); // estudiante
            $table->foreignId('actividad_id')->constrained()->cascadeOnDelete();
            $table->dateTime('fecha_inscripcion');
            $table->string('estado')->default('inscrito'); // inscrito, cancelado, asistió, etc.
            $table->unique(['persona_id', 'actividad_id']); // evita doble inscripción
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('inscripciones');
    }
};
