<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->datetime('appointment_date');
            $table->enum('status', ['scheduled', 'confirmed', 'in_progress', 'completed', 'canceled', 'no_show'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->text('symptons')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('treatment')->nullable();
            $table->text('prescription')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable(); 

            $table->index(['appointment_date', 'status']);
            $table->index(['patient_id', 'appointment_date']);
            $table->index(['doctor_id', 'appointment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
