<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizaciones', function (Blueprint $table) {
            $table->id();
            $table->string('rif')->unique();
            $table->string('nombre_razon_social');
            $table->string('direccion');
            $table->string('telefono_contacto')->nullable();
            $table->string('email_contacto')->nullable();
            $table->string('persona_contacto'); // Tutor Empresarial inicial
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizaciones');
    }
};
