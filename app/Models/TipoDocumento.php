<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model {
    protected $table = 'tipo_documentos';
    protected $primaryKey = 'id_tipo_documento';
    protected $fillable = ['titulo', 'descripcion', 'estado'];
}