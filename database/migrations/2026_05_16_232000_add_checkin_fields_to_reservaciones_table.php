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
        Schema::table('reservaciones', function (Blueprint $table) {
            $table->dateTime('checkin_at')->nullable()->after('estado_reservacion');
            $table->foreignId('checkin_user_id')->nullable()->after('checkin_at')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservaciones', function (Blueprint $table) {
            $table->dropForeign(['checkin_user_id']);
            $table->dropColumn('checkin_user_id');
            $table->dropColumn('checkin_at');
        });
    }
};
