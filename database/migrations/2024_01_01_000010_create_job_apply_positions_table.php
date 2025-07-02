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
        Schema::create('job_apply_positions', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('society_id')->constrained('societies')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('job_vacancy_id')->nullable()->constrained('job_vacancies')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('position_id')->nullable()->constrained('available_positions')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('job_apply_societies_id')->nullable()->constrained('job_apply_societies')->onDelete('cascade')->onUpdate('cascade');
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_apply_positions');
    }
};
