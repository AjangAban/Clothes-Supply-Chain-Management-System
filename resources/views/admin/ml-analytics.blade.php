@extends('layouts.app')
@section('content')
<div class="container">
    <nav>
        <a href="/admin/ml-analytics">ML Analytics</a> |
        <a href="/user/recommendations">User Recommendations</a>
    </nav>
    <h2>ML Segment Recommendations</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Segment</th>
                <th>Product</th>
                <th>Count</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recommendations as $rec)
                <tr>
                    <td>{{ $rec['segment'] }}</td>
                    <td>{{ $productNames[$rec['product_id']] ?? $rec['product_id'] }}</td>
                    <td>{{ $rec['count'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <h2>Sales Forecast</h2>
    <img src="{{ $forecastImg }}" alt="Sales Forecast" style="max-width:100%;height:auto;">

    <h2>Customer Segment Distribution</h2>
    <canvas id="segmentPie" width="400" height="200"></canvas>

    <h2>Top Products per Segment</h2>
    <canvas id="segmentBar" width="600" height="300"></canvas>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Pie chart for segment distribution
const segmentCounts = @json(collect($segments)->groupBy('segment')->map->count()->toArray());
const pieLabels = Object.keys(segmentCounts);
const pieData = Object.values(segmentCounts);
new Chart(document.getElementById('segmentPie'), {
    type: 'pie',
    data: {
        labels: pieLabels,
        datasets: [{ data: pieData, backgroundColor: ['#36a2eb', '#ff6384', '#ffce56', '#4bc0c0', '#9966ff', '#ff9f40'] }]
    }
});
// Bar chart for top products per segment
const recs = @json($recommendations);
const prodNames = @json($productNames);
const barData = {};
recs.forEach(r => {
    const seg = r.segment;
    const prod = prodNames[r.product_id] || r.product_id;
    if (!barData[seg]) barData[seg] = {};
    barData[seg][prod] = (barData[seg][prod] || 0) + parseInt(r.count);
});
const barLabels = Array.from(new Set(recs.map(r => prodNames[r.product_id] || r.product_id)));
const barDatasets = Object.keys(barData).map((seg, i) => ({
    label: 'Segment ' + seg,
    data: barLabels.map(p => barData[seg][p] || 0),
    backgroundColor: `hsl(${i * 60}, 70%, 60%)`
}));
new Chart(document.getElementById('segmentBar'), {
    type: 'bar',
    data: {
        labels: barLabels,
        datasets: barDatasets
    },
    options: { responsive: true, plugins: { legend: { position: 'top' } } }
});
</script>
@endsection 