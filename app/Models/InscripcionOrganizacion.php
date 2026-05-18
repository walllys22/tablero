<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InscripcionOrganizacion extends Model
{
    use HasFactory;

    protected $table = 'inscripcion_organizaciones';

    protected $fillable = [
        'torneo_id',
        'organizacion_id',
        'costo',
        'monto_pagado',
    ];

    protected $casts = [
        'costo' => 'decimal:2',
        'monto_pagado' => 'decimal:2',
    ];

    public function torneo()
    {
        return $this->belongsTo(Torneo::class);
    }

    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class);
    }

    public function competidores()
    {
        return $this->hasMany(InscripcionCompetidor::class);
    }
}
