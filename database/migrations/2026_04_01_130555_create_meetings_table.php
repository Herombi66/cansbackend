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
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_id')->unique();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone_number');
            $table->string('company');
            $table->string('meeting_type');
            $table->integer('duration'); // in minutes
            $table->dateTime('scheduled_at');
            $table->string('timezone');
            $table->text('message')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'rejected', 'completed'])->default('pending');
            $table->text('internal_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
