<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    public function getImageUrlAttribute(): string
    {
        $defaultImage = asset('images/default.jpg');
        $imagePath = ltrim((string) $this->image, '/');

        if ($imagePath === '') {
            return $defaultImage;
        }

        if (Str::startsWith($imagePath, ['http://', 'https://'])) {
            return $imagePath;
        }

        if (Str::startsWith($imagePath, 'images/')) {
            return file_exists(public_path($imagePath)) ? asset($imagePath) : $defaultImage;
        }

        if (Str::startsWith($imagePath, 'storage/')) {
            $storagePath = Str::after($imagePath, 'storage/');

            return Storage::disk('public')->exists($storagePath) ? asset($imagePath) : $defaultImage;
        }

        return Storage::disk('public')->exists($imagePath)
            ? asset('storage/' . $imagePath)
            : $defaultImage;
    }

    public function inscripcionCompetidores()
    {
        return $this->hasMany(InscripcionCompetidor::class);
    }

    public function organizaciones()
    {
        return $this->hasMany(Organizacion::class);
    }

    public function torneos()
    {
        return $this->hasMany(Torneo::class);
    }

    public function arbitros()
    {
        return $this->hasMany(Arbitro::class);
    }
}
