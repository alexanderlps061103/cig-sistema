<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sello extends Model {
    protected $table = 'sellos';
    protected $primaryKey = 'id_sello';
    protected $fillable = ['nombre', 'imagen', 'estado'];
}