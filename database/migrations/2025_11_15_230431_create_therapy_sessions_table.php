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
            $table->text('therapy_record')->nullable();

            // Foreign Keys
            $table->foreignId('appointment_id')
                  ->nullable()
                  ->constrained('appointments')
                  ->onDelete('set null');

            $table->foreignId('charge_id')
                  ->nullable()
                  ->constrained('charges')
                  ->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('therapy_sessions');
    }
};
