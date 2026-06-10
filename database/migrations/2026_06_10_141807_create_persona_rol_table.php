<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('persona_rol', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rol_id')->constrained()->cascadeOnDelete();
            $table->unique(['persona_id', 'rol_id']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('persona_rol');
    }
};
