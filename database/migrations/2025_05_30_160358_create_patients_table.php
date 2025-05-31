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
        Schema::create('patients', function (Blueprint $table) {
             $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('cpf', 11)->unique();
            $table->date('birth_date');
            $table->enum('gender', ['M', 'F', 'O'])->comment('M=Masculino, F=Feminino, O=Outro');
            $table->text('address');
            $table->string('city');
            $table->string('state', 2);
            $table->text('medical_history')->nullable();
            $table->text('allergies')->nullable();
            $table->text('medications')->nullable();
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_phone');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
