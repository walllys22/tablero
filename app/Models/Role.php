<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'permissions', 'status'];

    protected $casts = [
        'permissions' => 'array',
        'status' => 'integer',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
