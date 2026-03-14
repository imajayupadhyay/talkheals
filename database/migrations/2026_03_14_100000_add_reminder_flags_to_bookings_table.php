<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->boolean('reminder_24h_sent')->default(false)->after('cancellation_reason');
            $table->boolean('reminder_12h_sent')->default(false)->after('reminder_24h_sent');
            $table->boolean('reminder_30min_sent')->default(false)->after('reminder_12h_sent');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['reminder_24h_sent', 'reminder_12h_sent', 'reminder_30min_sent']);
        });
    }
};
