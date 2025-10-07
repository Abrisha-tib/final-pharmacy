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
        Schema::table('users', function (Blueprint $table) {
            // User status and activity tracking
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('email_verified_at');
            $table->timestamp('last_login_at')->nullable()->after('status');
            $table->timestamp('last_activity_at')->nullable()->after('last_login_at');
            $table->integer('login_attempts')->default(0)->after('last_activity_at');
            $table->timestamp('locked_until')->nullable()->after('login_attempts');
            
            // Profile information
            $table->string('phone')->nullable()->after('locked_until');
            $table->string('department')->nullable()->after('phone');
            $table->text('notes')->nullable()->after('department');
            $table->string('avatar')->nullable()->after('notes');
            
            // Audit fields
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null')->after('avatar');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null')->after('created_by');
            
            // Indexes for performance
            $table->index(['status', 'created_at']);
            $table->index('last_login_at');
            $table->index('department');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex('last_login_at');
            $table->dropIndex('department');
            
            // Drop foreign key constraints
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            
            // Drop columns
            $table->dropColumn([
                'status', 'last_login_at', 'last_activity_at', 'login_attempts', 
                'locked_until', 'phone', 'department', 'notes', 'avatar', 
                'created_by', 'updated_by'
            ]);
        });
    }
};
