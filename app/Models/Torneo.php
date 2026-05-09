<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Torneo extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'fecha_inicio',
        'fecha_fin',
        'nombre',
        'ciudad',
        'direccion',
        'sistema_competencia',
        'modalidad_puntaje',
        'organiza',
        'persona_id',
        'lugar',
        'logo',
        'status',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'status' => 'integer',
    ];

    public function modalidades()
    {
        return $this->hasMany(Modalidad::class);
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function categorias()
    {
        return $this->hasMany(Categoria::class);
    }

    public function inscripcionOrganizaciones()
    {
        return $this->hasMany(InscripcionOrganizacion::class);
    }

    public function inscripcionCompetidores()
    {
        return $this->hasMany(InscripcionCompetidor::class);
    }

    public function arbitros()
    {
        return $this->hasMany(Arbitro::class);
    }
}
