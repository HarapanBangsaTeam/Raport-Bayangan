<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nis')->nullable();
            $table->string('nisn')->nullable();
            $table->foreignId('classroom_id')->constrained()->cascadeOnDelete();
            $table->string('gender')->nullable(); // L / P
            $table->string('birthplace')->nullable();
            $table->date('birthdate')->nullable();
            $table->text('address')->nullable();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('Aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
