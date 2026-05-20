<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paquetes', function (Blueprint $table) {
            $table->foreignId('tour_id')
                ->nullable()
                ->after('tour_incluido')
                ->constrained('tours')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('paquetes', function (Blueprint $table) {
            $table->dropForeign(['tour_id']);
            $table->dropColumn('tour_id');
        });
    }
};
