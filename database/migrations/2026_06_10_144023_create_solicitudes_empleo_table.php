<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('solicitudes_empleo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')->unique()->constrained()->cascadeOnDelete(); // el aspirante
            $table->text('mensaje')->nullable();
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada'])->default('pendiente');
            $table->foreignId('revisado_por')->nullable()->constrained('personas'); // admin que decide
            $table->timestamp('fecha_revision')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('solicitudes_empleo');
    }
};
