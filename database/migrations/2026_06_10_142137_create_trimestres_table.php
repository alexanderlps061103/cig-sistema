<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('trimestres', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // ej: 2026-I
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->boolean('activo')->default(true);
            $table->foreignId('creado_por')->constrained('personas');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trimestres');
    }
};
