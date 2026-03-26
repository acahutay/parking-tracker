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
        Schema::create('parking_sections', function (Blueprint $table) {
            $table->id();
            $table->string('floor');
            $table->string('section_code');
            $table->unsignedInteger('capacity')->default(5);
            $table->unsignedInteger('available_spaces')->default(5);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parking_sections');
    }
};
