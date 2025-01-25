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
        Schema::create('examinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained(); // Link to the patient
            $table->string('last_extraction')->nullable();
            $table->text('problem_satisfaction_patient')->nullable();
            $table->text('problem_satisfaction_dentist')->nullable();
            $table->text('face_form')->nullable();
            $table->text('facial_profile')->nullable();
            $table->text('facial_complexion')->nullable();
            $table->text('tmj')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examinations');
    }
};
