<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('personas', function (Blueprint $table) {
            $table->id();
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('cedula')->unique();
            $table->string('telefono')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('sexo', ['M', 'F', 'Otro'])->nullable();
            $table->string('foto')->nullable();
            $table->string('cedula_imagen')->nullable();
            $table->boolean('verificado')->default(false);
            $table->boolean('activo')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('personas');
    }
};
