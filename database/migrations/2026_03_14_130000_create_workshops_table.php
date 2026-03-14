<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workshops', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('workshop_date');
            $table->string('workshop_time');           // e.g. "7:00 PM EST"
            $table->unsignedSmallInteger('duration_minutes')->default(60);
            $table->string('image_url')->nullable();
            $table->enum('status', ['upcoming', 'waitlist', 'cancelled'])->default('upcoming');
            $table->string('zoom_link')->nullable();
            $table->boolean('is_free')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['workshop_date', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workshops');
    }
};
