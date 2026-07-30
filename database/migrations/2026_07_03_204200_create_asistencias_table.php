<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id('id_asistencia');
            $table->string('estado'); // Enum o string para representar el estado (ej. "presente", "ausente")
            $table->boolean('verificacion_qr')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('asistencias');
    }
};