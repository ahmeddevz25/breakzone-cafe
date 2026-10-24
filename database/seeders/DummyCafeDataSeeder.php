<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DummyCafeDataSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // 1. Cafe Setting
        DB::table('cafe_settings')->updateOrInsert(
            ['id' => 1],
            [
                'cafe_name' => 'BreakZone Cafe',
                'cafe_address' => 'Learning Alliance DHA Campus, Sector W, Phase 8, Lahore',
                'phone_number' => '+92 42 35773141',
                'logo' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        // 2. Stores / Outlets
        $stores = [
            [
                'id' => 1,
                'company_id' => 1,
                'store' => 'BreakZone Cafe - DHA Campus',
                'printer_ip_address' => '192.168.1.101',
                'printer_port' => '9100',
                'opening_balance' => 15000,
                'status' => 'A',
                'position' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'company_id' => 1,
                'store' => 'BreakZone Cafe - Aziz Avenue',
                'printer_ip_address' => '192.168.1.102',
                'printer_port' => '9100',
                'opening_balance' => 10000,
                'status' => 'A',
                'position' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'company_id' => 1,
                'store' => 'BreakZone Cafe - Gulberg Campus',
                'printer_ip_address' => '192.168.1.103',
                'printer_port' => '9100',
                'opening_balance' => 10000,
                'status' => 'A',
                'position' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        foreach ($stores as $s) {
            DB::table('cafe_stores')->updateOrInsert(['id' => $s['id']], $s);
        }

        // 3. Suppliers
        $suppliers = [
            [
                'id' => 1,
                'name' => 'Ejaz Traders',
                'company' => 'Ejaz Beverages & Confectionery Distributors',
                'address' => 'Shop 14, Main Market, Gulberg II, Lahore',
                'mobile' => '+92 300 8452119',
                'email' => 'ejaz.traders@gmail.com',
                'ntn_no' => '3829104-7',
                'status' => 'A',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'name' => 'Nestle Pakistan Official Distribution',
                'company' => 'Nestle Pakistan Ltd',
                'address' => '308 Upper Mall, Lahore',
                'mobile' => '+92 42 111 637 853',
                'email' => 'orders@nestle.com.pk',
                'ntn_no' => '0801452-3',
                'status' => 'A',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'name' => 'Al-Madina Meat & Poultry',
                'company' => 'Al-Madina Fresh Meat Suppliers',
                'address' => 'Tollinton Market, Jail Road, Lahore',
                'mobile' => '+92 321 4455889',
                'email' => 'almadina.poultry@gmail.com',
                'ntn_no' => '1948201-5',
                'status' => 'A',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'name' => 'Dawn Bread & Bakers',
                'company' => 'Golden Harvest Foods (Pvt) Ltd',
                'address' => 'Industrial Estate, Kot Lakhpat, Lahore',
                'mobile' => '+92 42 35824551',
                'email' => 'supply@dawnbread.com',
                'ntn_no' => '0711829-1',
                'status' => 'A',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'name' => 'Gourmet Dairy & Groceries',
                'company' => 'Gourmet Foods Distribution',
                'address' => 'Ferozepur Road, Lahore',
                'mobile' => '+92 333 4901234',
                'email' => 'dairy@gourmetfoods.com.pk',
                'ntn_no' => '2291045-8',
                'status' => 'A',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        foreach ($suppliers as $sup) {
            DB::table('cafe_suppliers')->updateOrInsert(['id' => $sup['id']], $sup);
        }

        // 4. Brands
        $brands = [
            ['id' => 1, 'name' => 'Nestle', 'position' => 1, 'status' => 'A', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'name' => 'PepsiCo', 'position' => 2, 'status' => 'A', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'name' => 'Coca-Cola', 'position' => 3, 'status' => 'A', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'name' => 'Dawn', 'position' => 4, 'status' => 'A', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'name' => 'Lays', 'position' => 5, 'status' => 'A', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'name' => 'In-House Kitchen', 'position' => 6, 'status' => 'A', 'created_at' => $now, 'updated_at' => $now],
        ];
        foreach ($brands as $b) {
            DB::table('cafe_brands')->updateOrInsert(['id' => $b['id']], $b);
        }

        // 5. Categories
        $categories = [
            ['id' => 1, 'parent_id' => null, 'category' => 'Cold Beverages', 'position' => 1, 'status' => 'A', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'parent_id' => null, 'category' => 'Hot Beverages', 'position' => 2, 'status' => 'A', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'parent_id' => null, 'category' => 'Fast Food & Burgers', 'position' => 3, 'status' => 'A', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'parent_id' => null, 'category' => 'Pizzas & Baked Items', 'position' => 4, 'status' => 'A', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'parent_id' => null, 'category' => 'Snacks & Confectionery', 'position' => 5, 'status' => 'A', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'parent_id' => null, 'category' => 'Raw Kitchen Ingredients', 'position' => 6, 'status' => 'A', 'created_at' => $now, 'updated_at' => $now],
        ];
        foreach ($categories as $c) {
            DB::table('cafe_categories')->updateOrInsert(['id' => $c['id']], $c);
        }

        // 6. Units
        $units = [
            ['id' => 1, 'unit' => 'Pcs', 'quantity' => 1, 'position' => 1, 'status' => 'A', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'unit' => 'Box', 'quantity' => 24, 'position' => 2, 'status' => 'A', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'unit' => 'Pack', 'quantity' => 1, 'position' => 3, 'status' => 'A', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'unit' => 'Kg', 'quantity' => 1, 'position' => 4, 'status' => 'A', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'unit' => 'Gram', 'quantity' => 0.001, 'position' => 5, 'status' => 'A', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'unit' => 'Liter', 'quantity' => 1, 'position' => 6, 'status' => 'A', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'unit' => 'Ml', 'quantity' => 0.001, 'position' => 7, 'status' => 'A', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 8, 'unit' => 'Serving', 'quantity' => 1, 'position' => 8, 'status' => 'A', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 9, 'unit' => 'Slice', 'quantity' => 1, 'position' => 9, 'status' => 'A', 'created_at' => $now, 'updated_at' => $now],
        ];
        foreach ($units as $u) {
            DB::table('cafe_units')->updateOrInsert(['id' => $u['id']], $u);
        }

        // 7. Ingredients (Raw materials for kitchen & coffee counter)
        $ingredients = [
            [
                'id' => 1,
                'store_id' => 1,
                'name' => 'Chicken Breast Fillet (Boneless)',
                'buying_unit_id' => 4, // Kg
                'usage_unit_id' => 5,  // Gram
                'conversion_value' => 1000,
                'purchase_price' => 950.00,
                'stock' => 25.00,
                'supplier_id' => 3, // Al-Madina
                'details' => 'Fresh skinless boneless chicken breast for burgers and sandwiches',
                'picture' => null,
                'status' => 'A',
                'created_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'store_id' => 1,
                'name' => 'Burger Buns (Jumbo Sesame 6-Pack)',
                'buying_unit_id' => 3, // Pack
                'usage_unit_id' => 1,  // Pcs
                'conversion_value' => 6,
                'purchase_price' => 180.00,
                'stock' => 40.00,
                'supplier_id' => 4, // Dawn
                'details' => 'Freshly baked sesame seed jumbo buns',
                'picture' => null,
                'status' => 'A',
                'created_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'store_id' => 1,
                'name' => 'Full Cream Fresh Milk',
                'buying_unit_id' => 6, // Liter
                'usage_unit_id' => 7,  // Ml
                'conversion_value' => 1000,
                'purchase_price' => 240.00,
                'stock' => 35.00,
                'supplier_id' => 5, // Gourmet Dairy
                'details' => 'Standardized pasteurized full cream milk for coffee & tea',
                'picture' => null,
                'status' => 'A',
                'created_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'store_id' => 1,
                'name' => 'Espresso Roast Coffee Beans',
                'buying_unit_id' => 4, // Kg
                'usage_unit_id' => 5,  // Gram
                'conversion_value' => 1000,
                'purchase_price' => 3800.00,
                'stock' => 12.00,
                'supplier_id' => 2, // Nestle
                'details' => 'Premium dark roast Arabica espresso beans',
                'picture' => null,
                'status' => 'A',
                'created_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'store_id' => 1,
                'name' => 'Cheddar Cheese Slices (12-Pack)',
                'buying_unit_id' => 3, // Pack
                'usage_unit_id' => 9,  // Slice
                'conversion_value' => 12,
                'purchase_price' => 650.00,
                'stock' => 20.00,
                'supplier_id' => 5, // Gourmet Dairy
                'details' => 'Individually wrapped natural cheddar cheese slices',
                'picture' => null,
                'status' => 'A',
                'created_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 6,
                'store_id' => 1,
                'name' => 'French Fries (Imported 9mm Skinny)',
                'buying_unit_id' => 4, // Kg
                'usage_unit_id' => 5,  // Gram
                'conversion_value' => 1000,
                'purchase_price' => 800.00,
                'stock' => 30.00,
                'supplier_id' => 1, // Ejaz Traders
                'details' => 'Crispy frozen pre-cut french fries 2.5kg bags',
                'picture' => null,
                'status' => 'A',
                'created_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 7,
                'store_id' => 1,
                'name' => 'Cooking Oil (Canola)',
                'buying_unit_id' => 6, // Liter
                'usage_unit_id' => 7,  // Ml
                'conversion_value' => 1000,
                'purchase_price' => 520.00,
                'stock' => 25.00,
                'supplier_id' => 1, // Ejaz Traders
                'details' => 'Pure canola oil for deep fryers',
                'picture' => null,
                'status' => 'A',
                'created_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 8,
                'store_id' => 1,
                'name' => 'Pizza Mozzarella Cheese (Block)',
                'buying_unit_id' => 4, // Kg
                'usage_unit_id' => 5,  // Gram
                'conversion_value' => 1000,
                'purchase_price' => 2100.00,
                'stock' => 10.00,
                'supplier_id' => 5, // Gourmet Dairy
                'details' => '100% pure shredded mozzarella with high stretch',
                'picture' => null,
                'status' => 'A',
                'created_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 9,
                'store_id' => 1,
                'name' => 'Pizza Dough Base (8 inch Small)',
                'buying_unit_id' => 1, // Pcs
                'usage_unit_id' => 1,  // Pcs
                'conversion_value' => 1,
                'purchase_price' => 90.00,
                'stock' => 50.00,
                'supplier_id' => 4, // Dawn
                'details' => 'Pre-baked 8 inch personal pan pizza crusts',
                'picture' => null,
                'status' => 'A',
                'created_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        foreach ($ingredients as $ing) {
            DB::table('cafe_ingredients')->updateOrInsert(['id' => $ing['id']], $ing);
        }

        // 8. Retail Packaged Items (Ready to sell products)
        $items = [
            [
                'id' => 1,
                'store_id' => 1,
                'name' => 'Nescafe Mocha 220ml Can',
                'code' => '02090005',
                'category_id' => 1, // Cold Beverages
                'unit_id' => 1, // Pcs
                'brand_id' => 1, // Nestle
                'supplier_id' => 2, // Nestle
                'stock' => 85.00,
                'price' => 144.00,
                'sale_price' => 200.00,
                'details' => 'Chilled canned mocha ready to drink',
                'picture' => null,
                'status' => 'A',
                'position' => 1,
                'created_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'store_id' => 1,
                'name' => 'Nescafe Cappuccino 220ml Can',
                'code' => '02090001',
                'category_id' => 1,
                'unit_id' => 1,
                'brand_id' => 1,
                'supplier_id' => 2,
                'stock' => 60.00,
                'price' => 144.00,
                'sale_price' => 200.00,
                'details' => 'Chilled canned cappuccino ready to drink',
                'picture' => null,
                'status' => 'A',
                'position' => 2,
                'created_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'store_id' => 1,
                'name' => 'Pepsi 345ml Bottle',
                'code' => '01010001',
                'category_id' => 1,
                'unit_id' => 1,
                'brand_id' => 2, // PepsiCo
                'supplier_id' => 1, // Ejaz Traders
                'stock' => 120.00,
                'price' => 75.00,
                'sale_price' => 100.00,
                'details' => 'Cold carbonated soft drink 345ml pet bottle',
                'picture' => null,
                'status' => 'A',
                'position' => 3,
                'created_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'store_id' => 1,
                'name' => '7Up 345ml Bottle',
                'code' => '01010002',
                'category_id' => 1,
                'unit_id' => 1,
                'brand_id' => 2,
                'supplier_id' => 1,
                'stock' => 90.00,
                'price' => 75.00,
                'sale_price' => 100.00,
                'details' => 'Clear lemon lime carbonated beverage',
                'picture' => null,
                'status' => 'A',
                'position' => 4,
                'created_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'store_id' => 1,
                'name' => 'Aquafina Mineral Water 500ml',
                'code' => '01010005',
                'category_id' => 1,
                'unit_id' => 1,
                'brand_id' => 2,
                'supplier_id' => 1,
                'stock' => 150.00,
                'price' => 45.00,
                'sale_price' => 60.00,
                'details' => 'Purified drinking water with minerals',
                'picture' => null,
                'status' => 'A',
                'position' => 5,
                'created_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 6,
                'store_id' => 1,
                'name' => 'Lays Wavy Mexican Chili 40g',
                'code' => '03020001',
                'category_id' => 5, // Snacks
                'unit_id' => 1,
                'brand_id' => 5, // Lays
                'supplier_id' => 1,
                'stock' => 110.00,
                'price' => 75.00,
                'sale_price' => 100.00,
                'details' => 'Crunchy potato chips spicy Mexican chili flavor',
                'picture' => null,
                'status' => 'A',
                'position' => 6,
                'created_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 7,
                'store_id' => 1,
                'name' => 'Lays French Cheese 40g',
                'code' => '03020002',
                'category_id' => 5,
                'unit_id' => 1,
                'brand_id' => 5,
                'supplier_id' => 1,
                'stock' => 95.00,
                'price' => 75.00,
                'sale_price' => 100.00,
                'details' => 'Classic french cheese flavored potato chips',
                'picture' => null,
                'status' => 'A',
                'position' => 7,
                'created_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 8,
                'store_id' => 1,
                'name' => 'Nestle Milo 180ml Pack',
                'code' => '02090010',
                'category_id' => 1,
                'unit_id' => 1,
                'brand_id' => 1,
                'supplier_id' => 2,
                'stock' => 70.00,
                'price' => 90.00,
                'sale_price' => 120.00,
                'details' => 'Chocolate malt flavored energy drink pack',
                'picture' => null,
                'status' => 'A',
                'position' => 8,
                'created_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        foreach ($items as $it) {
            DB::table('cafe_items')->updateOrInsert(['id' => $it['id']], $it);
        }

        // 9. Prepared Foods (Cooked cafe recipes)
        $foods = [
            [
                'id' => 1,
                'store_id' => 1,
                'category_id' => 3, // Fast Food
                'name' => 'Crispy Zinger Burger',
                'code' => 'FD-001',
                'price' => 480.00,
                'cost_price' => 252.00,
                'stock' => 50.00,
                'picture' => null,
                'status' => 'A',
                'position' => 1,
                'created_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'store_id' => 1,
                'category_id' => 3,
                'name' => 'Club Sandwich with Fries',
                'code' => 'FD-002',
                'price' => 420.00,
                'cost_price' => 213.00,
                'stock' => 40.00,
                'picture' => null,
                'status' => 'A',
                'position' => 2,
                'created_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'store_id' => 1,
                'category_id' => 2, // Hot Beverages
                'name' => 'Hot Cappuccino (Regular)',
                'code' => 'FD-003',
                'price' => 280.00,
                'cost_price' => 111.00,
                'stock' => 100.00,
                'picture' => null,
                'status' => 'A',
                'position' => 3,
                'created_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'store_id' => 1,
                'category_id' => 4, // Pizzas
                'name' => 'Chicken Tikka Pizza (8 inch)',
                'code' => 'FD-004',
                'price' => 750.00,
                'cost_price' => 376.00,
                'stock' => 30.00,
                'picture' => null,
                'status' => 'A',
                'position' => 4,
                'created_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'store_id' => 1,
                'category_id' => 3,
                'name' => 'Loaded Cheese Fries',
                'code' => 'FD-005',
                'price' => 350.00,
                'cost_price' => 185.00,
                'stock' => 60.00,
                'picture' => null,
                'status' => 'A',
                'position' => 5,
                'created_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        foreach ($foods as $f) {
            DB::table('cafe_foods')->updateOrInsert(['id' => $f['id']], $f);
        }

        // 10. Food Details (Recipe mapping of ingredients used)
        $foodDetails = [
            // Crispy Zinger Burger (food_id: 1)
            ['id' => 1, 'food_id' => 1, 'ingredient_id' => 1, 'usage_unit_id' => 5, 'quantity' => 150.00, 'unit_price' => 0.95, 'total_price' => 142.50, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'food_id' => 1, 'ingredient_id' => 2, 'usage_unit_id' => 1, 'quantity' => 1.00, 'unit_price' => 30.00, 'total_price' => 30.00, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'food_id' => 1, 'ingredient_id' => 5, 'usage_unit_id' => 9, 'quantity' => 1.00, 'unit_price' => 54.17, 'total_price' => 54.17, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'food_id' => 1, 'ingredient_id' => 7, 'usage_unit_id' => 7, 'quantity' => 50.00, 'unit_price' => 0.52, 'total_price' => 26.00, 'created_at' => $now, 'updated_at' => $now],

            // Club Sandwich with Fries (food_id: 2)
            ['id' => 5, 'food_id' => 2, 'ingredient_id' => 1, 'usage_unit_id' => 5, 'quantity' => 100.00, 'unit_price' => 0.95, 'total_price' => 95.00, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'food_id' => 2, 'ingredient_id' => 5, 'usage_unit_id' => 9, 'quantity' => 1.00, 'unit_price' => 54.17, 'total_price' => 54.17, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'food_id' => 2, 'ingredient_id' => 6, 'usage_unit_id' => 5, 'quantity' => 80.00, 'unit_price' => 0.80, 'total_price' => 64.00, 'created_at' => $now, 'updated_at' => $now],

            // Hot Cappuccino (food_id: 3)
            ['id' => 8, 'food_id' => 3, 'ingredient_id' => 4, 'usage_unit_id' => 5, 'quantity' => 18.00, 'unit_price' => 3.80, 'total_price' => 68.40, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 9, 'food_id' => 3, 'ingredient_id' => 3, 'usage_unit_id' => 7, 'quantity' => 180.00, 'unit_price' => 0.24, 'total_price' => 43.20, 'created_at' => $now, 'updated_at' => $now],

            // Pizza (food_id: 4)
            ['id' => 10, 'food_id' => 4, 'ingredient_id' => 9, 'usage_unit_id' => 1, 'quantity' => 1.00, 'unit_price' => 90.00, 'total_price' => 90.00, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 11, 'food_id' => 4, 'ingredient_id' => 8, 'usage_unit_id' => 5, 'quantity' => 100.00, 'unit_price' => 2.10, 'total_price' => 210.00, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 12, 'food_id' => 4, 'ingredient_id' => 1, 'usage_unit_id' => 5, 'quantity' => 80.00, 'unit_price' => 0.95, 'total_price' => 76.00, 'created_at' => $now, 'updated_at' => $now],

            // Loaded Fries (food_id: 5)
            ['id' => 13, 'food_id' => 5, 'ingredient_id' => 6, 'usage_unit_id' => 5, 'quantity' => 180.00, 'unit_price' => 0.80, 'total_price' => 144.00, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 14, 'food_id' => 5, 'ingredient_id' => 7, 'usage_unit_id' => 7, 'quantity' => 40.00, 'unit_price' => 0.52, 'total_price' => 20.80, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 15, 'food_id' => 5, 'ingredient_id' => 5, 'usage_unit_id' => 9, 'quantity' => 1.00, 'unit_price' => 54.17, 'total_price' => 54.17, 'created_at' => $now, 'updated_at' => $now],
        ];
        foreach ($foodDetails as $fd) {
            DB::table('cafe_food_details')->updateOrInsert(['id' => $fd['id']], $fd);
        }

        // 11. Combo Deals
        $combos = [
            [
                'id' => 1,
                'store_id' => 1,
                'name' => 'Student Deal 1 - Zinger Feast',
                'code' => 'DEAL-01',
                'price' => 500.00, // Burger (480) + Pepsi (100) = 580 -> Deal: 500
                'picture' => null,
                'status' => 'A',
                'position' => 1,
                'created_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'store_id' => 1,
                'name' => 'Breakfast Duo - Coffee & Sandwich',
                'code' => 'DEAL-02',
                'price' => 600.00, // Sandwich (420) + Cappuccino (280) = 700 -> Deal: 600
                'picture' => null,
                'status' => 'A',
                'position' => 2,
                'created_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'store_id' => 1,
                'name' => 'Pizza Buddy Deal',
                'code' => 'DEAL-03',
                'price' => 800.00, // Pizza (750) + 7Up (100) = 850 -> Deal: 800
                'picture' => null,
                'status' => 'A',
                'position' => 3,
                'created_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];
        foreach ($combos as $c) {
            DB::table('cafe_combo_products')->updateOrInsert(['id' => $c['id']], $c);
        }

        $comboDetails = [
            // Deal 1: Zinger Burger (food_id: 1) + Pepsi (item_id: 3)
            ['id' => 1, 'combo_product_id' => 1, 'food_id' => 1, 'item_id' => null, 'quantity' => 1, 'price' => 480.00, 'discount' => 50.00, 'total_price' => 430.00, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'combo_product_id' => 1, 'food_id' => null, 'item_id' => 3, 'quantity' => 1, 'price' => 100.00, 'discount' => 30.00, 'total_price' => 70.00, 'created_at' => $now, 'updated_at' => $now],

            // Deal 2: Club Sandwich (food_id: 2) + Hot Cappuccino (food_id: 3)
            ['id' => 3, 'combo_product_id' => 2, 'food_id' => 2, 'item_id' => null, 'quantity' => 1, 'price' => 420.00, 'discount' => 60.00, 'total_price' => 360.00, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'combo_product_id' => 2, 'food_id' => 3, 'item_id' => null, 'quantity' => 1, 'price' => 280.00, 'discount' => 40.00, 'total_price' => 240.00, 'created_at' => $now, 'updated_at' => $now],

            // Deal 3: Pizza (food_id: 4) + 7Up (item_id: 4)
            ['id' => 5, 'combo_product_id' => 3, 'food_id' => 4, 'item_id' => null, 'quantity' => 1, 'price' => 750.00, 'discount' => 30.00, 'total_price' => 720.00, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'combo_product_id' => 3, 'food_id' => null, 'item_id' => 4, 'quantity' => 1, 'price' => 100.00, 'discount' => 20.00, 'total_price' => 80.00, 'created_at' => $now, 'updated_at' => $now],
        ];
        foreach ($comboDetails as $cd) {
            DB::table('cafe_combo_product_details')->updateOrInsert(['id' => $cd['id']], $cd);
        }

        // 12. Purchases / Stock-In POs
        $purchases = [
            [
                'id' => 1,
                'store_id' => 1,
                'supplier_id' => 1, // Ejaz Traders
                'po_no' => 'PO-20261015-0001',
                'purchase_date' => '2026-10-15',
                'school_id' => 8, // LA Junior DHA
                'total' => 48000.00,
                'notes' => 'Weekly soft drinks and french fries bulk delivery',
                'created_by' => 1,
                'created_at' => $now->copy()->subDays(8),
                'updated_at' => $now->copy()->subDays(8),
            ],
            [
                'id' => 2,
                'store_id' => 1,
                'supplier_id' => 2, // Nestle
                'po_no' => 'PO-20261018-0002',
                'purchase_date' => '2026-10-18',
                'school_id' => 8,
                'total' => 72780.00,
                'notes' => 'Nestle direct dispatch: Canned coffees, Milo and coffee beans',
                'created_by' => 1,
                'created_at' => $now->copy()->subDays(5),
                'updated_at' => $now->copy()->subDays(5),
            ],
            [
                'id' => 3,
                'store_id' => 1,
                'supplier_id' => 3, // Al-Madina
                'po_no' => 'PO-20261021-0003',
                'purchase_date' => '2026-10-21',
                'school_id' => 8,
                'total' => 23750.00,
                'notes' => 'Fresh boneless chicken fillets supply',
                'created_by' => 1,
                'created_at' => $now->copy()->subDays(2),
                'updated_at' => $now->copy()->subDays(2),
            ],
            [
                'id' => 4,
                'store_id' => 1,
                'supplier_id' => 4, // Dawn
                'po_no' => 'PO-20261022-0004',
                'purchase_date' => '2026-10-22',
                'school_id' => 8,
                'total' => 11700.00,
                'notes' => 'Fresh bakery delivery - jumbo buns and small pizza bases',
                'created_by' => 1,
                'created_at' => $now->copy()->subDays(1),
                'updated_at' => $now->copy()->subDays(1),
            ],
        ];
        foreach ($purchases as $p) {
            DB::table('cafe_purchases')->updateOrInsert(['id' => $p['id']], $p);
        }

        $purchaseDetails = [
            // Purchase 1 (Ejaz Traders) - Total 48,000
            ['id' => 1, 'purchase_id' => 1, 'item_id' => 3, 'ingredient_id' => null, 'food_id' => null, 'unit_id' => 1, 'quantity' => 120.00, 'price' => 75.00, 'total_price' => 9000.00, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'purchase_id' => 1, 'item_id' => 4, 'ingredient_id' => null, 'food_id' => null, 'unit_id' => 1, 'quantity' => 90.00, 'price' => 75.00, 'total_price' => 6750.00, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'purchase_id' => 1, 'item_id' => 6, 'ingredient_id' => null, 'food_id' => null, 'unit_id' => 1, 'quantity' => 110.00, 'price' => 75.00, 'total_price' => 8250.00, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'purchase_id' => 1, 'item_id' => null, 'ingredient_id' => 6, 'food_id' => null, 'unit_id' => 4, 'quantity' => 30.00, 'price' => 800.00, 'total_price' => 24000.00, 'created_at' => $now, 'updated_at' => $now],

            // Purchase 2 (Nestle) - Total 72,780
            ['id' => 5, 'purchase_id' => 2, 'item_id' => 1, 'ingredient_id' => null, 'food_id' => null, 'unit_id' => 1, 'quantity' => 85.00, 'price' => 144.00, 'total_price' => 12240.00, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'purchase_id' => 2, 'item_id' => 2, 'ingredient_id' => null, 'food_id' => null, 'unit_id' => 1, 'quantity' => 60.00, 'price' => 144.00, 'total_price' => 8640.00, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'purchase_id' => 2, 'item_id' => null, 'ingredient_id' => 4, 'food_id' => null, 'unit_id' => 4, 'quantity' => 12.00, 'price' => 3800.00, 'total_price' => 45600.00, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 8, 'purchase_id' => 2, 'item_id' => 8, 'ingredient_id' => null, 'food_id' => null, 'unit_id' => 1, 'quantity' => 70.00, 'price' => 90.00, 'total_price' => 6300.00, 'created_at' => $now, 'updated_at' => $now],

            // Purchase 3 (Al-Madina) - Total 23,750
            ['id' => 9, 'purchase_id' => 3, 'item_id' => null, 'ingredient_id' => 1, 'food_id' => null, 'unit_id' => 4, 'quantity' => 25.00, 'price' => 950.00, 'total_price' => 23750.00, 'created_at' => $now, 'updated_at' => $now],

            // Purchase 4 (Dawn) - Total 11,700
            ['id' => 10, 'purchase_id' => 4, 'item_id' => null, 'ingredient_id' => 2, 'food_id' => null, 'unit_id' => 3, 'quantity' => 40.00, 'price' => 180.00, 'total_price' => 7200.00, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 11, 'purchase_id' => 4, 'item_id' => null, 'ingredient_id' => 9, 'food_id' => null, 'unit_id' => 1, 'quantity' => 50.00, 'price' => 90.00, 'total_price' => 4500.00, 'created_at' => $now, 'updated_at' => $now],
        ];
        foreach ($purchaseDetails as $pd) {
            DB::table('cafe_purchase_details')->updateOrInsert(['id' => $pd['id']], $pd);
        }

        // 13. Synchronize Store Quantities (Store 1)
        // Sync Items
        foreach ($items as $it) {
            DB::table('cafe_quantities')->updateOrInsert(
                ['store_id' => 1, 'item_id' => $it['id']],
                ['quantity' => $it['stock'], 'updated_at' => $now]
            );
        }
        // Sync Ingredients
        foreach ($ingredients as $ing) {
            DB::table('cafe_quantities')->updateOrInsert(
                ['store_id' => 1, 'ingredient_id' => $ing['id']],
                ['quantity' => $ing['stock'], 'updated_at' => $now]
            );
        }
        // Sync Foods
        foreach ($foods as $fd) {
            DB::table('cafe_quantities')->updateOrInsert(
                ['store_id' => 1, 'food_id' => $fd['id']],
                ['quantity' => $fd['stock'], 'updated_at' => $now]
            );
        }

        // Fix PostgreSQL auto-increment sequences for all tables
        $tablesWithId = [
            'cafe_settings', 'cafe_stores', 'cafe_suppliers', 'cafe_brands',
            'cafe_categories', 'cafe_units', 'cafe_items', 'cafe_ingredients',
            'cafe_foods', 'cafe_food_details', 'cafe_combo_products',
            'cafe_combo_product_details', 'cafe_purchases', 'cafe_purchase_details',
            'cafe_quantities'
        ];
        foreach ($tablesWithId as $t) {
            try {
                DB::statement("SELECT setval(pg_get_serial_sequence('$t', 'id'), coalesce(max(id), 0) + 1, false) FROM $t");
            } catch (\Exception $e) {
                // Ignore if table has no serial sequence
            }
        }
    }
}
