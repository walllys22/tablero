<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'status'];

    protected $casts = [
        'status' => 'integer',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
