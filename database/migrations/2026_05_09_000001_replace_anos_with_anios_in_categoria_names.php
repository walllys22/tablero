<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('categorias')
            ->where('nombre', 'like', '%anos%')
            ->update([
                'nombre' => DB::raw("REPLACE(nombre, 'anos', 'años')"),
            ]);
    }

    public function down(): void
    {
        DB::table('categorias')
            ->where('nombre', 'like', '%años%')
            ->update([
                'nombre' => DB::raw("REPLACE(nombre, 'años', 'anos')"),
            ]);
    }
};
