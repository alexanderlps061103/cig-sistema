<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')->unique()->constrained('personas')->cascadeOnDelete();
            $table->unsignedBigInteger('carrera_id')->nullable();
            $table->enum('modalidad_egreso', ['pasantia', 'acreditacion'])->nullable();
            $table->boolean('es_regular')->default(false);
            $table->timestamps();

            $table->foreign('carrera_id')->references('id')->on('carreras')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estudiantes');
    }
};
