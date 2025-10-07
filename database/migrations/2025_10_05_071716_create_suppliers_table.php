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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_person');
            $table->string('email');
            $table->string('phone');
            $table->string('location');
            $table->enum('status', ['Active', 'Pending', 'Inactive'])->default('Pending');
            $table->decimal('rating', 3, 1)->default(0);
            $table->integer('total_orders')->default(0);
            $table->string('on_time_delivery')->default('100%');
            $table->decimal('total_spent', 10, 2)->default(0);
            $table->json('categories')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
