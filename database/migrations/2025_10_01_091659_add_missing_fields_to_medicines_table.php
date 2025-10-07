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
        // Add missing fields one by one with try-catch to handle existing columns
        try {
            Schema::table('medicines', function (Blueprint $table) {
                $table->integer('reorder_level')->default(0);
            });
        } catch (Exception $e) {
            // Column might already exist, continue
        }
        
        try {
            Schema::table('medicines', function (Blueprint $table) {
                $table->string('barcode')->nullable();
            });
        } catch (Exception $e) {
            // Column might already exist, continue
        }
        
        try {
            Schema::table('medicines', function (Blueprint $table) {
                $table->string('manufacturer')->nullable();
            });
        } catch (Exception $e) {
            // Column might already exist, continue
        }
        
        try {
            Schema::table('medicines', function (Blueprint $table) {
                $table->enum('prescription_required', ['yes', 'no'])->default('no');
            });
        } catch (Exception $e) {
            // Column might already exist, continue
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            // Remove the added fields
            $table->dropIndex(['barcode']);
            $table->dropIndex(['manufacturer']);
            $table->dropIndex(['prescription_required']);
            
            $table->dropColumn([
                'reorder_level',
                'barcode', 
                'manufacturer',
                'prescription_required'
            ]);
        });
    }
};
