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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('reservacion_id')
                  ->constrained('reservaciones')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();
                  
            $table->decimal('monto', 10, 2);
            $table->string('metodo_pago', 50);
            $table->string('estado_pago', 50)->default('Pendiente');
            $table->dateTime('fecha_pago')->nullable();
            $table->string('comprobante')->nullable();
            $table->text('observacion')->nullable();
            
            $table->foreignId('registrado_por')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->cascadeOnUpdate();
                  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
