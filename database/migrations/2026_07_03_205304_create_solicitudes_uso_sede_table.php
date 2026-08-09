<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('solicitudes_uso_sede', function (Blueprint $table) {
            $table->id();
            // Quién viene (Institución)
            $table->foreignId('institucion_id')->constrained('instituciones_aliadas')->cascadeOnDelete();

            // Quién solicita (Persona Natural)
            $table->foreignId('solicitante_persona_id')->constrained('personas');

            // Quién dicta la actividad (Ponente - Persona Natural)
            $table->foreignId('ponente_persona_id')->constrained('personas');

            // Lugar y Tiempo
            $table->unsignedBigInteger('id_salon');
            $table->foreign('id_salon')->references('id_salon')->on('salones');
            $table->date('fecha_actividad');
            $table->time('hora_inicio');
            $table->time('hora_fin');

            // Métricas de impacto
            $table->integer('poblacion_atendida'); // Cantidad de personas (ej: 30 niños)
            $table->text('descripcion_actividad');

            // Colaboraciones (El "pago" en insumos)
            $table->text('detalle_colaboracion')->nullable(); // Ej: "1 Bombona de gas, 2 cloro"

            // Control Administrativo
            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada', 'ejecutada'])->default('pendiente');
            $table->foreignId('verificado_por')->nullable()->constrained('personas'); // Coordinador General
            $table->timestamp('fecha_verificacion')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('solicitudes_uso_sede');
    }
};
