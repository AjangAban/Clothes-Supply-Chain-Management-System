<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MLAnalyticsController extends Controller
{
    // Admin: Show segment recommendations and sales forecast
    public function adminAnalytics()
    {
        $recPath = storage_path('app/exports/segment_recommendations.csv');
        $segPath = storage_path('app/exports/customer_segments.csv');
        $recommendations = $this->readCsv($recPath);
        $segments = $this->readCsv($segPath);
        $forecastImg = asset('storage/app/exports/sales_forecast.png');
        $productNames = $this->getProductNames();
        return view('admin.ml-analytics', compact('recommendations', 'segments', 'forecastImg', 'productNames'));
    }

    // User: Show personalized recommendations
    public function userRecommendations()
    {
        $user = Auth::user();
        $segPath = storage_path('app/exports/customer_segments.csv');
        $recPath = storage_path('app/exports/segment_recommendations.csv');
        $segments = $this->readCsv($segPath);
        $recommendations = $this->readCsv($recPath);
        $userSegment = null;
        foreach ($segments as $seg) {
            if ($seg['customer_id'] == $user->id) {
                $userSegment = $seg['segment'];
                break;
            }
        }
        $userRecs = collect($recommendations)->where('segment', $userSegment)->all();
        $productNames = $this->getProductNames();
        return view('user.recommendations', compact('userSegment', 'userRecs', 'productNames'));
    }

    // Helper to read CSV as array
    private function readCsv($path)
    {
        if (!file_exists($path)) return [];
        $rows = array_map('str_getcsv', file($path));
        $header = array_shift($rows);
        $data = [];
        foreach ($rows as $row) {
            $data[] = array_combine($header, $row);
        }
        return $data;
    }

    // Helper to get product names by id
    private function getProductNames()
    {
        return DB::table('products')->pluck('name', 'id')->toArray();
    }
} 