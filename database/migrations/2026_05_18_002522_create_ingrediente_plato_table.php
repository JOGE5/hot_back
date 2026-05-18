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
        Schema::create('ingrediente_plato', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plato_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ingrediente_id')->constrained()->cascadeOnDelete();
            $table->decimal('cantidad_requerida', 10, 2);
            $table->timestamps();

            $table->unique(['plato_id', 'ingrediente_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingrediente_plato');
    }
};
