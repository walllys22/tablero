<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InscripcionCompetidorModalidad extends Model
{
    use HasFactory;

    protected $table = 'inscripcion_competidor_modalidades';

    protected $fillable = [
        'inscripcion_competidor_id',
        'modalidad_id',
        'costo',
    ];

    protected $casts = [
        'costo' => 'decimal:2',
    ];

    public function inscripcionCompetidor()
    {
        return $this->belongsTo(InscripcionCompetidor::class);
    }

    public function modalidad()
    {
        return $this->belongsTo(Modalidad::class);
    }
}
