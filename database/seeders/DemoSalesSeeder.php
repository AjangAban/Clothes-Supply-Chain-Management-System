<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DemoSalesSeeder extends Seeder
{
    public function run()
    {
        // Remove previous demo customers to avoid unique constraint errors
        DB::table('users')->where('email', 'like', 'customer%@example.com')->delete();

        // Create users
        $userIds = [];
        for ($i = 1; $i <= 10; $i++) {
            $userIds[] = DB::table('users')->insertGetId([
                'name' => 'Customer ' . $i,
                'email' => 'customer' . $i . '@example.com',
                'password' => bcrypt('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Remove previous demo products to avoid unique constraint errors
        DB::table('products')->where('sku', 'like', 'SKU%')->delete();

        // Create products
        $productIds = [];
        for ($i = 1; $i <= 10; $i++) {
            $price = rand(100, 1000) / 10;
            $cost = round($price * (rand(60, 90) / 100), 2);
            $productIds[] = DB::table('products')->insertGetId([
                'name' => 'Product ' . $i,
                'sku' => 'SKU' . $i,
                'description' => 'Description for product ' . $i,
                'material' => 'Material ' . rand(1, 5),
                'price' => $price,
                'cost' => $cost,
                'unit' => 'pcs',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create orders
        $orderIds = [];
        for ($i = 1; $i <= 30; $i++) {
            $userId = $userIds[array_rand($userIds)];
            $orderDate = Carbon::now()->subDays(rand(0, 60));
            $orderIds[] = DB::table('orders')->insertGetId([
                'user_id' => $userId,
                'status' => 'delivered',
                'total_amount' => 0,
                'shipping_address' => '123 Demo Street, City, Country',
                'shipping_city' => 'Demo City',
                'shipping_state' => 'Demo State',
                'shipping_zip' => '12345',
                'shipping_country' => 'Demo Country',
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);
        }

        // Create order items
        foreach ($orderIds as $orderId) {
            $numItems = rand(1, 5);
            $chosenProducts = array_rand($productIds, $numItems);
            if (!is_array($chosenProducts)) $chosenProducts = [$chosenProducts];
            foreach ($chosenProducts as $productIdx) {
                $productId = $productIds[$productIdx];
                $unitPrice = DB::table('products')->where('id', $productId)->value('price');
                $quantity = rand(1, 5);
                $totalPrice = $unitPrice * $quantity;
                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
} 