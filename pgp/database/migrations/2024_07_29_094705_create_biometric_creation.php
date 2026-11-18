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
        Schema::create('biometric_creation', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id');
            $table->string('zone');
            $table->string('district');
            $table->string('taluk');
            $table->string('school_location');
            $table->string('device_code');
            $table->string('device_name');
            $table->string('serial_no');
            $table->string('ip_details');
            $table->string('user_name');
            $table->string('password');
            $table->string('created_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biometric_creation');
    }
};