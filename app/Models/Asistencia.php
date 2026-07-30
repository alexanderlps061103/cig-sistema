<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model {
    protected $table = 'asistencias';
    protected $primaryKey = 'id_asistencia';
    protected $fillable = ['estado', 'verificacion_qr'];
}
