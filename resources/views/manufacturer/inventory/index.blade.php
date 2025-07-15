@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h2>Inventory Management</h2>
    <h4>Inventory Items</h4>
    <table class="table table-bordered">
        <thead><tr><th>Name</th><th>Type</th><th>Quantity</th><th>Unit</th><th>Reorder Point</th></tr></thead>
        <tbody>
        @foreach($items as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td>{{ $item->type }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->unit }}</td>
                <td>{{ $item->reorder_point }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <h4>Inventory Movements</h4>
    <table class="table table-bordered">
        <thead><tr><th>Item</th><th>Type</th><th>Quantity</th><th>Date</th><th>Reason</th></tr></thead>
        <tbody>
        @foreach($movements as $move)
            <tr>
                <td>{{ $move->item->name ?? 'N/A' }}</td>
                <td>{{ $move->type }}</td>
                <td>{{ $move->quantity }}</td>
                <td>{{ $move->date }}</td>
                <td>{{ $move->reason }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection 