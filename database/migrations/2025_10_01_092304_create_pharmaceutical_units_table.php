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
        Schema::create('pharmaceutical_units', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., "Tablets", "Capsules", "ml", "mg"
            $table->string('symbol')->unique(); // e.g., "tab", "cap", "ml", "mg"
            $table->text('description')->nullable();
            $table->string('category'); // Count, Volume, Weight, Length, etc.
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index('category');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmaceutical_units');
    }
};
