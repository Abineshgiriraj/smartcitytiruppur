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
        Schema::create('standard_creation', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id');
            $table->string('school_id');
            $table->string('school_name');
            $table->string('school_address');
            $table->string('school_taluk');
            $table->string('school_district');
            $table->string('school_zone');
            $table->string('medium');
            $table->string('standard');
            $table->string('remarks');
            $table->integer('is_active')->default(1);
            $table->integer('is_delete')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('standard_creation');
    }
};