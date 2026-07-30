<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('respuestas_encuesta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pregunta_id')->constrained('preguntas_encuesta')->cascadeOnDelete();
            $table->foreignId('persona_id')->constrained('personas')->cascadeOnDelete();
            $table->text('valor');
            $table->timestamps();
            $table->unique(['pregunta_id', 'persona_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('respuestas_encuesta');
    }
};
