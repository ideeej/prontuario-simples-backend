<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_therapy_session', function (Blueprint $table) {
            $table->id();

            // Chaves estrangeiras
            $table->foreignId('patient_id')
                  ->constrained('patients')
                  ->onDelete('cascade');

            $table->foreignId('therapy_session_id')
                  ->constrained('therapy_sessions')
                  ->onDelete('cascade');

            // Campo opcional para identificar o papel do paciente
            $table->string('role')->nullable(); // 'main', 'partner', 'parent', 'child'

            $table->timestamps();

            // Evitar duplicatas
            $table->unique(['patient_id', 'therapy_session_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_therapy_session');
    }
};
