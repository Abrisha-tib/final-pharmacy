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
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('generic_name')->nullable();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('strength');
            $table->string('form'); // Tablet, Capsule, Syrup, etc.
            $table->string('batch_number');
            $table->integer('stock_quantity')->default(0);
            $table->decimal('price', 10, 2);
            $table->decimal('cost', 10, 2);
            $table->date('expiry_date');
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('category_id');
            $table->index('is_active');
            $table->index('stock_quantity');
            $table->index('expiry_date');
            $table->index('batch_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
