@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Analytics Dashboard</h2>
    <div class="mb-3">
        <a href="/admin/analytics-dashboard/export/csv" class="btn btn-primary">Export CSV</a>
        <a href="/admin/analytics-dashboard/export/excel" class="btn btn-success">Export Excel</a>
        <a href="/admin/analytics-dashboard/export/pdf" class="btn btn-danger">Export PDF</a>
    </div>
    <h4>Sales Over Time (Last 30 Days)</h4>
    <canvas id="salesChart"></canvas>
    <h4>Top 5 Products</h4>
    <canvas id="topProductsChart"></canvas>
    <h4>Inventory Status</h4>
    <table class="table">
        <thead><tr><th>Product</th><th>Stock</th></tr></thead>
        <tbody>
            @foreach($inventory as $item)
                <tr><td>{{ $item->name }}</td><td>{{ $item->quantity }}</td></tr>
            @endforeach
        </tbody>
    </table>
    <h4>Customer Segments</h4>
    <table class="table">
        <thead><tr><th>Customer ID</th><th>Segment</th></tr></thead>
        <tbody>
            @foreach($segments as $seg)
                <tr><td>{{ $seg['customer_id'] }}</td><td>{{ $seg['segment'] }}</td></tr>
            @endforeach
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const salesData = @json($salesOverTime);
const salesLabels = salesData.map(d => d.date);
const salesTotals = salesData.map(d => d.total);
new Chart(document.getElementById('salesChart'), {
    type: 'line',
    data: { labels: salesLabels, datasets: [{ label: 'Sales', data: salesTotals, borderColor: '#36a2eb', fill: false }] }
});
const topProducts = @json($topProducts);
new Chart(document.getElementById('topProductsChart'), {
    type: 'bar',
    data: {
        labels: topProducts.map(d => d.name),
        datasets: [{ label: 'Units Sold', data: topProducts.map(d => d.total_sold), backgroundColor: '#ff6384' }]
    }
});
</script>
@endsection 