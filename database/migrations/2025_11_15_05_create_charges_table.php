<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('therapy_session_id')->nullable()->constrained('therapy_sessions')->onDelete('cascade');

            // No banco fica salvo em centavos
            $table->integer('amount');

            // pending, paid, cancelled
            $table->string('status')->default('pending');
            $table->date('due_date')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('charges');
    }
};
