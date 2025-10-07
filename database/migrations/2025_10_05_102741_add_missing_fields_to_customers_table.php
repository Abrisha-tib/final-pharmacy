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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('state')->nullable()->after('city');
            $table->string('zip_code', 20)->nullable()->after('state');
            $table->string('emergency_phone', 20)->nullable()->after('emergency_contact');
            $table->string('tags', 500)->nullable()->after('insurance_number');
            $table->boolean('is_active')->default(true)->after('tags');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['state', 'zip_code', 'emergency_phone', 'tags', 'is_active']);
        });
    }
};
