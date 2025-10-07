<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'name' => 'Michael Brown',
                'email' => 'michael.brown@email.com',
                'phone' => '+1234567892',
                'address' => '123 Main Street',
                'city' => 'Addis Ababa',
                'country' => 'Ethiopia',
                'age' => 46,
                'loyalty_points' => 300,
                'total_spent' => 0.00,
                'status' => 'new',
                'segment' => 'new',
                'date_of_birth' => '1978-03-15',
                'gender' => 'male',
                'notes' => 'New customer, interested in diabetes management'
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@email.com',
                'phone' => '+1234567891',
                'address' => '456 Oak Avenue',
                'city' => 'Addis Ababa',
                'country' => 'Ethiopia',
                'age' => 35,
                'loyalty_points' => 75,
                'total_spent' => 0.00,
                'status' => 'new',
                'segment' => 'new',
                'date_of_birth' => '1989-07-22',
                'gender' => 'female',
                'notes' => 'Regular customer for family medications'
            ],
            [
                'name' => 'John Smith',
                'email' => 'john.smith@email.com',
                'phone' => '+1234567890',
                'address' => '789 Pine Road',
                'city' => 'Addis Ababa',
                'country' => 'Ethiopia',
                'age' => 40,
                'loyalty_points' => 150,
                'total_spent' => 0.00,
                'status' => 'new',
                'segment' => 'new',
                'date_of_birth' => '1984-11-08',
                'gender' => 'male',
                'notes' => 'Prefers generic medications'
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emily.davis@email.com',
                'phone' => '+1234567893',
                'address' => '321 Elm Street',
                'city' => 'Addis Ababa',
                'country' => 'Ethiopia',
                'age' => 28,
                'loyalty_points' => 500,
                'total_spent' => 1250.50,
                'status' => 'active',
                'segment' => 'loyal',
                'date_of_birth' => '1996-05-12',
                'gender' => 'female',
                'notes' => 'VIP customer, regular prescriptions'
            ],
            [
                'name' => 'David Wilson',
                'email' => 'david.wilson@email.com',
                'phone' => '+1234567894',
                'address' => '654 Maple Drive',
                'city' => 'Addis Ababa',
                'country' => 'Ethiopia',
                'age' => 52,
                'loyalty_points' => 750,
                'total_spent' => 2100.75,
                'status' => 'premium',
                'segment' => 'vip',
                'date_of_birth' => '1972-09-30',
                'gender' => 'male',
                'notes' => 'Premium customer, chronic condition management'
            ],
            [
                'name' => 'Lisa Anderson',
                'email' => 'lisa.anderson@email.com',
                'phone' => '+1234567895',
                'address' => '987 Cedar Lane',
                'city' => 'Addis Ababa',
                'country' => 'Ethiopia',
                'age' => 31,
                'loyalty_points' => 200,
                'total_spent' => 450.25,
                'status' => 'active',
                'segment' => 'regular',
                'date_of_birth' => '1993-12-03',
                'gender' => 'female',
                'notes' => 'Regular customer, prefers brand medications'
            ]
        ];

        foreach ($customers as $customerData) {
            Customer::create($customerData);
        }
    }
}