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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('name');
            $table->unsignedSmallInteger('age');
            $table->string('gender');
            $table->string('occupation');
            $table->string('address');
            $table->unsignedSmallInteger('phone');
            $table->string('medical_history');
            $table->text('complaint');
            $table->text('dental_history');
            $table->string('pain_level');
            $table->string('dental_history_file');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
