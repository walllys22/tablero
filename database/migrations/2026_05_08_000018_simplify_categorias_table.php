<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $categorias = DB::table('categorias')
            ->leftJoin('modalidades', 'modalidades.id', '=', 'categorias.modalidad_id')
            ->select('categorias.*', 'modalidades.nombre as modalidad_nombre')
            ->orderBy('categorias.id')
            ->get();

        foreach ($categorias as $categoria) {
            $updates = [];
            $nombre = $categoria->nombre;
            $modalidadNombre = mb_strtolower((string) $categoria->modalidad_nombre);
            $isKata = str_contains($modalidadNombre, 'kata');
            $hasPesoText = str_contains(mb_strtolower($nombre), 'kilo')
                || str_contains(mb_strtolower($nombre), 'mayor o igual')
                || str_contains(mb_strtolower($nombre), 'menor o igual');

            if ($isKata) {
                $updates['peso_hasta'] = null;
            } elseif (! $hasPesoText) {
                if (Schema::hasColumn('categorias', 'peso_desde') && $categoria->peso_desde !== null) {
                    $updates['peso_hasta'] = $categoria->peso_desde;
                    $updates['nombre'] = trim($nombre . ' mayor o igual a ' . rtrim(rtrim((string) $categoria->peso_desde, '0'), '.') . ' kilos');
                } elseif ($categoria->peso_hasta !== null) {
                    $updates['nombre'] = trim($nombre . ' menor o igual a ' . rtrim(rtrim((string) $categoria->peso_hasta, '0'), '.') . ' kilos');
                }
            } elseif (Schema::hasColumn('categorias', 'peso_desde') && $categoria->peso_desde !== null) {
                $updates['peso_hasta'] = $categoria->peso_desde;
            }

            if ($updates !== []) {
                DB::table('categorias')
                    ->where('id', $categoria->id)
                    ->update($updates);
            }
        }

        foreach (['peso_desde', 'grado', 'orden'] as $column) {
            if (! Schema::hasColumn('categorias', $column)) {
                continue;
            }

            Schema::table('categorias', function (Blueprint $table) use ($column) {
                $table->dropColumn($column);
            });
        }
    }

    public function down(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            if (! Schema::hasColumn('categorias', 'peso_desde')) {
                $table->decimal('peso_desde', 6, 2)->nullable()->after('edad_hasta');
            }

            if (! Schema::hasColumn('categorias', 'grado')) {
                $table->string('grado', 100)->nullable()->after('peso_hasta');
            }

            if (! Schema::hasColumn('categorias', 'orden')) {
                $table->unsignedInteger('orden')->default(0)->after('grado');
            }
        });
    }
};
