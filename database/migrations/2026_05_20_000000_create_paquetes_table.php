<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paquetes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('imagen')->nullable();
            $table->string('tipo_habitacion')->nullable();
            $table->string('tour_incluido')->nullable();
            $table->boolean('incluye_desayuno')->default(false);
            $table->boolean('incluye_almuerzo')->default(false);
            $table->boolean('incluye_cena')->default(false);
            $table->unsignedInteger('duracion_dias')->default(1);
            $table->decimal('precio_total', 10, 2);
            $table->string('estado')->default('Borrador');
            $table->text('observacion')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paquetes');
    }
};
