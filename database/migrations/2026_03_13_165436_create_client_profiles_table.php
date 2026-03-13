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
        Schema::create('client_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            
            // Personal & Demographics
            $table->string('preferred_name')->nullable();
            $table->string('pronouns')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender_identity')->nullable();
            $table->string('phone')->nullable();
            $table->string('timezone')->default('UTC');
            $table->string('occupation')->nullable();

            // Emergency Contact (Critical for Therapy)
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relationship')->nullable();

            // Clinical Context
            $table->text('reason_for_therapy')->nullable();
            $table->boolean('previous_therapy_experience')->default(false);
            $table->text('current_medications')->nullable();
            $table->text('mental_health_history')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_profiles');
    }
};
