<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $databaseName = config('database.connections.mysql.database');
        $charset = config('database.connections.mysql.charset', 'utf8mb4');
        $collation = config('database.connections.mysql.collation', 'utf8mb4_unicode_ci');

        DB::statement("CREATE DATABASE IF NOT EXISTS `{$databaseName}` CHARACTER SET {$charset} COLLATE {$collation};");
    }

    public function down()
    {
        // Não remova o banco de dados no rollback
    }
};