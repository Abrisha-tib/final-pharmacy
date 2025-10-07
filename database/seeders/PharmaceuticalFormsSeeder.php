<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PharmaceuticalFormsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $forms = [
            // Solid Dosage Forms
            ['name' => 'Tablet', 'code' => 'TAB', 'category' => 'Solid', 'description' => 'Compressed solid dosage form', 'sort_order' => 1],
            ['name' => 'Capsule', 'code' => 'CAP', 'category' => 'Solid', 'description' => 'Gelatin shell containing medication', 'sort_order' => 2],
            ['name' => 'Powder', 'code' => 'POW', 'category' => 'Solid', 'description' => 'Finely divided solid particles', 'sort_order' => 3],
            ['name' => 'Granules', 'code' => 'GRA', 'category' => 'Solid', 'description' => 'Agglomerated particles', 'sort_order' => 4],
            ['name' => 'Lozenge', 'code' => 'LOZ', 'category' => 'Solid', 'description' => 'Medicated candy for oral dissolution', 'sort_order' => 5],
            ['name' => 'Troche', 'code' => 'TRO', 'category' => 'Solid', 'description' => 'Medicated tablet for oral dissolution', 'sort_order' => 6],
            ['name' => 'Chewable Tablet', 'code' => 'CHT', 'category' => 'Solid', 'description' => 'Tablet designed to be chewed', 'sort_order' => 7],
            ['name' => 'Sublingual Tablet', 'code' => 'SLT', 'category' => 'Solid', 'description' => 'Tablet for sublingual administration', 'sort_order' => 8],
            ['name' => 'Buccal Tablet', 'code' => 'BUC', 'category' => 'Solid', 'description' => 'Tablet for buccal administration', 'sort_order' => 9],
            ['name' => 'Effervescent Tablet', 'code' => 'EFT', 'category' => 'Solid', 'description' => 'Tablet that dissolves in water', 'sort_order' => 10],
            
            // Liquid Dosage Forms
            ['name' => 'Syrup', 'code' => 'SYP', 'category' => 'Liquid', 'description' => 'Sweetened liquid preparation', 'sort_order' => 11],
            ['name' => 'Elixir', 'code' => 'ELX', 'category' => 'Liquid', 'description' => 'Clear, sweetened hydroalcoholic solution', 'sort_order' => 12],
            ['name' => 'Solution', 'code' => 'SOL', 'category' => 'Liquid', 'description' => 'Homogeneous liquid mixture', 'sort_order' => 13],
            ['name' => 'Suspension', 'code' => 'SUS', 'category' => 'Liquid', 'description' => 'Heterogeneous liquid with undissolved particles', 'sort_order' => 14],
            ['name' => 'Emulsion', 'code' => 'EMU', 'category' => 'Liquid', 'description' => 'Mixture of immiscible liquids', 'sort_order' => 15],
            ['name' => 'Tincture', 'code' => 'TIN', 'category' => 'Liquid', 'description' => 'Alcoholic or hydroalcoholic solution', 'sort_order' => 16],
            ['name' => 'Drops', 'code' => 'DRP', 'category' => 'Liquid', 'description' => 'Liquid administered in drops', 'sort_order' => 17],
            ['name' => 'Mouthwash', 'code' => 'MOU', 'category' => 'Liquid', 'description' => 'Liquid for oral rinsing', 'sort_order' => 18],
            ['name' => 'Gargle', 'code' => 'GAR', 'category' => 'Liquid', 'description' => 'Liquid for throat rinsing', 'sort_order' => 19],
            
            // Semi-Solid Dosage Forms
            ['name' => 'Cream', 'code' => 'CRE', 'category' => 'Semi-Solid', 'description' => 'Semi-solid emulsion for external use', 'sort_order' => 20],
            ['name' => 'Ointment', 'code' => 'OIN', 'category' => 'Semi-Solid', 'description' => 'Semi-solid preparation for external use', 'sort_order' => 21],
            ['name' => 'Gel', 'code' => 'GEL', 'category' => 'Semi-Solid', 'description' => 'Semi-solid colloidal system', 'sort_order' => 22],
            ['name' => 'Paste', 'code' => 'PAS', 'category' => 'Semi-Solid', 'description' => 'Semi-solid with high proportion of solids', 'sort_order' => 23],
            ['name' => 'Lotion', 'code' => 'LOT', 'category' => 'Semi-Solid', 'description' => 'Liquid preparation for external use', 'sort_order' => 24],
            ['name' => 'Foam', 'code' => 'FOA', 'category' => 'Semi-Solid', 'description' => 'Aerated semi-solid preparation', 'sort_order' => 25],
            ['name' => 'Suppository', 'code' => 'SUP', 'category' => 'Semi-Solid', 'description' => 'Solid form for insertion into body cavities', 'sort_order' => 26],
            ['name' => 'Pessary', 'code' => 'PES', 'category' => 'Semi-Solid', 'description' => 'Vaginal suppository', 'sort_order' => 27],
            
            // Parenteral Dosage Forms
            ['name' => 'Injection', 'code' => 'INJ', 'category' => 'Parenteral', 'description' => 'Sterile preparation for injection', 'sort_order' => 28],
            ['name' => 'Infusion', 'code' => 'INF', 'category' => 'Parenteral', 'description' => 'Large volume parenteral solution', 'sort_order' => 29],
            ['name' => 'Implant', 'code' => 'IMP', 'category' => 'Parenteral', 'description' => 'Solid form for implantation', 'sort_order' => 30],
            
            // Specialized Forms
            ['name' => 'Patch', 'code' => 'PAT', 'category' => 'Transdermal', 'description' => 'Adhesive patch for transdermal delivery', 'sort_order' => 31],
            ['name' => 'Inhaler', 'code' => 'INH', 'category' => 'Respiratory', 'description' => 'Device for respiratory administration', 'sort_order' => 32],
            ['name' => 'Nebulizer', 'code' => 'NEB', 'category' => 'Respiratory', 'description' => 'Device for aerosol administration', 'sort_order' => 33],
            ['name' => 'Nasal Spray', 'code' => 'NAS', 'category' => 'Respiratory', 'description' => 'Liquid for nasal administration', 'sort_order' => 34],
            ['name' => 'Eye Drops', 'code' => 'EYE', 'category' => 'Ophthalmic', 'description' => 'Liquid for ophthalmic use', 'sort_order' => 35],
            ['name' => 'Eye Ointment', 'code' => 'EYO', 'category' => 'Ophthalmic', 'description' => 'Semi-solid for ophthalmic use', 'sort_order' => 36],
            ['name' => 'Ear Drops', 'code' => 'EAR', 'category' => 'Otic', 'description' => 'Liquid for ear administration', 'sort_order' => 37],
            ['name' => 'Rectal Suppository', 'code' => 'REC', 'category' => 'Rectal', 'description' => 'Suppository for rectal administration', 'sort_order' => 38],
            ['name' => 'Vaginal Cream', 'code' => 'VAG', 'category' => 'Vaginal', 'description' => 'Cream for vaginal administration', 'sort_order' => 39],
            
            // Specialized Solid Forms
            ['name' => 'Film', 'code' => 'FIL', 'category' => 'Specialized', 'description' => 'Thin film for oral dissolution', 'sort_order' => 40],
            ['name' => 'Strip', 'code' => 'STR', 'category' => 'Specialized', 'description' => 'Medicated strip', 'sort_order' => 41],
            ['name' => 'Wafer', 'code' => 'WAF', 'category' => 'Specialized', 'description' => 'Thin, flat solid form', 'sort_order' => 42],
            ['name' => 'Pellets', 'code' => 'PEL', 'category' => 'Specialized', 'description' => 'Small spherical solid forms', 'sort_order' => 43],
        ];

        foreach ($forms as $form) {
            DB::table('pharmaceutical_forms')->insert(array_merge($form, [
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
