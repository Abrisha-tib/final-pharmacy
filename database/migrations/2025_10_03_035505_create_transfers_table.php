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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicine_id')->constrained()->onDelete('cascade');
            $table->integer('quantity_transferred');
            $table->integer('quantity_remaining');
            $table->string('batch_number');
            $table->date('expiry_date');
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->enum('transfer_type', ['inventory_to_dispensary', 'dispensary_to_inventory']);
            $table->foreignId('transferred_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('transferred_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('medicine_id');
            $table->index('status');
            $table->index('transfer_type');
            $table->index('transferred_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
