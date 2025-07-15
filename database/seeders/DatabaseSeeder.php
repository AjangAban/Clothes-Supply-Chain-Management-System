<?php
namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call([
            RolesTableSeeder::class,
            UsersTableSeeder::class,
            VendorsTableSeeder::class,
            DesignersTableSeeder::class,
            CollectionsTableSeeder::class,
            DesignsTableSeeder::class,
            SamplesTableSeeder::class,
            ProductsTableSeeder::class,
            ProductionOrdersTableSeeder::class,
            ProductionStagesTableSeeder::class,
            QualityChecksTableSeeder::class,
        ]);

        // Seed admin role and user
        $adminRole = \App\Models\Role::firstOrCreate([
            'name' => 'admin',
        ], [
            'display_name' => 'Admin',
            'description' => 'System administrator with full access',
        ]);
        \App\Models\User::firstOrCreate([
            'email' => 'brianakansasira07@gmail.com',
        ], [
            'name' => 'GenZ Admin',
            'password' => bcrypt('Brian Ak07'),
            'role_id' => $adminRole->id,
        ]);

        // Seed raw material supplier role and user
        $supplierRole = \App\Models\Role::firstOrCreate([
            'name' => 'raw_material_supplier',
        ], [
            'display_name' => 'Raw Material Supplier',
            'description' => 'Supplies fabric, thread, zippers, and other raw materials',
        ]);
        $supplierUser = \App\Models\User::firstOrCreate([
            'email' => 'supplier@genzfashionz.com',
        ], [
            'name' => 'GenZ Supplier',
            'password' => bcrypt('SupplierPass123'),
            'role_id' => $supplierRole->id,
        ]);
        \App\Models\RawMaterialSupplier::firstOrCreate([
            'user_id' => $supplierUser->id,
        ], [
            'company_name' => 'GenZ Fabrics Ltd.',
            'contact_person' => 'Alice Supplier',
            'email' => 'supplier@genzfashionz.com',
            'phone' => '123-456-7890',
            'address' => '123 Fabric Street, Textile City',
            'status' => 'active',
        ]);

        // --- DEMO DATA FOR ALL ROLES ---
        // 1. Create roles if not exist
        $roles = [
            'retailer' => \App\Models\Role::firstOrCreate(['name' => 'retailer'], ['display_name' => 'Retailer']),
            'delivery_personnel' => \App\Models\Role::firstOrCreate(['name' => 'delivery_personnel'], ['display_name' => 'Delivery Personnel']),
            'manufacturer' => \App\Models\Role::firstOrCreate(['name' => 'manufacturer'], ['display_name' => 'Manufacturer']),
            'vendor' => \App\Models\Role::firstOrCreate(['name' => 'vendor'], ['display_name' => 'Vendor']),
        ];

        // 2. Create users for each role
        $retailer = \App\Models\User::firstOrCreate([
            'email' => 'retailer@genzfashionz.com',
        ], [
            'name' => 'Retail Store Manager',
            'password' => bcrypt('RetailerPass123'),
            'role_id' => $roles['retailer']->id,
        ]);
        $deliveryRole = Role::firstOrCreate([
            'name' => 'delivery_personnel',
        ], [
            'display_name' => 'Delivery Personnel',
            'description' => 'Handles deliveries',
        ]);
        $vendor = \App\Models\User::firstOrCreate([
            'email' => 'vendor@genzfashionz.com',
        ], [
            'name' => 'Vendor User',
            'password' => bcrypt('VendorPass123'),
            'role_id' => $roles['vendor']->id,
        ]);

        // 4. Create and assign a retail store to the retailer
        $retailStore = \App\Models\RetailStore::firstOrCreate([
            'name' => 'Main Retail Store',
        ], [
            'address' => '456 Retail St',
            'manager_id' => $retailer->id,
            'contact_person' => $retailer->name,
            'phone' => '555-0002',
            'email' => 'retailer@genzfashionz.com',
            'opening_time' => '08:00:00',
            'closing_time' => '20:00:00',
            'status' => 'active',
        ]);

        // 5. Create a product for inventory and orders
        $product = \App\Models\Product::firstOrCreate([
            'name' => 'Classic T-Shirt',
        ], [
            'sku' => 'TSHIRT-001',
            'description' => 'A classic cotton t-shirt',
            'material' => 'Cotton',
            'size' => 'M',
            'color' => 'White',
            'price' => 20.00,
            'cost' => 10.00,
            'category' => 'tops',
            'unit' => 'pcs',
            'min_stock_level' => 10,
            'max_stock_level' => 1000,
            'is_active' => true,
        ]);

        // 6. Create inventory for warehouse and retail store
        \App\Models\Inventory::firstOrCreate([
            'product_id' => $product->id,
            'warehouse_id' => null,
        ], [
            'location_type' => 'retail',
            'location_id' => $retailStore->id,
            'quantity' => 100,
            'reserved_quantity' => 10,
            'available_quantity' => 90,
            'batch_number' => 'BATCH-002',
            'status' => 'active',
        ]);

        // 7. Create a sample order for the retailer
        $order = \App\Models\Order::firstOrCreate([
            'user_id' => $retailer->id,
            'order_number' => 'ORDER-001',
        ], [
            'source' => 'retailer',
            'status' => 'pending',
            'total_amount' => 100,
            'tax_amount' => 10,
            'shipping_amount' => 5,
            'shipping_address' => '123 Main St',
            'shipping_city' => 'City',
            'shipping_state' => 'State',
            'shipping_zip' => '12345',
            'shipping_country' => 'Country',
        ]);

        // 8. Create a delivery for the order, assigned to delivery personnel
        \App\Models\Delivery::firstOrCreate([
            'order_id' => $order->id,
        ], [
            'driver_id' => $deliveryRole->id,
            'status' => 'assigned',
            'tracking_number' => 'TRACK-' . uniqid(),
            'delivery_fee' => 5.00,
        ]);

        // Fix legacy orders: set supplier_id for all orders based on first product
        \App\Models\Order::whereNull('supplier_id')->each(function ($order) {
            $firstItem = $order->orderItems()->first();
            if ($firstItem) {
                $product = $firstItem->product;
                if ($product && $product->supplier_id) {
                    $order->supplier_id = $product->supplier_id;
                    $order->save();
                }
            }
        });

        // Ensure manufacturer user exists
        $user = \App\Models\User::firstOrCreate([
            'email' => 'manufacturer@genzfashionz.com',
        ], [
            'name' => 'Manufacturer User',
            'password' => bcrypt('password'),
            'role_id' => 2, // Assuming 2 is manufacturer
        ]);

        // Ensure manufacturer profile exists
        $manufacturer = \App\Models\Manufacturer::firstOrCreate([
            'user_id' => $user->id,
        ], [
            'name' => $user->company_name ?? $user->name,
            'email' => $user->email,
        ]);

        // Ensure at least one product exists
        $product = \App\Models\Product::firstOrCreate([
            'manufacturer_id' => $manufacturer->id,
            'name' => 'Demo Product',
        ], [
            'sku' => 'DEMO-001',
            'description' => 'A demo product for testing',
            'material' => 'Cotton',
            'cost' => 10.00,
            'unit' => 'pcs',
        ]);

        // Ensure at least one production order exists
        $productionOrder = \App\Models\ProductionOrder::firstOrCreate([
            'manufacturer_id' => $manufacturer->id,
            'product_id' => $product->id,
            'order_number' => 'PO-001',
        ], [
            'quantity' => 100,
            'status' => 'pending',
        ]);

        // Ensure some production stages exist
        $stages = ['Cutting', 'Sewing', 'Finishing'];
        foreach ($stages as $stageName) {
            \App\Models\ProductionStage::firstOrCreate([
                'production_order_id' => $productionOrder->id,
                'stage_name' => $stageName,
            ], [
                'status' => 'pending',
            ]);
        }

        // \\App\Models\User::factory(10)->create();
        $this->call(ChatDemoSeeder::class);
        $this->call(DemoSalesSeeder::class);

        if (!User::where('email', 'delivery@example.com')->exists()) {
            $user = User::create([
                'name' => 'Test Delivery',
                'email' => 'delivery@example.com',
                'password' => bcrypt('password'),
                'role_id' => $deliveryRole->id,
            ]);
        }
    }
}
