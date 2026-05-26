<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->string('cpf', 14)->nullable()->after('name');
        });

        // Adiciona second_interview ao ENUM de status
        DB::statement("ALTER TABLE candidates MODIFY COLUMN status ENUM('pending','interview','second_interview','hired','discarded') DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("UPDATE candidates SET status = 'interview' WHERE status = 'second_interview'");
        DB::statement("ALTER TABLE candidates MODIFY COLUMN status ENUM('pending','interview','hired','discarded') DEFAULT 'pending'");

        Schema::table('candidates', function (Blueprint $table) {
            $table->dropColumn('cpf');
        });
    }
};
