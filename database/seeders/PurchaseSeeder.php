<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Medicine;
use App\Models\User;

class PurchaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Note: This seeder is intentionally empty to ensure only real data is used.
     * All purchase data should be created through the actual Purchase Management interface.
     */
    public function run(): void
    {
        $this->command->info('Purchase seeder completed - no mock data created. Use the Purchase Management interface to create real purchase orders.');
    }
}