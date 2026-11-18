<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->unique();
            $table->string('emis_no');
            $table->string('biometric_id');
            $table->string('device_id');
            $table->string('school_id');
            $table->string('udise_code');
            $table->string('student_name');
            $table->string('dob');
            $table->string('cur_std');
            $table->string('group_name');
            $table->string('class_sec');
            $table->string('medium');
            $table->string('schl_name');
            $table->string('schl_block');
            $table->string('schl_district');
            $table->string('father_name');
            $table->string('mother_name');
            $table->string('is_delete')->default('0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};