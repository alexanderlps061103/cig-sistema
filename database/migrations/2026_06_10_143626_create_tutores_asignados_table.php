<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tutores_asignados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('personas')->cascadeOnDelete();
            $table->foreignId('tutor_id')->constrained('personas')->cascadeOnDelete();
            $table->date('fecha_asignacion');
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->unique(['estudiante_id', 'activo']); // solo un tutor activo a la vez
        });
    }

    public function down()
    {
        Schema::dropIfExists('tutores_asignados');
    }
};
