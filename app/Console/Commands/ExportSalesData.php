<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExportSalesData extends Command
{
    protected $signature = 'export:sales-data';
    protected $description = 'Export sales data to CSV (date, product_id, quantity_sold)';

    public function handle()
    {
        $filePath = storage_path('app/exports/sales_data.csv');
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0777, true);
        }

        $sales = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->select(
                DB::raw('DATE(orders.created_at) as date'),
                'order_items.product_id',
                DB::raw('SUM(order_items.quantity) as quantity_sold')
            )
            ->groupBy(DB::raw('DATE(orders.created_at)'), 'order_items.product_id')
            ->orderBy('date')
            ->get();

        $handle = fopen($filePath, 'w');
        fputcsv($handle, ['date', 'product_id', 'quantity_sold']);
        foreach ($sales as $row) {
            fputcsv($handle, [(string)$row->date, $row->product_id, $row->quantity_sold]);
        }
        fclose($handle);

        $this->info('Sales data exported to ' . $filePath);
    }
} 