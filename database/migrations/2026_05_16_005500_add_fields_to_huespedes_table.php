<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('huespedes', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nombres');
            $table->string('apellido_paterno');
            $table->string('apellido_materno')->nullable();
            $table->string('tipo_documento');
            $table->string('numero_documento', 50);
            $table->string('telefono', 30)->nullable();
            $table->string('correo_electronico')->nullable();
            $table->string('nacionalidad', 100)->nullable();
            $table->date('fecha_nacimiento')->nullable();

            $table->boolean('estado')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('huespedes', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'user_id',
                'nombres',
                'apellido_paterno',
                'apellido_materno',
                'tipo_documento',
                'numero_documento',
                'telefono',
                'correo_electronico',
                'nacionalidad',
                'fecha_nacimiento',
                'estado',
            ]);
        });
    }
};
