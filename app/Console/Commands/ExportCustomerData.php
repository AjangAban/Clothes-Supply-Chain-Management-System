<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExportCustomerData extends Command
{
    protected $signature = 'export:customer-data';
    protected $description = 'Export customer purchase data to CSV (customer_id, product_id, purchase_date, amount)';

    public function handle()
    {
        $filePath = storage_path('app/exports/customer_data.csv');
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0777, true);
        }

        $data = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->select(
                'users.id as customer_id',
                'order_items.product_id',
                DB::raw('DATE(orders.created_at) as purchase_date'),
                'order_items.total_price as amount'
            )
            ->orderBy('purchase_date')
            ->get();

        $handle = fopen($filePath, 'w');
        fputcsv($handle, ['customer_id', 'product_id', 'purchase_date', 'amount']);
        foreach ($data as $row) {
            fputcsv($handle, [$row->customer_id, $row->product_id, (string)$row->purchase_date, $row->amount]);
        }
        fclose($handle);

        $this->info('Customer data exported to ' . $filePath);
    }
} 