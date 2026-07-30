<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feriados', function (Blueprint $table) {
            $table->id();
            $table->date('fecha')->index();
            $table->string('descripcion');
            $table->boolean('recurrente')->default(false); // si se repite cada año en la misma fecha
            $table->foreignId('creado_por')->nullable()->constrained('personas')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feriados');
    }
};
