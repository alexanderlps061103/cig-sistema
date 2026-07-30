<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('persona_rol', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')->constrained('personas')->cascadeOnDelete();
            $table->foreignId('rol_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('asignado_por')->nullable()->constrained('personas')->nullOnDelete();
            $table->boolean('activo')->default(true);
            $table->timestamp('asignado_en')->nullable();
            $table->unique(['persona_id', 'rol_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('persona_rol');
    }
};
