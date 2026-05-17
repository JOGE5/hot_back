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
            $table->string('codigo_checkout')->nullable()->unique()->after('checkin_user_id');
            $table->dateTime('checkout_at')->nullable()->after('codigo_checkout');
            $table->foreignId('checkout_user_id')->nullable()->after('checkout_at')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservaciones', function (Blueprint $table) {
            $table->dropForeign(['checkout_user_id']);
            $table->dropColumn('checkout_user_id');
            $table->dropColumn('checkout_at');
            $table->dropColumn('codigo_checkout');
        });
    }
};
