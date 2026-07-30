<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Firma extends Model {
    protected $table = 'firmas';
    protected $primaryKey = 'id_firma';
    protected $fillable = ['nombre', 'imagen', 'estado'];
}