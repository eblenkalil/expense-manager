<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('job_postings')->cascadeOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('linkedin')->nullable();
            $table->decimal('salary_expectation', 10, 2)->nullable();
            $table->string('cv_path')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'interview', 'hired', 'discarded'])->default('pending');
            $table->enum('source', ['manual', 'public_form'])->default('manual');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
