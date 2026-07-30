<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salon extends Model {
    protected $table = 'salones';
    protected $primaryKey = 'id_salon';
    protected $fillable = ['nombre', 'capacidad', 'estado'];
}