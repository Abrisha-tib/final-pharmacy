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
        Schema::create('pharmaceutical_forms', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., "Tablet", "Capsule", "Injection"
            $table->string('code')->unique(); // e.g., "TAB", "CAP", "INJ"
            $table->text('description')->nullable();
            $table->string('category'); // Solid, Liquid, Semi-Solid, Parenteral, etc.
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
        Schema::dropIfExists('pharmaceutical_forms');
    }
};
