<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KataCombateResultado extends Model
{
    use HasFactory;

    protected $table = 'kata_combate_resultados';

    protected $fillable = [
        'sorteo_llave_id',
        'indice_combate',
        'competidor_rojo',
        'competidor_azul',
        'kata_numero_rojo',
        'kata_numero_azul',
        'kata_nombre_rojo',
        'kata_nombre_azul',
        'puntaje_rojo',
        'puntaje_azul',
        'kiken_rojo',
        'kiken_azul',
        'ganador',
        'ganador_color',
        'realizado_at',
    ];

    protected $casts = [
        'kiken_rojo' => 'boolean',
        'kiken_azul' => 'boolean',
        'realizado_at' => 'datetime',
    ];

    public function sorteoLlave()
    {
        return $this->belongsTo(SorteoLlave::class);
    }
}
