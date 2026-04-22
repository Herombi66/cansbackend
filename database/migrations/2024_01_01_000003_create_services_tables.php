<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category')->default('general');
            $table->string('image_path')->nullable();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->timestamps();
        });

        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address')->nullable();
            $table->timestamps();
        });

        Schema::create('school_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('grade');
            $table->timestamps();
        });

        Schema::create('hospitals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address')->nullable();
            $table->timestamps();
        });

        Schema::create('hospital_patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('condition')->nullable();
            $table->timestamps();
        });

        Schema::create('corporates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('industry')->nullable();
            $table->timestamps();
        });

        Schema::create('corporate_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('corporate_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('position');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corporate_employees');
        Schema::dropIfExists('corporates');
        Schema::dropIfExists('hospital_patients');
        Schema::dropIfExists('hospitals');
        Schema::dropIfExists('school_students');
        Schema::dropIfExists('schools');
        Schema::dropIfExists('contents');
    }
};