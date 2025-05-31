<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasTable('migrations')) {
            Schema::create('migrations', function (Blueprint $table) {
                $table->id();
                $table->string('migration');
                $table->integer('batch');
                $table->timestamps();
            });

            // Registrar as migrações existentes
            DB::table('migrations')->insert([
                ['migration' => '2025_05_30_155716_create_sessions_table', 'batch' => 1],
                ['migration' => '2025_05_30_160358_create_patients_table', 'batch' => 1],
                ['migration' => '2025_05_30_170813_create_users_table', 'batch' => 1],
                ['migration' => '2025_05_30_171440_create_appointments_table', 'batch' => 1],
            ]);
        }
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
