<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model {
    protected $table = 'documentos';
    protected $primaryKey = 'id_documento';
    protected $fillable = ['nombre', 'descripcion', 'id_sello', 'id_firma'];

    public function sello() {
        return $this->belongsTo(Sello::class, 'id_sello');
    }

    public function firma() {
        return $this->belongsTo(Firma::class, 'id_firma');
    }
}
