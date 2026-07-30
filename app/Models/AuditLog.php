<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    protected $fillable = [
        'usuario_id',
        'persona_id',
        'accion',
        'modelo',
        'modelo_id',
        'cambios',
        'ip',
        'user_agent'
    ];

    protected $casts = [
        'cambios' => 'array'
    ];

     public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }
}
