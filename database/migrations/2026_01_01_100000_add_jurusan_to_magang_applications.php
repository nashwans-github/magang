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
        Schema::table('magang_applications', function (Blueprint $table) {
            $table->string('jurusan')->nullable()->after('institution_name');
        });

        Schema::table('magang_application_members', function (Blueprint $table) {
            $table->string('jurusan')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('magang_applications', function (Blueprint $table) {
            $table->dropColumn('jurusan');
        });

        Schema::table('magang_application_members', function (Blueprint $table) {
            $table->dropColumn('jurusan');
        });
    }
};
