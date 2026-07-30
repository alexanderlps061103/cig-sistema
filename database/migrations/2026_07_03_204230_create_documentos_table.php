<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id('id_documento');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            
            // Relaciones opcionales con firmas y sellos
            $table->unsignedBigInteger('id_sello')->nullable();
            $table->unsignedBigInteger('id_firma')->nullable();
            
            $table->foreign('id_sello')->references('id_sello')->on('sellos')->onDelete('set null');
            $table->foreign('id_firma')->references('id_firma')->on('firmas')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('documentos');
    }
};