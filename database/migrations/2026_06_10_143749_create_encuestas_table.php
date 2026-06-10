<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('encuestas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actividad_id')->constrained()->cascadeOnDelete();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->dateTime('fecha_limite')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('encuestas');
    }
};
