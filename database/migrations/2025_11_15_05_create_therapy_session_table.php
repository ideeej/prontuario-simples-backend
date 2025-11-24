<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('therapy_sessions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained();

            $table->foreignId('appointment_id')->nullable()->constrained();
            $table->foreignId('charge_id')->nullable()->constrained();

            $table->text('notes')->nullable(); // Anotações da sessão
            $table->text('transcription')->nullable(); // Transcrição automática

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('therapy_sessions');
    }
};
