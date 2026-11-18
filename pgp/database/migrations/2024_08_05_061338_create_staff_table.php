<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('acc_year')->nullable(); // Nullable if not required
            $table->string('emp_name');
            $table->string('emp_id')->unique(); // Unique employee ID
            $table->string('designation');
            $table->string('district');
            $table->string('zone');
            $table->string('school');
            $table->date('doj'); // Date of joining
            $table->string('emp_contact', 15); // Employee contact number
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
