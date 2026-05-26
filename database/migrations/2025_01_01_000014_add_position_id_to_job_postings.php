<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->foreignId('position_id')->nullable()->constrained('positions')->nullOnDelete()->after('title');
        });

        // Migra strings existentes para a tabela positions
        $positions = DB::table('job_postings')->whereNotNull('position')->pluck('position')->unique();
        foreach ($positions as $positionName) {
            $pos = DB::table('positions')->insertGetId(['name' => $positionName, 'active' => 1, 'created_at' => now(), 'updated_at' => now()]);
            DB::table('job_postings')->where('position', $positionName)->update(['position_id' => $pos]);
        }

        Schema::table('job_postings', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }

    public function down(): void
    {
        Schema::table('job_postings', function (Blueprint $table) {
            $table->string('position')->nullable()->after('title');
        });

        $postings = DB::table('job_postings')->whereNotNull('position_id')->get();
        foreach ($postings as $posting) {
            $pos = DB::table('positions')->find($posting->position_id);
            DB::table('job_postings')->where('id', $posting->id)->update(['position' => $pos?->name]);
        }

        Schema::table('job_postings', function (Blueprint $table) {
            $table->dropForeign(['position_id']);
            $table->dropColumn('position_id');
        });
    }
};
