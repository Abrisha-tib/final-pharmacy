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
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('theme')->default('auto');
            $table->string('language')->default('en');
            $table->string('timezone')->default('Africa/Addis_Ababa');
            $table->string('date_format')->default('Y-m-d');
            $table->string('time_format')->default('24');
            $table->string('currency')->default('ETB');
            $table->string('currency_symbol')->default('Br');
            $table->json('notifications')->nullable();
            $table->json('dashboard_widgets')->nullable();
            $table->timestamps();
            
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};
