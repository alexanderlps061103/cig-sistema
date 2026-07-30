<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')->constrained('personas')->cascadeOnDelete();
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('verificado')->default(false);
            $table->boolean('activo')->default(true);
            $table->foreignId('aprobado_por')->nullable()->constrained('personas')->nullOnDelete();
            $table->timestamp('aprobado_at')->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['email', 'activo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
