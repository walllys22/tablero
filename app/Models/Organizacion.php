<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organizacion extends Model
{
    use HasFactory;

    protected $table = 'organizaciones';

    protected $fillable = [
        'nombre',
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
}
