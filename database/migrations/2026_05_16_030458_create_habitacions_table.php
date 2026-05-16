<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('habitaciones', function (Blueprint $table) {
            $table->id();

            $table->string('numero')->unique();
            $table->string('tipo', 50);
            $table->unsignedInteger('capacidad');
            $table->decimal('precio_noche', 10, 2);
            $table->string('estado', 50)->default('Disponible');
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('habitaciones');
    }
};