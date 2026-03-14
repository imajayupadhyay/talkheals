<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Store Google OAuth tokens on the admin user
        Schema::table('users', function (Blueprint $table) {
            $table->json('google_token')->nullable()->after('remember_token');
        });

        // Store Meet link on each booking
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('google_meet_link')->nullable()->after('cancellation_reason');
            $table->string('google_event_id')->nullable()->after('google_meet_link');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('google_token');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['google_meet_link', 'google_event_id']);
        });
    }
};
