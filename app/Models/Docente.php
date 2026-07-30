<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Docente extends Model
{
    use HasFactory;

    protected $table = 'docentes';
    protected $fillable = ['persona_id','profesion_id'];

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    public function profesion()
    {
        return $this->belongsTo(Profesion::class, 'profesion_id');
    }

    public function ponencias()
    {
        return $this->hasMany(PonenteSesion::class, 'docente_id');
    }
}
