<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_availability_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
            $table->date('date');
            $table->boolean('is_blocked')->default(true); // true = blocked all day, false = custom hours
            $table->time('start_time')->nullable();       // custom start if not blocked
            $table->time('end_time')->nullable();         // custom end if not blocked
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->unique(['admin_id', 'date']);
            $table->index(['admin_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_availability_exceptions');
    }
};
