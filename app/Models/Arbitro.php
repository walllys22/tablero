<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Arbitro extends Model
{
    use HasFactory;

    protected $table = 'arbitros';

    protected $fillable = [
        'torneo_id',
        'persona_id',
        'cargo',
        'modalidad',
        'rango',
        'licencia_tipo_id',
    ];

    public function torneo()
    {
        return $this->belongsTo(Torneo::class);
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function licenciaTipo()
    {
        return $this->belongsTo(LicenciaTipo::class);
    }
}
