<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Persona extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'personas';

    protected $fillable = [
        'ci',
        'first_name',
        'birth_date',
        'email',
        'phone',
        'address',
        'gender',
        'sangre',
        'image',
        'status',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'status' => 'integer',
    ];

    public function inscripcionCompetidores()
    {
        return $this->hasMany(InscripcionCompetidor::class);
    }

    public function organizaciones()
    {
        return $this->hasMany(Organizacion::class);
    }
}
