<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('instituciones_aliadas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique(); // Ej: Escuela Básica "Manuel Cedeño"
            $table->enum('tipo', ['escolar', 'universitario', 'comunitario', 'gubernamental', 'privado']);
            $table->string('direccion')->nullable();
            $table->string('persona_contacto_fijo')->nullable(); // Director o enlace permanente
            $table->string('telefono')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('instituciones_aliadas');
    }
};
