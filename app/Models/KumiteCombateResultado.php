<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KumiteCombateResultado extends Model
{
    use HasFactory;

    protected $table = 'kumite_combate_resultados';

    protected $fillable = [
        'sorteo_llave_id',
        'numero_llave',
        'indice_combate',
        'competidor_rojo',
        'competidor_azul',
        'puntaje_rojo',
        'puntaje_azul',
        'faltas_rojo',
        'faltas_azul',
        'senshu',
        'senshu_rojo',
        'senshu_azul',
        'tecnicas_rojo',
        'tecnicas_azul',
        'ganador',
        'ganador_color',
        'realizado_at',
    ];

    protected $casts = [
        'faltas_rojo' => 'array',
        'faltas_azul' => 'array',
        'senshu_rojo' => 'boolean',
        'senshu_azul' => 'boolean',
        'tecnicas_rojo' => 'array',
        'tecnicas_azul' => 'array',
        'realizado_at' => 'datetime',
    ];

    public function sorteoLlave()
    {
        return $this->belongsTo(SorteoLlave::class);
    }
}
