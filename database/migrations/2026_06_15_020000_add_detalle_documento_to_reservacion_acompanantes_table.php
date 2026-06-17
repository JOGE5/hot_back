<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservacion_acompanantes', function (Blueprint $table) {
            $table->string('nombre_completo')->nullable()->after('reservacion_id');
            $table->string('tipo_documento', 30)->nullable()->after('nombre_completo');
            $table->string('numero_documento', 50)->nullable()->after('tipo_documento');
            $table->string('nacionalidad', 100)->nullable()->after('numero_documento');
            $table->date('fecha_nacimiento')->nullable()->after('nacionalidad');
        });
    }

    public function down(): void
    {
        Schema::table('reservacion_acompanantes', function (Blueprint $table) {
            $table->dropColumn([
                'nombre_completo',
                'tipo_documento',
                'numero_documento',
                'nacionalidad',
                'fecha_nacimiento',
            ]);
        });
    }
};
