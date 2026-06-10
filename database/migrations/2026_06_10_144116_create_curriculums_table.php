<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('curriculums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('especialidad'); // área de experiencia principal
            $table->text('experiencia')->nullable();
            $table->string('archivo_cv')->nullable(); // PDF del CV completo
            $table->text('notas_internas')->nullable(); // visible solo para admins
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('curriculums');
    }
};
