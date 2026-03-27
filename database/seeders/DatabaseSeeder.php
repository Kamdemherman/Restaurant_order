<?php
// FILE: database/seeders/DatabaseSeeder.php
namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\PrinterConfig;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin user ────────────────────────────────────────────
        User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@restaurant.com')],
            [
                'name'            => 'Restaurant Admin',
                'password'        => Hash::make('Admin@12345!'),
                'role'            => 'admin',
                'status'          => 'approved',
                'gdpr_consent'    => true,
                'gdpr_consent_at' => now(),
            ]
        );

        // ── Demo customer ─────────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'customer@demo.com'],
            [
                'name'            => 'Demo Customer',
                'password'        => Hash::make('Customer@123'),
                'role'            => 'customer',
                'status'          => 'approved',
                'phone'           => '+353 87 000 0001',
                'gdpr_consent'    => true,
                'gdpr_consent_at' => now(),
            ]
        );

        // ── Categories ────────────────────────────────────────────
        $starters = Category::firstOrCreate(
            ['slug' => 'starters'],
            ['name' => 'Starters', 'sort_order' => 1, 'is_active' => true,
             'description' => 'Light bites to begin your meal']
        );
        $mains = Category::firstOrCreate(
            ['slug' => 'main-courses'],
            ['name' => 'Main Courses', 'sort_order' => 2, 'is_active' => true,
             'description' => 'Hearty and delicious main dishes']
        );
        $drinks = Category::firstOrCreate(
            ['slug' => 'drinks'],
            ['name' => 'Drinks', 'sort_order' => 3, 'is_active' => true,
             'description' => 'Refreshing beverages']
        );
        $desserts = Category::firstOrCreate(
            ['slug' => 'desserts'],
            ['name' => 'Desserts', 'sort_order' => 4, 'is_active' => true,
             'description' => 'Sweet treats to finish']
        );

        // ── Menu Items ────────────────────────────────────────────
        $items = [
            ['category' => $starters, 'name' => 'Soup of the Day',   'price' => 6.50,  'featured' => false,
             'desc' => 'Freshly made daily, served with crusty bread', 'allergens' => ['Gluten','Dairy']],
            ['category' => $starters, 'name' => 'Garlic Bruschetta', 'price' => 7.50,  'featured' => false,
             'desc' => 'Toasted sourdough with roasted garlic and cherry tomatoes', 'allergens' => ['Gluten']],
            ['category' => $starters, 'name' => 'Chicken Wings',     'price' => 9.95,  'featured' => true,
             'desc' => 'Crispy wings tossed in buffalo sauce, served with blue cheese dip', 'allergens' => ['Dairy']],

            ['category' => $mains, 'name' => '8oz Ribeye Steak',    'price' => 28.95, 'featured' => true,
             'desc' => 'Prime Irish beef, served with fries and peppercorn sauce', 'allergens' => ['Dairy']],
            ['category' => $mains, 'name' => 'Atlantic Salmon',      'price' => 22.95, 'featured' => false,
             'desc' => 'Pan-seared fillet with lemon butter and seasonal vegetables', 'allergens' => ['Fish','Dairy']],
            ['category' => $mains, 'name' => 'Mushroom Risotto',     'price' => 17.95, 'featured' => false,
             'desc' => 'Arborio rice with wild mushrooms, parmesan and truffle oil', 'allergens' => ['Dairy']],
            ['category' => $mains, 'name' => 'Classic Burger',       'price' => 15.95, 'featured' => true,
             'desc' => '6oz beef patty, lettuce, tomato, pickles, burger sauce', 'allergens' => ['Gluten','Dairy','Eggs']],

            ['category' => $drinks, 'name' => 'Still Water (500ml)', 'price' => 2.50,  'featured' => false, 'desc' => '', 'allergens' => []],
            ['category' => $drinks, 'name' => 'Sparkling Water',     'price' => 2.50,  'featured' => false, 'desc' => '', 'allergens' => []],
            ['category' => $drinks, 'name' => 'Fresh Orange Juice',  'price' => 4.50,  'featured' => false, 'desc' => 'Freshly squeezed', 'allergens' => []],
            ['category' => $drinks, 'name' => 'House Lemonade',      'price' => 4.00,  'featured' => false, 'desc' => 'Homemade with fresh lemons', 'allergens' => []],

            ['category' => $desserts, 'name' => 'Chocolate Lava Cake', 'price' => 8.50, 'featured' => true,
             'desc' => 'Warm chocolate cake with a molten centre, vanilla ice cream', 'allergens' => ['Gluten','Dairy','Eggs']],
            ['category' => $desserts, 'name' => 'Cheesecake',           'price' => 7.95, 'featured' => false,
             'desc' => 'New York style with berry coulis', 'allergens' => ['Gluten','Dairy','Eggs']],
        ];

        foreach ($items as $itemData) {
            $item = MenuItem::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($itemData['name'])],
                [
                    'category_id'      => $itemData['category']->id,
                    'name'             => $itemData['name'],
                    'description'      => $itemData['desc'],
                    'price'            => $itemData['price'],
                    'is_available'     => true,
                    'is_featured'      => $itemData['featured'],
                    'allergens'        => $itemData['allergens'],
                    'preparation_time' => rand(10, 25),
                    'sort_order'       => 0,
                ]
            );

            // Add-ons for mains and starters
            if (in_array($itemData['category']->slug, ['main-courses', 'starters']) && $item->addons()->count() === 0) {
                if ($itemData['category']->slug === 'main-courses') {
                    $item->addons()->createMany([
                        ['name' => 'Side Salad',   'price' => 3.50, 'is_available' => true],
                        ['name' => 'Extra Sauce',  'price' => 1.50, 'is_available' => true],
                        ['name' => 'Extra Fries',  'price' => 3.00, 'is_available' => true],
                    ]);
                }
                if ($item->name === 'Classic Burger') {
                    $item->addons()->createMany([
                        ['name' => 'Extra Patty',  'price' => 4.00, 'is_available' => true],
                        ['name' => 'Bacon',        'price' => 2.00, 'is_available' => true],
                        ['name' => 'Extra Cheese', 'price' => 1.50, 'is_available' => true],
                    ]);
                }
            }
        }

        // ── Sample Coupons ────────────────────────────────────────
        \App\Models\Coupon::firstOrCreate(
            ['code' => 'WELCOME10'],
            ['type' => 'percentage', 'value' => 10, 'min_order_amount' => 20,
             'is_single_use' => true, 'is_active' => true,
             'notes' => 'Welcome discount for new customers']
        );
        \App\Models\Coupon::firstOrCreate(
            ['code' => 'SAVE5'],
            ['type' => 'fixed', 'value' => 5, 'min_order_amount' => 30,
             'is_single_use' => true, 'is_active' => true,
             'notes' => '€5 off orders over €30']
        );

        // ── Default Printer Config ────────────────────────────────
        PrinterConfig::firstOrCreate(
            ['name' => 'Main Printer'],
            [
                'type'        => 'network',
                'host'        => '192.168.1.100',
                'port'        => 9100,
                'paper_width' => 80,
                'auto_print'  => false, // disabled until configured
                'is_active'   => false,
            ]
        );

        // ── Sample Newsletter ─────────────────────────────────────
        \App\Models\NewsletterSubscription::firstOrCreate(
            ['email' => 'demo@newsletter.com'],
            ['name' => 'Demo Subscriber', 'is_active' => true, 'confirmed_at' => now()]
        );

        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('   Admin: admin@restaurant.com / Admin@12345!');
        $this->command->info('   Demo:  customer@demo.com / Customer@123');
    }
}
