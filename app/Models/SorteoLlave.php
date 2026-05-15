<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SorteoLlave extends Model
{
    use HasFactory;

    protected $table = 'sorteo_llaves';

    protected $fillable = [
        'torneo_id',
        'modalidad_id',
        'categoria_id',
        'seed',
        'llaves',
        'area',
    ];

    protected $casts = [
        'seed' => 'integer',
        'llaves' => 'array',
        'area' => 'integer',
    ];

    public function torneo()
    {
        return $this->belongsTo(Torneo::class);
    }

    public function modalidad()
    {
        return $this->belongsTo(Modalidad::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function resultadosKumite()
    {
        return $this->hasMany(KumiteCombateResultado::class, 'sorteo_llave_id');
    }
}
