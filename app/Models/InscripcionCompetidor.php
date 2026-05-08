<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InscripcionCompetidor extends Model
{
    use HasFactory;

    protected $table = 'inscripcion_competidores';

    protected $fillable = [
        'torneo_id',
        'inscripcion_organizacion_id',
        'persona_id',
    ];

    public function torneo()
    {
        return $this->belongsTo(Torneo::class);
    }

    public function inscripcionOrganizacion()
    {
        return $this->belongsTo(InscripcionOrganizacion::class);
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function modalidades()
    {
        return $this->hasMany(InscripcionCompetidorModalidad::class);
    }

    public function getTotalAttribute()
    {
        return $this->modalidades->sum('costo');
    }
}
