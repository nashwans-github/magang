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
        Schema::create('penilaians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_id')->constrained('pesertas')->cascadeOnDelete();
            $table->foreignId('pembimbing_id')->constrained('pembimbings')->cascadeOnDelete();
            $table->decimal('attendance_score', 5, 2)->default(0); // Kehadiran
            $table->decimal('discipline_score', 5, 2)->default(0); // Ketepatan waktu
            $table->decimal('task_completion_score', 5, 2)->default(0); // Tugas selesai
            $table->decimal('deadline_accuracy_score', 5, 2)->default(0); // Ketepatan deadline
            $table->decimal('independence_score', 5, 2)->default(0); // Kemandirian
            $table->decimal('final_score', 5, 2)->default(0);
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaians');
    }
};
