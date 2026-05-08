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
}
