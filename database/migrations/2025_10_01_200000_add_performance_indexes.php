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
        Schema::table('medicines', function (Blueprint $table) {
            // Add composite indexes for common queries
            $table->index(['is_active', 'stock_quantity'], 'idx_medicines_active_stock');
            $table->index(['category_id', 'is_active'], 'idx_medicines_category_active');
            $table->index(['expiry_date', 'is_active'], 'idx_medicines_expiry_active');
            $table->index(['name', 'is_active'], 'idx_medicines_name_active');
            $table->index(['created_at', 'is_active'], 'idx_medicines_created_active');
            
            // Add full-text search index for name and generic_name
            $table->fullText(['name', 'generic_name'], 'idx_medicines_fulltext');
        });

        Schema::table('categories', function (Blueprint $table) {
            // Add indexes for category queries
            $table->index(['is_active', 'sort_order'], 'idx_categories_active_sort');
            $table->index(['name', 'is_active'], 'idx_categories_name_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->dropIndex('idx_medicines_active_stock');
            $table->dropIndex('idx_medicines_category_active');
            $table->dropIndex('idx_medicines_expiry_active');
            $table->dropIndex('idx_medicines_name_active');
            $table->dropIndex('idx_medicines_created_active');
            $table->dropIndex('idx_medicines_fulltext');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('idx_categories_active_sort');
            $table->dropIndex('idx_categories_name_active');
        });
    }
};
