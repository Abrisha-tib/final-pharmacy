<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Global Health Supplies',
                'contact_person' => 'Mike Wilson',
                'email' => 'mike@globalhealth.com',
                'phone' => '+1-555-0103',
                'location' => 'Chicago, IL',
                'status' => 'Pending',
                'rating' => 0,
                'total_orders' => 2,
                'on_time_delivery' => '100%',
                'total_spent' => 5128.00,
                'categories' => []
            ],
            [
                'name' => 'MedSupply Co.',
                'contact_person' => 'John Smith',
                'email' => 'john@medsupply.com',
                'phone' => '+1-555-0101',
                'location' => 'New York, NY',
                'status' => 'Pending',
                'rating' => 0,
                'total_orders' => 5,
                'on_time_delivery' => '100%',
                'total_spent' => 14973.00,
                'categories' => []
            ],
            [
                'name' => 'PharmaDirect',
                'contact_person' => 'Sarah Johnson',
                'email' => 'sarah@pharmadirect.com',
                'phone' => '+1-555-0102',
                'location' => 'Los Angeles, CA',
                'status' => 'Pending',
                'rating' => 0,
                'total_orders' => 8,
                'on_time_delivery' => '100%',
                'total_spent' => 24837.00,
                'categories' => []
            ],
            [
                'name' => 'MedTech Solutions',
                'contact_person' => 'David Brown',
                'email' => 'david@medtech.com',
                'phone' => '+1-555-0104',
                'location' => 'Boston, MA',
                'status' => 'Active',
                'rating' => 4.5,
                'total_orders' => 12,
                'on_time_delivery' => '95%',
                'total_spent' => 32450.00,
                'categories' => []
            ],
            [
                'name' => 'HealthCare Plus',
                'contact_person' => 'Lisa Davis',
                'email' => 'lisa@healthcareplus.com',
                'phone' => '+1-555-0105',
                'location' => 'Miami, FL',
                'status' => 'Active',
                'rating' => 4.2,
                'total_orders' => 7,
                'on_time_delivery' => '98%',
                'total_spent' => 18750.00,
                'categories' => []
            ],
            [
                'name' => 'PharmaCorp',
                'contact_person' => 'Robert Wilson',
                'email' => 'robert@pharmacorp.com',
                'phone' => '+1-555-0106',
                'location' => 'Seattle, WA',
                'status' => 'Inactive',
                'rating' => 3.8,
                'total_orders' => 3,
                'on_time_delivery' => '90%',
                'total_spent' => 8200.00,
                'categories' => []
            ]
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
