<?php

namespace App\Models;

use App\Models\EstilosKarate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organizacion extends Model
{
    use HasFactory;

    protected $table = 'organizaciones';

    protected $fillable = [
        'nombre',
        'estilo_id',
        'persona_id',
        'logo',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    public function inscripciones()
    {
        return $this->hasMany(InscripcionOrganizacion::class);
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function estilo()
    {
        return $this->belongsTo(EstilosKarate::class, 'estilo_id');
    }
}
