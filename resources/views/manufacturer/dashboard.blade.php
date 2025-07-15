@extends('layouts.app')
@section('content')
<div class="container py-5">
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('manufacturer.dashboard') }}">Dashboard</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('workforce.index') }}">Workforce Management</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('inventory.index') }}">Inventory Management</a>
        </li>
    </ul>
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-3">Welcome, {{ $manufacturer->name ?? auth()->user()->name }}</h2>
            <p class="lead text-muted">Manage production orders, quality checks, and manufacturing processes</p>
        </div>
    </div>
    
    {{-- Chat Button --}}
   <div class="container"> 
  @if(auth()->user()->role && auth()->user()->role->name === 'manufacturer')
    @php
      // Assuming you have at least one supplier
      $supplier = \App\Models\User::whereHas('rawMaterialSupplier')->first();
    @endphp

    @if($supplier)
      <a href="{{ route('manufacturer.chat.index', ['partner' => $supplier->id]) }}" class="btn btn-primary mb-3">
        Chat with Supplier
      </a>
    @else
      <p>No supplier available for chat.</p>
    @endif
  @endif
</div>


    <!-- Manufacturer Chat Widget -->
    <div class="row mb-4">
        <div class="col-12">
            <manufacturer-chat :user-id="{{ auth()->id() }}"></manufacturer-chat>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('manufacturer.materials.browse') }}" class="btn btn-lg btn-primary">
                <i class="fas fa-shopping-cart me-2"></i> View & Order Raw Materials
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['total_production_orders'] }}</h4>
                            <small>Total Orders</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-industry fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['active_production_orders'] }}</h4>
                            <small>Active Orders</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-cogs fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['completed_orders'] }}</h4>
                            <small>Completed</small>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Workforce Distribution Management Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Workforce Distribution Management</h5>
                    <a href="{{ route('workforce.index') }}" class="btn btn-sm btn-primary">Manage Workforce</a>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-info text-white h-100">
                                <div class="card-body">
                                    <h4 class="mb-0">{{ $stats['total_workers'] ?? 0 }}</h4>
                                    <small>Total Workers</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @php
        $supplyCenterJson = json_encode($charts['workers_by_supply_center'] ?? (object)[]);
        $shiftJson = json_encode($charts['workers_by_shift'] ?? (object)[]);
    @endphp
    <script id="supply-center-data" type="application/json">{!! $supplyCenterJson !!}</script>
    <script id="shift-data" type="application/json">{!! $shiftJson !!}</script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const workersBySupplyCenter = JSON.parse(document.getElementById('supply-center-data').textContent);
        const workersByShift = JSON.parse(document.getElementById('shift-data').textContent);
        if (document.getElementById('workersBySupplyCenterChart')) {
            new Chart(document.getElementById('workersBySupplyCenterChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: workersBySupplyCenter.labels,
                    datasets: [{
                        label: 'Workers',
                        data: workersBySupplyCenter.data,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)'
                    }]
                },
                options: {responsive: true}
            });
        }
        if (document.getElementById('workersByShiftChart')) {
            new Chart(document.getElementById('workersByShiftChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: workersByShift.labels,
                    datasets: [{
                        label: 'Workers',
                        data: workersByShift.data,
                        backgroundColor: 'rgba(75, 192, 192, 0.7)'
                    }]
                },
                options: {responsive: true}
            });
        }
    </script>

    <div class="row">
        <!-- Finished Products Table -->
        <div class="col-12 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Finished Products (Clothes)</h5>
                    <a href="{{ route('manufacturer.products.create') }}" class="btn btn-sm btn-success">Add Product</a>
                </div>
                <div class="card-body">
                    @if(isset($products) && $products->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Description</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                    <tr>
                                        <td>
                                            @if($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" alt="Image" style="max-width: 60px;">
                                            @else
                                                <span class="text-muted">No Image</span>
                                            @endif
                                        </td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->category ?? '-' }}</td>
                                        <td>{{ $product->description }}</td>
                                        <td>{{ $product->price ? ('$' . number_format($product->price, 2)) : '-' }}</td>
                                        <td>{{ $product->inventory->quantity ?? 0 }}</td>
                                        <td>
                                            <a href="{{ route('manufacturer.products.edit', $product->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                            <form action="{{ route('manufacturer.products.destroy', $product->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No finished products found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Production Orders from Retailers -->
    @if(isset($retailerOrders) && $retailerOrders->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Production Orders from Retailers</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Retailer</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Status</th>
                                    <th>Due Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($retailerOrders as $order)
                                <tr>
                                    <td>{{ $order->order_number }}</td>
                                    <td>{{ $order->retailer->name ?? 'N/A' }}</td>
                                    <td>{{ $order->product->name ?? 'N/A' }}</td>
                                    <td>{{ $order->quantity }}</td>
                                    <td>{{ ucfirst($order->status) }}</td>
                                    <td>{{ $order->due_date ? $order->due_date->format('Y-m-d') : '-' }}</td>
                                    <td>
                                        <a href="{{ route('manufacturer.production-orders.show', $order->id) }}" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('manufacturer.purchase-orders') }}" class="btn btn-warning w-100">
                                <i class="fas fa-shopping-basket me-2"></i>My Purchase Orders
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('manufacturer.quality-checks') }}" class="btn btn-warning w-100">
                                <i class="fas fa-clipboard-check me-2"></i>Quality Checks
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('manufacturer.analytics') }}" class="btn btn-info w-100">
                                <i class="fas fa-chart-bar me-2"></i>Analytics
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('manufacturer.retailer-orders') }}" class="btn btn-primary w-100">
                                <i class="fas fa-list me-2"></i>Retailer Orders Management
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(isset($conversations))
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Supplier Chat</h5>
                </div>
                <div class="card-body">
                    @foreach($conversations as $conversation)
                        <h6>Chat with:
                            @foreach($conversation->participants as $participant)
                                @if($participant->id !== $user->id)
                                    {{ $participant->name }}
                                @endif
                            @endforeach
                        </h6>
                        <div>
                            @php
                                $recentMessages = $conversation->messages->sortByDesc('created_at')->take(3)->reverse();
                            @endphp
                            @foreach($recentMessages as $message)
                                <div>
                                    <strong>{{ $message->user->name }}:</strong>
                                    {{ $message->body }}
                                    <span class="text-muted small">{{ $message->created_at->diffForHumans() }}</span>
                                </div>
                            @endforeach
                        </div>
                        <a href="{{ route('chat.show', $conversation) }}" class="btn btn-outline-primary btn-sm mt-2 mb-2">
                            Go to Chat
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection 