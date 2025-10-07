<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PharmaceuticalUnitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            // Count Units
            ['name' => 'Tablets', 'symbol' => 'tab', 'category' => 'Count', 'description' => 'Individual tablet units', 'sort_order' => 1],
            ['name' => 'Capsules', 'symbol' => 'cap', 'category' => 'Count', 'description' => 'Individual capsule units', 'sort_order' => 2],
            ['name' => 'Pieces', 'symbol' => 'pcs', 'category' => 'Count', 'description' => 'Individual pieces', 'sort_order' => 3],
            ['name' => 'Individual Units', 'symbol' => 'unit', 'category' => 'Count', 'description' => 'Individual units', 'sort_order' => 4],
            ['name' => 'Doses', 'symbol' => 'dose', 'category' => 'Count', 'description' => 'Individual doses', 'sort_order' => 5],
            ['name' => 'Vials', 'symbol' => 'vial', 'category' => 'Count', 'description' => 'Individual vials', 'sort_order' => 6],
            ['name' => 'Ampoules', 'symbol' => 'amp', 'category' => 'Count', 'description' => 'Individual ampoules', 'sort_order' => 7],
            ['name' => 'Syringes', 'symbol' => 'syringe', 'category' => 'Count', 'description' => 'Individual syringes', 'sort_order' => 8],
            ['name' => 'Patches', 'symbol' => 'patch', 'category' => 'Count', 'description' => 'Individual patches', 'sort_order' => 9],
            ['name' => 'Suppositories', 'symbol' => 'supp', 'category' => 'Count', 'description' => 'Individual suppositories', 'sort_order' => 10],
            ['name' => 'Pessaries', 'symbol' => 'pess', 'category' => 'Count', 'description' => 'Individual pessaries', 'sort_order' => 11],
            ['name' => 'Inhalers', 'symbol' => 'inh', 'category' => 'Count', 'description' => 'Individual inhalers', 'sort_order' => 12],
            ['name' => 'Nebulizers', 'symbol' => 'neb', 'category' => 'Count', 'description' => 'Individual nebulizers', 'sort_order' => 13],
            ['name' => 'Strips', 'symbol' => 'strip', 'category' => 'Count', 'description' => 'Individual strips', 'sort_order' => 14],
            ['name' => 'Films', 'symbol' => 'film', 'category' => 'Count', 'description' => 'Individual films', 'sort_order' => 15],
            
            // Weight Units
            ['name' => 'Milligrams', 'symbol' => 'mg', 'category' => 'Weight', 'description' => 'Milligrams', 'sort_order' => 16],
            ['name' => 'Grams', 'symbol' => 'g', 'category' => 'Weight', 'description' => 'Grams', 'sort_order' => 17],
            ['name' => 'Kilograms', 'symbol' => 'kg', 'category' => 'Weight', 'description' => 'Kilograms', 'sort_order' => 18],
            ['name' => 'Micrograms', 'symbol' => 'mcg', 'category' => 'Weight', 'description' => 'Micrograms', 'sort_order' => 19],
            ['name' => 'Nanograms', 'symbol' => 'ng', 'category' => 'Weight', 'description' => 'Nanograms', 'sort_order' => 20],
            ['name' => 'Pounds', 'symbol' => 'lb', 'category' => 'Weight', 'description' => 'Pounds', 'sort_order' => 21],
            ['name' => 'Ounces', 'symbol' => 'oz', 'category' => 'Weight', 'description' => 'Ounces', 'sort_order' => 22],
            
            // Volume Units
            ['name' => 'Milliliters', 'symbol' => 'ml', 'category' => 'Volume', 'description' => 'Milliliters', 'sort_order' => 23],
            ['name' => 'Liters', 'symbol' => 'L', 'category' => 'Volume', 'description' => 'Liters', 'sort_order' => 24],
            ['name' => 'Microliters', 'symbol' => 'μl', 'category' => 'Volume', 'description' => 'Microliters', 'sort_order' => 25],
            ['name' => 'Fluid Ounces', 'symbol' => 'fl oz', 'category' => 'Volume', 'description' => 'Fluid ounces', 'sort_order' => 26],
            ['name' => 'Cups', 'symbol' => 'cup', 'category' => 'Volume', 'description' => 'Cups', 'sort_order' => 27],
            ['name' => 'Teaspoons', 'symbol' => 'tsp', 'category' => 'Volume', 'description' => 'Teaspoons', 'sort_order' => 28],
            ['name' => 'Tablespoons', 'symbol' => 'tbsp', 'category' => 'Volume', 'description' => 'Tablespoons', 'sort_order' => 29],
            ['name' => 'Drops', 'symbol' => 'drops', 'category' => 'Volume', 'description' => 'Drops', 'sort_order' => 30],
            
            // Length Units
            ['name' => 'Millimeters', 'symbol' => 'mm', 'category' => 'Length', 'description' => 'Millimeters', 'sort_order' => 31],
            ['name' => 'Centimeters', 'symbol' => 'cm', 'category' => 'Length', 'description' => 'Centimeters', 'sort_order' => 32],
            ['name' => 'Meters', 'symbol' => 'm', 'category' => 'Length', 'description' => 'Meters', 'sort_order' => 33],
            ['name' => 'Inches', 'symbol' => 'in', 'category' => 'Length', 'description' => 'Inches', 'sort_order' => 34],
            
            // Area Units
            ['name' => 'Square Centimeters', 'symbol' => 'cm²', 'category' => 'Area', 'description' => 'Square centimeters', 'sort_order' => 35],
            ['name' => 'Square Meters', 'symbol' => 'm²', 'category' => 'Area', 'description' => 'Square meters', 'sort_order' => 36],
            ['name' => 'Square Inches', 'symbol' => 'in²', 'category' => 'Area', 'description' => 'Square inches', 'sort_order' => 37],
            
            // Time Units
            ['name' => 'Hours', 'symbol' => 'hr', 'category' => 'Time', 'description' => 'Hours', 'sort_order' => 38],
            ['name' => 'Days', 'symbol' => 'day', 'category' => 'Time', 'description' => 'Days', 'sort_order' => 39],
            ['name' => 'Weeks', 'symbol' => 'wk', 'category' => 'Time', 'description' => 'Weeks', 'sort_order' => 40],
            ['name' => 'Months', 'symbol' => 'mo', 'category' => 'Time', 'description' => 'Months', 'sort_order' => 41],
            ['name' => 'Years', 'symbol' => 'yr', 'category' => 'Time', 'description' => 'Years', 'sort_order' => 42],
            
            // Concentration Units
            ['name' => 'Percent', 'symbol' => '%', 'category' => 'Concentration', 'description' => 'Percentage', 'sort_order' => 43],
            ['name' => 'Parts Per Million', 'symbol' => 'ppm', 'category' => 'Concentration', 'description' => 'Parts per million', 'sort_order' => 44],
            ['name' => 'International Units', 'symbol' => 'IU', 'category' => 'Concentration', 'description' => 'International units', 'sort_order' => 45],
            ['name' => 'Units', 'symbol' => 'U', 'category' => 'Concentration', 'description' => 'Units of activity', 'sort_order' => 46],
            
            // Specialized Units
            ['name' => 'Doses', 'symbol' => 'doses', 'category' => 'Specialized', 'description' => 'Number of doses', 'sort_order' => 47],
            ['name' => 'Applications', 'symbol' => 'app', 'category' => 'Specialized', 'description' => 'Number of applications', 'sort_order' => 48],
            ['name' => 'Treatments', 'symbol' => 'trt', 'category' => 'Specialized', 'description' => 'Number of treatments', 'sort_order' => 49],
            ['name' => 'Sessions', 'symbol' => 'sess', 'category' => 'Specialized', 'description' => 'Number of sessions', 'sort_order' => 50],
        ];

        foreach ($units as $unit) {
            DB::table('pharmaceutical_units')->insert(array_merge($unit, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
