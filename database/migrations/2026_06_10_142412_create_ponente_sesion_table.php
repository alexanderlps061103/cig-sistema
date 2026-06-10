<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('ponente_sesion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesion_id')->constrained()->cascadeOnDelete();
            $table->foreignId('persona_id')->constrained()->cascadeOnDelete();
            $table->string('rol')->nullable(); // titular, invitado, etc.
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ponente_sesion');
    }
};
