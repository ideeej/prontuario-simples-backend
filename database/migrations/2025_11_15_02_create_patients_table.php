<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();

            $table->foreignId(column: 'user_id')->constrained('users')->onDelete('cascade');

            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('document')->unique();
            $table->string('phone_number')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('address')->nullable();
            $table->longText('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
