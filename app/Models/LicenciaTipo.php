<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicenciaTipo extends Model
{
    use HasFactory;

    protected $table = 'licencia_tipos';

    protected $fillable = [
        'nombre',
    ];

    public function arbitros()
    {
        return $this->hasMany(Arbitro::class);
    }
}
