<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias';

    public $timestamps = false;

    protected $fillable = [
        'torneo_id',
        'modalidad_id',
        'nombre',
        'genero',
        'edad_desde',
        'edad_hasta',
        'peso_hasta',
    ];

    protected $casts = [
        'edad_desde' => 'integer',
        'edad_hasta' => 'integer',
        'peso_hasta' => 'decimal:2',
    ];

    public function torneo()
    {
        return $this->belongsTo(Torneo::class);
    }

    public function modalidad()
    {
        return $this->belongsTo(Modalidad::class);
    }
}
