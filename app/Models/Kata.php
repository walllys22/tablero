<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kata extends Model
{
    use HasFactory;

    protected $table = 'katas';

    public $timestamps = false;

    protected $fillable = [
        'numero',
        'nombre',
        'sistema_id',
        'estado',
    ];

    protected $casts = [
        'numero' => 'integer',
    ];

    public function sistema()
    {
        return $this->belongsTo(SistemaCompetencia::class, 'sistema_id');
    }
}
