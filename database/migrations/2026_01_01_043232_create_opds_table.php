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
        Schema::create('opds', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('address');
            $table->string('phone')->nullable();
            $table->string('operational_hours')->nullable();
            $table->text('required_education')->nullable(); // Pendidikan yang dicari
            $table->text('document_requirements')->nullable(); // Persyaratan dokumen
            $table->text('description')->nullable();
            $table->json('documentation_images')->nullable(); // Dokumentasi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opds');
    }
};
