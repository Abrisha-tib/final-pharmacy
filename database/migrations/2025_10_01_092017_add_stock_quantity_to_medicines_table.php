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
        try {
            Schema::table('medicines', function (Blueprint $table) {
                $table->integer('stock_quantity')->default(0);
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
        try {
            Schema::table('medicines', function (Blueprint $table) {
                $table->dropColumn('stock_quantity');
            });
        } catch (Exception $e) {
            // Column might not exist, continue
        }
    }
};
