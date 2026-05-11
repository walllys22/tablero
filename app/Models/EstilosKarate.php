<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstilosKarate extends Model
{
    use HasFactory;

    protected $table = 'estiloskarate';

    protected $fillable = [
        'nombre',
        'descripcion',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];
}
