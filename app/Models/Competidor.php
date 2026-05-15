<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competidor extends Model
{
    use HasFactory;

    protected $table = 'competidores';

    protected $fillable = [
        'organizacion_id',
        'persona_id',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    public function organizacion()
    {
        return $this->belongsTo(Organizacion::class);
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }
}
