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
        Schema::create('academic_year_creation', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->unique();
            $table->string('from_year');
            $table->string('to_year');
            $table->string('acc_year')->nullable(false);
            $table->string('remarks');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_year_creation');
    }
};