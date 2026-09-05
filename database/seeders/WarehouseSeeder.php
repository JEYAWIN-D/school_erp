<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\AdmissionKitConfig;
use App\Models\Classes;
use App\Models\AcademicYear;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Categories
        $categoriesData = [
            ['name' => 'Uniform & Apparel', 'code' => 'CAT-UNIFORM', 'description' => 'School uniforms, dress sets, ties, belts'],
            ['name' => 'Footwear', 'code' => 'CAT-FOOTWEAR', 'description' => 'Formal shoes, sports shoes, socks'],
            ['name' => 'Bags & Accessories', 'code' => 'CAT-BAGS', 'description' => 'School bags, lunch bags, water bottles'],
            ['name' => 'Notebooks & Paper', 'code' => 'CAT-NOTEBOOKS', 'description' => 'Class notebooks, drawing books, rough pads'],
            ['name' => 'Textbooks & Curriculum', 'code' => 'CAT-BOOKS', 'description' => 'Standard-wise subject textbooks'],
            ['name' => 'General Stationery', 'code' => 'CAT-STATIONERY', 'description' => 'Pens, pencils, erasers, compass boxes'],
            ['name' => 'Cleaning & Sanitation', 'code' => 'CAT-CLEANING', 'description' => 'Floor cleaners, brooms, mops, dustbins'],
            ['name' => 'Washroom & Hygiene', 'code' => 'CAT-HYGIENE', 'description' => 'Toilet cleaners, hand wash, paper towels'],
            ['name' => 'Maintenance & General', 'code' => 'CAT-MAINT', 'description' => 'Air fresheners, sprays, hardware items'],
        ];

        $categories = [];
        foreach ($categoriesData as $c) {
            $categories[$c['code']] = InventoryCategory::updateOrCreate(['code' => $c['code']], $c);
        }

        // 2. Seed Academic Inventory Items
        $academicItems = [
            [
                'category_id'       => $categories['CAT-NOTEBOOKS']->id,
                'inventory_type'    => 'academic',
                'academic_category' => 'notebooks',
                'name'              => 'Standard Notebook (192 pgs)',
                'item_code'         => 'ACAD-NBK-001',
                'unit'              => 'Piece',
                'unit_price'        => 35.00,
                'purchase_cost'     => 35.00,
                'student_price'      => 50.00,
                'reorder_level'     => 100,
                'current_stock'     => 1000,
                'location'          => 'Rack A1',
                'description'       => '192-page ruled single line notebook',
            ],
            [
                'category_id'       => $categories['CAT-BAGS']->id,
                'inventory_type'    => 'academic',
                'academic_category' => 'school_bag',
                'name'              => 'School Bag (Standard)',
                'item_code'         => 'ACAD-BAG-001',
                'unit'              => 'Piece',
                'unit_price'        => 450.00,
                'purchase_cost'     => 450.00,
                'student_price'      => 650.00,
                'reorder_level'     => 25,
                'current_stock'     => 150,
                'location'          => 'Rack B2',
                'description'       => 'DASA EduGroup branded durable school bag',
            ],
            [
                'category_id'       => $categories['CAT-UNIFORM']->id,
                'inventory_type'    => 'academic',
                'academic_category' => 'uniform',
                'name'              => 'School Uniform Set',
                'item_code'         => 'ACAD-DRS-001',
                'unit'              => 'Set',
                'unit_price'        => 600.00,
                'purchase_cost'     => 600.00,
                'student_price'      => 850.00,
                'reorder_level'     => 40,
                'current_stock'     => 250,
                'location'          => 'Rack C1',
                'description'       => 'Complete uniform dress set with shirt & trousers/skirt',
            ],
            [
                'category_id'       => $categories['CAT-FOOTWEAR']->id,
                'inventory_type'    => 'academic',
                'academic_category' => 'shoes',
                'name'              => 'School Black Shoes',
                'item_code'         => 'ACAD-SHO-001',
                'unit'              => 'Pair',
                'unit_price'        => 350.00,
                'purchase_cost'     => 350.00,
                'student_price'      => 500.00,
                'reorder_level'     => 20,
                'current_stock'     => 120,
                'location'          => 'Rack D1',
                'description'       => 'Formal leather school shoes',
            ],
            [
                'category_id'       => $categories['CAT-FOOTWEAR']->id,
                'inventory_type'    => 'academic',
                'academic_category' => 'socks',
                'name'              => 'School Socks Pair',
                'item_code'         => 'ACAD-SOC-001',
                'unit'              => 'Set',
                'unit_price'        => 40.00,
                'purchase_cost'     => 40.00,
                'student_price'      => 75.00,
                'reorder_level'     => 50,
                'current_stock'     => 300,
                'location'          => 'Rack D2',
                'description'       => 'Cotton school logo socks pair',
            ],
            [
                'category_id'       => $categories['CAT-STATIONERY']->id,
                'inventory_type'    => 'academic',
                'academic_category' => 'stationery',
                'name'              => 'Student Stationery Kit',
                'item_code'         => 'ACAD-STN-001',
                'unit'              => 'Pack',
                'unit_price'        => 60.00,
                'purchase_cost'     => 60.00,
                'student_price'      => 100.00,
                'reorder_level'     => 30,
                'current_stock'     => 400,
                'location'          => 'Rack A2',
                'description'       => 'Includes pens, pencils, eraser, sharpener, and scale',
            ],
            // Textbooks
            [
                'category_id'       => $categories['CAT-BOOKS']->id,
                'inventory_type'    => 'academic',
                'academic_category' => 'textbooks',
                'name'              => 'Tamil Textbook',
                'item_code'         => 'BOOK-TAM-01',
                'unit'              => 'Piece',
                'unit_price'        => 120.00,
                'purchase_cost'     => 120.00,
                'student_price'      => 150.00,
                'reorder_level'     => 30,
                'current_stock'     => 250,
                'location'          => 'Rack E1',
                'description'       => 'State syllabus Tamil language textbook',
            ],
            [
                'category_id'       => $categories['CAT-BOOKS']->id,
                'inventory_type'    => 'academic',
                'academic_category' => 'textbooks',
                'name'              => 'English Reader Book',
                'item_code'         => 'BOOK-ENG-01',
                'unit'              => 'Piece',
                'unit_price'        => 140.00,
                'purchase_cost'     => 140.00,
                'student_price'      => 180.00,
                'reorder_level'     => 30,
                'current_stock'     => 250,
                'location'          => 'Rack E2',
                'description'       => 'English literature reader textbook',
            ],
            [
                'category_id'       => $categories['CAT-BOOKS']->id,
                'inventory_type'    => 'academic',
                'academic_category' => 'textbooks',
                'name'              => 'Mathematics Textbook',
                'item_code'         => 'BOOK-MAT-01',
                'unit'              => 'Piece',
                'unit_price'        => 160.00,
                'purchase_cost'     => 160.00,
                'student_price'      => 200.00,
                'reorder_level'     => 30,
                'current_stock'     => 250,
                'location'          => 'Rack E3',
                'description'       => 'Standard mathematics coursebook',
            ],
            [
                'category_id'       => $categories['CAT-BOOKS']->id,
                'inventory_type'    => 'academic',
                'academic_category' => 'textbooks',
                'name'              => 'General Science Book',
                'item_code'         => 'BOOK-SCI-01',
                'unit'              => 'Piece',
                'unit_price'        => 150.00,
                'purchase_cost'     => 150.00,
                'student_price'      => 190.00,
                'reorder_level'     => 30,
                'current_stock'     => 250,
                'location'          => 'Rack E4',
                'description'       => 'Comprehensive science textbook',
            ],
            [
                'category_id'       => $categories['CAT-BOOKS']->id,
                'inventory_type'    => 'academic',
                'academic_category' => 'textbooks',
                'name'              => 'Social Science Book',
                'item_code'         => 'BOOK-SOC-01',
                'unit'              => 'Piece',
                'unit_price'        => 140.00,
                'purchase_cost'     => 140.00,
                'student_price'      => 180.00,
                'reorder_level'     => 30,
                'current_stock'     => 250,
                'location'          => 'Rack E5',
                'description'       => 'History, Civics and Geography book',
            ],
        ];

        $createdAcademicItems = [];
        foreach ($academicItems as $itemData) {
            $item = InventoryItem::updateOrCreate(['item_code' => $itemData['item_code']], $itemData);
            $createdAcademicItems[$itemData['item_code']] = $item;
        }

        // 3. Seed General Operations Inventory Items
        $opsItems = [
            [
                'category_id'       => $categories['CAT-CLEANING']->id,
                'inventory_type'    => 'general_operations',
                'academic_category' => null,
                'name'              => 'Floor Cleaning Liquid (5 Litre Can)',
                'item_code'         => 'OPS-CLN-001',
                'unit'              => 'Box',
                'unit_price'        => 320.00,
                'purchase_cost'     => 320.00,
                'student_price'      => 0.00,
                'reorder_level'     => 10,
                'current_stock'     => 50,
                'location'          => 'Store Room 1',
                'description'       => 'Disinfectant floor cleaner liquid',
            ],
            [
                'category_id'       => $categories['CAT-CLEANING']->id,
                'inventory_type'    => 'general_operations',
                'academic_category' => null,
                'name'              => 'Classroom Soft Broom',
                'item_code'         => 'OPS-BRM-001',
                'unit'              => 'Piece',
                'unit_price'        => 80.00,
                'purchase_cost'     => 80.00,
                'student_price'      => 0.00,
                'reorder_level'     => 15,
                'current_stock'     => 60,
                'location'          => 'Store Room 1',
                'description'       => 'Long handle indoor floor broom',
            ],
            [
                'category_id'       => $categories['CAT-CLEANING']->id,
                'inventory_type'    => 'general_operations',
                'academic_category' => null,
                'name'              => 'Cotton Floor Mop with Stick',
                'item_code'         => 'OPS-MOP-001',
                'unit'              => 'Piece',
                'unit_price'        => 150.00,
                'purchase_cost'     => 150.00,
                'student_price'      => 0.00,
                'reorder_level'     => 10,
                'current_stock'     => 40,
                'location'          => 'Store Room 1',
                'description'       => 'Heavy duty wet mop for corridors',
            ],
            [
                'category_id'       => $categories['CAT-CLEANING']->id,
                'inventory_type'    => 'general_operations',
                'academic_category' => null,
                'name'              => 'Pedal Dustbin (Heavy Duty)',
                'item_code'         => 'OPS-BIN-001',
                'unit'              => 'Piece',
                'unit_price'        => 250.00,
                'purchase_cost'     => 250.00,
                'student_price'      => 0.00,
                'reorder_level'     => 5,
                'current_stock'     => 30,
                'location'          => 'Store Room 2',
                'description'       => 'Classroom and corridor waste bin',
            ],
            [
                'category_id'       => $categories['CAT-HYGIENE']->id,
                'inventory_type'    => 'general_operations',
                'academic_category' => null,
                'name'              => 'Toilet Cleaning Sanitizer (5L)',
                'item_code'         => 'OPS-TLT-001',
                'unit'              => 'Box',
                'unit_price'        => 280.00,
                'purchase_cost'     => 280.00,
                'student_price'      => 0.00,
                'reorder_level'     => 10,
                'current_stock'     => 45,
                'location'          => 'Store Room 1',
                'description'       => 'Acidic washroom bowl cleaner',
            ],
            [
                'category_id'       => $categories['CAT-HYGIENE']->id,
                'inventory_type'    => 'general_operations',
                'academic_category' => null,
                'name'              => 'Liquid Hand Wash (5L Refill)',
                'item_code'         => 'OPS-HND-001',
                'unit'              => 'Box',
                'unit_price'        => 350.00,
                'purchase_cost'     => 350.00,
                'student_price'      => 0.00,
                'reorder_level'     => 8,
                'current_stock'     => 35,
                'location'          => 'Store Room 1',
                'description'       => 'Antibacterial hand wash dispenser refill',
            ],
            [
                'category_id'       => $categories['CAT-HYGIENE']->id,
                'inventory_type'    => 'general_operations',
                'academic_category' => null,
                'name'              => 'Tissue / Paper Towels (Pack of 100)',
                'item_code'         => 'OPS-PAP-001',
                'unit'              => 'Pack',
                'unit_price'        => 180.00,
                'purchase_cost'     => 180.00,
                'student_price'      => 0.00,
                'reorder_level'     => 20,
                'current_stock'     => 80,
                'location'          => 'Store Room 2',
                'description'       => 'Multi-fold paper hand towels',
            ],
            [
                'category_id'       => $categories['CAT-MAINT']->id,
                'inventory_type'    => 'general_operations',
                'academic_category' => null,
                'name'              => 'Classroom Air Freshener Spray',
                'item_code'         => 'OPS-AIR-001',
                'unit'              => 'Piece',
                'unit_price'        => 220.00,
                'purchase_cost'     => 220.00,
                'student_price'      => 0.00,
                'reorder_level'     => 10,
                'current_stock'     => 40,
                'location'          => 'Store Room 2',
                'description'       => 'Automatic spray air freshener refill',
            ],
        ];

        foreach ($opsItems as $itemData) {
            InventoryItem::updateOrCreate(['item_code' => $itemData['item_code']], $itemData);
        }

        // 4. Seed Standard-wise Admission Kit Mapping for ALL Classes
        $classes = Classes::active()->get();
        $academicYear = AcademicYear::current();

        $kitDefaults = [
            ['code' => 'ACAD-NBK-001', 'qty' => 10, 'unit' => 'Piece', 'allow_add' => true,  'charge' => 50.00],
            ['code' => 'ACAD-BAG-001', 'qty' => 1,  'unit' => 'Piece', 'allow_add' => false, 'charge' => 0.00],
            ['code' => 'ACAD-DRS-001', 'qty' => 2,  'unit' => 'Set',   'allow_add' => true,  'charge' => 850.00],
            ['code' => 'ACAD-SHO-001', 'qty' => 1,  'unit' => 'Pair',  'allow_add' => false, 'charge' => 0.00],
            ['code' => 'ACAD-SOC-001', 'qty' => 1,  'unit' => 'Set',   'allow_add' => true,  'charge' => 75.00],
            // Textbooks
            ['code' => 'BOOK-TAM-01', 'qty' => 1,  'unit' => 'Piece', 'allow_add' => false, 'charge' => 0.00],
            ['code' => 'BOOK-ENG-01', 'qty' => 1,  'unit' => 'Piece', 'allow_add' => false, 'charge' => 0.00],
            ['code' => 'BOOK-MAT-01', 'qty' => 1,  'unit' => 'Piece', 'allow_add' => false, 'charge' => 0.00],
            ['code' => 'BOOK-SCI-01', 'qty' => 1,  'unit' => 'Piece', 'allow_add' => false, 'charge' => 0.00],
            ['code' => 'BOOK-SOC-01', 'qty' => 1,  'unit' => 'Piece', 'allow_add' => false, 'charge' => 0.00],
        ];

        foreach ($classes as $cls) {
            foreach ($kitDefaults as $kd) {
                $item = $createdAcademicItems[$kd['code']] ?? null;
                if ($item) {
                    AdmissionKitConfig::updateOrCreate([
                        'class_id'         => $cls->id,
                        'item_id'          => $item->id,
                        'academic_year_id' => $academicYear?->id,
                    ], [
                        'default_quantity'     => $kd['qty'],
                        'unit'                 => $kd['unit'],
                        'is_default_included'  => true,
                        'allow_additional_qty' => $kd['allow_add'],
                        'student_charge'       => $kd['charge'],
                    ]);
                }
            }
        }
    }
}
