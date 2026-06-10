<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('certificados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')->constrained()->cascadeOnDelete();
            $table->enum('tipo', ['estudiante_actividad', 'docente_sesion']);
            $table->foreignId('actividad_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sesion_id')->nullable()->constrained()->nullOnDelete();
            $table->string('codigo_verificacion')->unique();
            $table->date('fecha_emision');
            $table->string('archivo')->nullable(); // ruta PDF
            $table->string('firma_sello')->nullable(); // ruta imagen aplicada
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('certificados');
    }
};
