<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modalidad extends Model
{
    use HasFactory;

    protected $table = 'modalidades';

    protected $fillable = [
        'torneo_id',
        'nombre',
        'genero',
    ];

    public function torneo()
    {
        return $this->belongsTo(Torneo::class);
    }
}
