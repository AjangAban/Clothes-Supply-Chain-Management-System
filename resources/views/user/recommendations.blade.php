@extends('layouts.app')
@section('content')
<div class="container">
    <nav>
        <a href="/admin/ml-analytics">ML Analytics</a> |
        <a href="/user/recommendations">User Recommendations</a>
    </nav>
    <h2>Your Segment: {{ $userSegment }}</h2>
    <h3>Recommended Products for You</h3>
    @if(count($userRecs) > 0)
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product</th>
                <th>Popularity (in your segment)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($userRecs as $rec)
                <tr>
                    <td>{{ $productNames[$rec['product_id']] ?? $rec['product_id'] }}</td>
                    <td>{{ $rec['count'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @else
        <p>No recommendations available for your segment yet.</p>
    @endif
</div>
@endsection 