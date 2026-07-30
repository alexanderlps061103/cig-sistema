<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CartaPasantia extends Model
{
    use HasFactory;

    protected $table = 'cartas_pasantia';
    protected $fillable = [
        'estudiante_id',
        'tipo',
        'institucion_destino',
        'fecha_emision',
        'archivo',
        'estado'
    ];

    protected $casts = ['fecha_emision' => 'date'];

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }
}
