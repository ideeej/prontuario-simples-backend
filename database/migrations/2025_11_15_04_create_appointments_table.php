<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Terapeuta
            $table->foreignId('patient_id')->nullable()->constrained('patients')->onDelete('cascade');
            $table->foreignId('therapy_session_id')->nullable()->constrained('therapy_sessions')->onDelete('cascade');

            $table->dateTime('start_date');
            $table->dateTime('end_date');

            // Status: 'scheduled', 'completed', 'cancelled', 'no_show'
            $table->string('status')->default('scheduled');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
