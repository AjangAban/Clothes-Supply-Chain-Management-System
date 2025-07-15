<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalyticsController extends Controller
{
    public function dashboard()
    {
        // Sales over time (last 30 days)
        $salesOverTime = DB::table('orders')
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->where('status', 'delivered')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top products
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Inventory status
        $inventory = DB::table('products')
            ->select('name', 'quantity')
            ->get();

        // Customer segments (from ML)
        $segments = [];
        $segPath = storage_path('app/exports/customer_segments.csv');
        if (file_exists($segPath)) {
            $rows = array_map('str_getcsv', file($segPath));
            $header = array_shift($rows);
            foreach ($rows as $row) {
                $segments[] = array_combine($header, $row);
            }
        }

        return view('admin.analytics-dashboard', compact('salesOverTime', 'topProducts', 'inventory', 'segments'));
    }

    // CSV Export
    public function exportCsv(Request $request)
    {
        $data = $this->getAnalyticsData();
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="analytics_report.csv"',
        ];
        $callback = function() use ($data) {
            $handle = fopen('php://output', 'w');
            foreach ($data as $section => $rows) {
                fputcsv($handle, [$section]);
                if (count($rows) > 0) {
                    fputcsv($handle, array_keys((array)$rows[0]));
                    foreach ($rows as $row) {
                        fputcsv($handle, (array)$row);
                    }
                }
                fputcsv($handle, []);
            }
            fclose($handle);
        };
        return response()->stream($callback, 200, $headers);
    }

    // Excel Export
    public function exportExcel(Request $request)
    {
        $data = $this->getAnalyticsData();
        return Excel::download(new \App\Exports\AnalyticsExport($data), 'analytics_report.xlsx');
    }

    // PDF Export
    public function exportPdf(Request $request)
    {
        $data = $this->getAnalyticsData();
        $pdf = Pdf::loadView('admin.analytics-pdf', $data);
        return $pdf->download('analytics_report.pdf');
    }

    // Helper to gather analytics data for export
    private function getAnalyticsData()
    {
        $salesOverTime = DB::table('orders')
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->where('status', 'delivered')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();
        $inventory = DB::table('products')
            ->select('name', 'quantity')
            ->get();
        $segments = [];
        $segPath = storage_path('app/exports/customer_segments.csv');
        if (file_exists($segPath)) {
            $rows = array_map('str_getcsv', file($segPath));
            $header = array_shift($rows);
            foreach ($rows as $row) {
                $segments[] = array_combine($header, $row);
            }
        }
        return [
            'Sales Over Time' => $salesOverTime,
            'Top Products' => $topProducts,
            'Inventory' => $inventory,
            'Customer Segments' => $segments,
        ];
    }
}
