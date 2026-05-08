<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias';

    protected $fillable = [
        'torneo_id',
        'nombre',
        'genero',
        'edad_desde',
        'edad_hasta',
        'peso_desde',
        'peso_hasta',
        'grado',
        'orden',
    ];

    protected $casts = [
        'edad_desde' => 'integer',
        'edad_hasta' => 'integer',
        'peso_desde' => 'decimal:2',
        'peso_hasta' => 'decimal:2',
        'orden' => 'integer',
    ];

    public function torneo()
    {
        return $this->belongsTo(Torneo::class);
    }

    public function modalidades()
    {
        return $this->hasMany(Modalidad::class);
    }

    public function getDescripcionAttribute(): string
    {
        $parts = array_filter([
            $this->genero,
            $this->edad_desde || $this->edad_hasta ? trim(($this->edad_desde ?: '') . ' - ' . ($this->edad_hasta ?: '')) . ' anos' : null,
            $this->peso_desde || $this->peso_hasta ? trim(($this->peso_desde ?: '') . ' - ' . ($this->peso_hasta ?: '')) . ' kg' : null,
            $this->grado,
        ]);

        return implode(' | ', $parts);
    }
}
