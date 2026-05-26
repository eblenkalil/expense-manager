<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('roles')->nullable()->after('role');
        });

        DB::statement("UPDATE users SET roles = JSON_ARRAY(role)");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->json('roles')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['collaborator', 'admin'])->default('collaborator')->after('email');
        });

        DB::statement("UPDATE users SET role = JSON_UNQUOTE(JSON_EXTRACT(roles, '$[0]'))");

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('roles');
        });
    }
};
