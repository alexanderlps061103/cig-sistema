<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesiones', function (Blueprint $table) {
            $table->id('id_sesion'); // O puedes dejarlo como $table->id() si lo prefieres para sesiones
            
            // Relación corregida especificando que apunta a 'id_actividad'
            $table->foreignId('actividad_id')
                  ->constrained('actividades', 'id_actividad')
                  ->onDelete('cascade');
                  
            $table->integer('numero_sesion');
            $table->string('tema');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->string('lugar')->nullable();
            $table->integer('duracion_minutos');
            $table->string('qr_token')->nullable();
            $table->dateTime('qr_expires_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes(); // Requerido para el "use SoftDeletes" de tu modelo
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesiones');
    }
};