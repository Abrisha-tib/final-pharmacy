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
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->enum('type', ['info', 'warning', 'error', 'success', 'critical'])->default('info');
            $table->enum('category', ['inventory', 'expiry', 'system', 'sales', 'customer', 'purchase', 'supplier'])->default('system');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['active', 'acknowledged', 'resolved', 'dismissed'])->default('active');
            $table->json('metadata')->nullable(); // Store additional data like medicine_id, expiry_date, etc.
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // User who created/triggered the alert
            $table->foreignId('acknowledged_by')->nullable()->constrained('users')->onDelete('set null'); // User who acknowledged the alert
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('expires_at')->nullable(); // For time-sensitive alerts
            $table->boolean('is_auto_generated')->default(false); // System vs manual alerts
            $table->string('source')->nullable(); // What triggered this alert (e.g., 'low_stock_checker', 'expiry_monitor')
            $table->timestamps();
            
            // Indexes for performance optimization
            $table->index(['status', 'priority']);
            $table->index(['category', 'type']);
            $table->index(['created_at']);
            $table->index(['expires_at']);
            $table->index(['is_auto_generated', 'source']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
