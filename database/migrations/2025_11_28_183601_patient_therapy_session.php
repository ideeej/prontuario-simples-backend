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
        Schema::create('patient_therapy_session', function (Blueprint $table) {
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('therapy_session_id')->nullable()->constrained('therapy_sessions')->onDelete('cascade');
            $table->primary(['patient_id', 'therapy_session_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_therapy_session');
    }
};
