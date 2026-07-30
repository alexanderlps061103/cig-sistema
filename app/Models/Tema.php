<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tema extends Model {
    protected $table = 'temas';
    protected $primaryKey = 'id_tema';
    
    protected $fillable = [
        'tema_sesion', 
        'descripcion', 
        'numero_de_sesion', 
        'fecha', 
        'horario_inicio', 
        'horario_fin', 
        'estado', 
        'id_docente', 
        'id_actividad'
    ];

    // Atributos dinámicos que se anexan automáticamente al convertir el modelo a un objeto JSON
    protected $appends = [
        'numero_sesion',
        'tema',
        'hora_inicio',
        'hora_fin',
        'start_at',
        'end_at',
        'status',
        'docente',
        'nombre_docente'
    ];

    // Relación con Docentes
    public function docente() {
        return $this->belongsTo(Docente::class, 'id_docente', 'id');
    }

    // Relación con Actividades
    public function actividad() {
        return $this->belongsTo(Actividad::class, 'id_actividad', 'id_actividad');
    }

    // ==========================================
    // ACCESSORS DE COMPATIBILIDAD CON EL JAVASCRIPT
    // ==========================================
    
    public function getNumeroSesionAttribute() {
        return $this->numero_de_sesion;
    }

    public function getTemaAttribute() {
        return $this->tema_sesion;
    }

    public function getHoraInicioAttribute() {
        return $this->horario_inicio;
    }

    public function getHoraFinAttribute() {
        return $this->horario_fin;
    }

    public function getStartAtAttribute() {
        $fechaStr = ($this->fecha instanceof \Carbon\Carbon) ? $this->fecha->format('Y-m-d') : $this->fecha;
        return ($fechaStr && $this->horario_inicio) ? "{$fechaStr} {$this->horario_inicio}" : $this->horario_inicio;
    }

    public function getEndAtAttribute() {
        $fechaStr = ($this->fecha instanceof \Carbon\Carbon) ? $this->fecha->format('Y-m-d') : $this->fecha;
        return ($fechaStr && $this->horario_fin) ? "{$fechaStr} {$this->horario_fin}" : $this->horario_fin;
    }

    public function getStatusAttribute() {
        return $this->estado;
    }

    public function getDocenteAttribute() {
        // Obtenemos la relación de forma segura evitando la recursividad sobre la propiedad dinámica
        $docente = $this->relationLoaded('docente') 
            ? $this->getRelation('docente') 
            : $this->docente()->first();

        if ($docente && $docente->persona) {
            return $docente->persona->nombres . ' ' . $docente->persona->apellidos;
        }

        return 'Sin Docente Asignado';
    }

    public function getNombreDocenteAttribute() {
        return $this->getDocenteAttribute();
    }

    /**
     * Sobreescribimos toArray para garantizar compatibilidad con múltiples formatos
     * (camelCase, snake_case, inglés y español) en respuestas JSON y serializaciones.
     */
    public function toArray() {
        $array = parent::toArray();
        
        $fechaStr = ($this->fecha instanceof \Carbon\Carbon) ? $this->fecha->format('Y-m-d') : $this->fecha;
        $startAt = ($fechaStr && $this->horario_inicio) ? "{$fechaStr} {$this->horario_inicio}" : $this->horario_inicio;
        $endAt = ($fechaStr && $this->horario_fin) ? "{$fechaStr} {$this->horario_fin}" : $this->horario_fin;
        
        $nombreDocente = $this->getDocenteAttribute();

        // Identificadores y Títulos
        $array['idTema'] = $this->id_tema;
        $array['tema_sesion'] = $this->tema_sesion;
        $array['temaSesion'] = $this->tema_sesion;
        $array['tema'] = $this->tema_sesion;
        $array['nombre'] = $this->tema_sesion;
        $array['title'] = $this->tema_sesion;
        
        // Descripciones
        $array['descripcion'] = $this->descripcion;
        $array['description'] = $this->descripcion;
        
        // Números de sesión
        $array['numero_de_sesion'] = $this->numero_de_sesion;
        $array['numeroDeSesion'] = $this->numero_de_sesion;
        $array['numero_sesion'] = $this->numero_de_sesion;
        $array['numeroSesion'] = $this->numero_de_sesion;
        $array['numero'] = $this->numero_de_sesion;
        
        // Horas e inicios (Compatibilidad con formateadores Date de JS)
        $array['horario_inicio'] = $this->horario_inicio;
        $array['horarioInicio'] = $this->horario_inicio;
        $array['horario_fin'] = $this->horario_fin;
        $array['horarioFin'] = $this->horario_fin;
        
        $array['hora_inicio'] = $this->horario_inicio;
        $array['horaInicio'] = $this->horario_inicio;
        $array['hora_fin'] = $this->horario_fin;
        $array['horaFin'] = $this->horario_fin;
        
        $array['start_at'] = $startAt;
        $array['startAt'] = $startAt;
        $array['end_at'] = $endAt;
        $array['endAt'] = $endAt;
        
        // Estado
        $array['estado'] = $this->estado;
        $array['status'] = $this->estado;
        
        // Docente asignado
        $array['docente'] = $nombreDocente;
        $array['nombre_docente'] = $nombreDocente;
        $array['nombreDocente'] = $nombreDocente;
        $array['teacher'] = $nombreDocente;
        
        return $array;
    }
}