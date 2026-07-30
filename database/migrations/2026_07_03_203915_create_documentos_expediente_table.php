<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos_expediente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_id')->constrained('expedientes_estudiante')->cascadeOnDelete();
            $table->enum('tipo_documento', ['carnet', 'notas', 'certificado', 'otro']);
            $table->string('ruta_archivo');
            $table->string('mime')->nullable();
            $table->unsignedBigInteger('subido_por')->nullable();
            $table->timestamp('verificado_at')->nullable();
            $table->timestamps();

            $table->foreign('subido_por')->references('id')->on('personas')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos_expediente');
    }
};
