<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KumitePodio extends Model
{
    use HasFactory;

    protected $table = 'kumite_podios';

    protected $fillable = [
        'sorteo_llave_id',
        'oro',
        'plata',
        'bronce_1',
        'bronce_2',
        'generado_at',
    ];

    protected $casts = [
        'generado_at' => 'datetime',
    ];

    public function sorteoLlave()
    {
        return $this->belongsTo(SorteoLlave::class);
    }
}
