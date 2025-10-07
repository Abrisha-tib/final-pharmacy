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
        Schema::table('categories', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->string('color', 7)->default('#3B82F6')->after('description');
            $table->string('icon')->default('tag')->after('color');
            $table->boolean('is_active')->default(true)->after('icon');
            $table->integer('sort_order')->default(0)->after('is_active');
            
            // Add indexes
            $table->index('is_active');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['sort_order']);
            $table->dropColumn(['description', 'color', 'icon', 'is_active', 'sort_order']);
        });
    }
};
