<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sesion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sesiones';
    protected $fillable = [
        'actividad_id',
        'numero_sesion',
        'tema','start_at',
        'end_at','lugar',
        'duracion_minutos',
        'qr_token',
        'qr_expires_at'
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'qr_expires_at' => 'datetime',
    ];

    public function actividad()
    {
        return $this->belongsTo(Actividad::class, 'actividad_id');
    }

    public function ponentes()
    {
        return $this->hasMany(PonenteSesion::class, 'sesion_id');
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'sesion_id');
    }

    public function certificados()
    {
        return $this->hasMany(Certificado::class, 'sesion_id');
    }
}
