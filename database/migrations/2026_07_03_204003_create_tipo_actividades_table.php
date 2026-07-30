<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tipo_actividades', function (Blueprint $table) {
            $table->id('id_tipo_actividad');
            $table->string('nombre');
            $table->string('descripcion')->nullable();
            $table->time('duracion');
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('tipo_actividades');
    }
};