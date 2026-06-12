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
        Schema::create('consumos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('reservacion_id')
                ->nullable()
                ->constrained('reservaciones')
                ->nullOnDelete();

            $table->foreignId('huesped_id')
                ->nullable()
                ->constrained('huespedes')
                ->nullOnDelete();

            $table->foreignId('habitacion_id')
                ->nullable()
                ->constrained('habitaciones')
                ->nullOnDelete();

            $table->foreignId('plato_id')
                ->constrained('platos')
                ->restrictOnDelete();

            $table->unsignedInteger('cantidad')->default(1);
            $table->decimal('precio_unitario', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            $table->dateTime('fecha_consumo')->nullable();

            $table->string('estado', 50)->default('Confirmado');
            $table->text('observacion')->nullable();

            $table->foreignId('registrado_por')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumos');
    }
};