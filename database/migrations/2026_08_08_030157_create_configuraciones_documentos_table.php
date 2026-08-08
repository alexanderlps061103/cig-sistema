<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('configuraciones_documentos', function (Blueprint $table) {
            $table->id();
            $table->string('clave')->unique(); // Ej: 'firma_rector', 'sello_centro', 'kit_induccion_link'
            $table->text('valor'); // Ruta del archivo o ID
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuraciones_documentos');
    }
};
