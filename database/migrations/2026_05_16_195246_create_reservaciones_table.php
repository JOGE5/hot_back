<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservaciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('huesped_id')
                ->constrained('huespedes')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('habitacion_id')
                ->constrained('habitaciones')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('origen_reservacion', 50)->default('Recepción presencial');
            $table->date('fecha_entrada');
            $table->date('fecha_salida');
            $table->unsignedInteger('cantidad_personas');
            $table->decimal('total', 10, 2)->default(0);

            $table->string('estado_reservacion', 50)->default('Pendiente de pago');

            $table->string('metodo_pago', 50)->nullable();
            $table->string('estado_pago', 50)->default('Pendiente');

            $table->string('codigo_checkin', 20)->nullable()->unique();
            $table->text('observacion')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservaciones');
    }
};