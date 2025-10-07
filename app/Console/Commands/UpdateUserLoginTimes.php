<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;

class UpdateUserLoginTimes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:update-login-times {--set-now : Set all users last_login_at to now}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update user login times for existing users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating user login times...');

        if ($this->option('set-now')) {
            // Set all users' last_login_at to now
            $updated = User::whereNull('last_login_at')
                ->orWhere('last_login_at', '<', now()->subDays(1))
                ->update([
                    'last_login_at' => now(),
                    'last_activity_at' => now()
                ]);
            
            $this->info("Updated {$updated} users with current login time.");
        } else {
            // Show current status
            $totalUsers = User::count();
            $usersWithLogin = User::whereNotNull('last_login_at')->count();
            $usersWithoutLogin = User::whereNull('last_login_at')->count();
            
            $this->info("Total users: {$totalUsers}");
            $this->info("Users with login time: {$usersWithLogin}");
            $this->info("Users without login time: {$usersWithoutLogin}");
            
            if ($usersWithoutLogin > 0) {
                $this->warn("Found {$usersWithoutLogin} users without login times.");
                $this->info("Run with --set-now to update them with current time.");
            }
        }

        return 0;
    }
}