<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Certificado extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'certificados';
    protected $fillable = [
        'persona_id','tipo',
        'actividad_id',
        'sesion_id',
        'codigo_verificacion',
        'fecha_emision',
        'archivo',
        'firma_sello',
        'qr_data',
        'aprobado_por'
    ];

    protected $casts = ['fecha_emision' => 'date'];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    public function actividad()
    {
        return $this->belongsTo(Actividad::class, 'actividad_id');
    }

    public function sesion()
    {
        return $this->belongsTo(Sesion::class, 'sesion_id');
    }

    public function aprobadoPor()
    {
        return $this->belongsTo(Persona::class, 'aprobado_por');
    }
}
