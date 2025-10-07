<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\UserPreferences;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate existing preferences from notes field to user_preferences table
        $users = User::whereNotNull('notes')->get();
        
        foreach ($users as $user) {
            $notes = $user->notes;
            
            // Check if notes contains JSON preferences
            if (str_contains($notes, 'theme') && str_contains($notes, 'language')) {
                $preferences = json_decode($notes, true);
                
                if (is_array($preferences)) {
                    // Create user preferences record
                    UserPreferences::create([
                        'user_id' => $user->id,
                        'theme' => $preferences['theme'] ?? 'auto',
                        'language' => $preferences['language'] ?? 'en',
                        'timezone' => $preferences['timezone'] ?? 'Africa/Addis_Ababa',
                        'date_format' => $preferences['date_format'] ?? 'Y-m-d',
                        'time_format' => $preferences['time_format'] ?? '24',
                        'currency' => $preferences['currency'] ?? 'ETB',
                        'currency_symbol' => $preferences['currency_symbol'] ?? 'Br',
                        'notifications' => $preferences['notifications'] ?? [],
                        'dashboard_widgets' => $preferences['dashboard_widgets'] ?? [],
                    ]);
                    
                    // Clear the notes field
                    $user->update(['notes' => null]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not easily reversible
        // User preferences will remain in the new table
    }
};