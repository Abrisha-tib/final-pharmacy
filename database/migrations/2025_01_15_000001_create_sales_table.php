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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('sale_number')->unique();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_email')->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->enum('payment_method', ['cash', 'card', 'mobile_payment', 'bank_transfer'])->default('cash');
            $table->enum('status', ['pending', 'completed', 'cancelled', 'refunded'])->default('completed');
            $table->boolean('prescription_required')->default(false);
            $table->text('notes')->nullable();
            $table->foreignId('sold_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('sale_date')->useCurrent();
            $table->timestamps();
            
            // Indexes for performance
            $table->index('sale_number');
            $table->index('customer_name');
            $table->index('status');
            $table->index('payment_method');
            $table->index('sale_date');
            $table->index('sold_by');
            $table->index(['sale_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
