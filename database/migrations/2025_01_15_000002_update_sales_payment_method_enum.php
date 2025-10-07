<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the payment_method enum to include tele_birr
        DB::statement("ALTER TABLE sales MODIFY COLUMN payment_method ENUM('cash', 'card', 'mobile_payment', 'bank_transfer', 'tele_birr') DEFAULT 'cash'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE sales MODIFY COLUMN payment_method ENUM('cash', 'card', 'mobile_payment', 'bank_transfer') DEFAULT 'cash'");
    }
};
