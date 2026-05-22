<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('papelera_vaciada_at')->nullable()->after('deleted_at');
            $table->foreignId('papelera_vaciada_por')
                ->nullable()
                ->after('papelera_vaciada_at')
                ->constrained('users')
                ->nullOnDelete();
        });

        Schema::table('huespedes', function (Blueprint $table) {
            $table->timestamp('papelera_vaciada_at')->nullable()->after('deleted_at');
            $table->foreignId('papelera_vaciada_por')
                ->nullable()
                ->after('papelera_vaciada_at')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('huespedes', function (Blueprint $table) {
            $table->dropForeign(['papelera_vaciada_por']);
            $table->dropColumn(['papelera_vaciada_at', 'papelera_vaciada_por']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['papelera_vaciada_por']);
            $table->dropColumn(['papelera_vaciada_at', 'papelera_vaciada_por']);
        });
    }
};
