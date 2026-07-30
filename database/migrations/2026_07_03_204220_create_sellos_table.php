<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sellos', function (Blueprint $table) {
            $table->id('id_sello');
            $table->string('nombre');
            $table->string('imagen')->nullable(); // Ruta de la imagen
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('sellos');
    }
};