<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
            $table->integer('session_duration')->default(50);   // minutes
            $table->integer('buffer_time')->default(10);        // minutes between sessions
            $table->integer('advance_booking_days')->default(30); // how far ahead clients can book
            $table->string('timezone', 100)->default('America/Toronto');
            $table->boolean('is_booking_enabled')->default(true);
            $table->timestamps();

            $table->unique('admin_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_settings');
    }
};
