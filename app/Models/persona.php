<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class persona extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'personas';

    protected $fillable = [
        'ci',
        'first_name',
        'birth_date',
        'email',
        'country_code',
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
}
