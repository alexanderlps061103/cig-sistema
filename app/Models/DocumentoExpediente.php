<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoExpediente extends Model
{
    use HasFactory;

    protected $table = 'documentos_expediente';

    protected $fillable = [
        'expediente_id',
        'tipo_documento',
        'ruta_archivo',
        'mime',
        'subido_por',
        'verificado_at',
    ];

    protected $casts = [
        'verificado_at' => 'datetime',
    ];

    public function expediente()
    {
        return $this->belongsTo(ExpedienteEstudiante::class, 'expediente_id');
    }

    public function subidoPor()
    {
        return $this->belongsTo(Usuario::class, 'subido_por'); // asume modelo Usuario
    }
}
