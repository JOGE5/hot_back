<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservacion_acompanantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservacion_id')->constrained('reservaciones')->onDelete('cascade');
            $table->string('nombre');
            $table->string('documento');
            $table->unsignedSmallInteger('edad');
            $table->timestamps();

            $table->unique(['reservacion_id', 'documento']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservacion_acompanantes');
    }
};
