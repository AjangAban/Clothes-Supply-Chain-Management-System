@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-success bg-gradient-custom text-white">
                <style>
                    .bg-gradient-custom {
                        background: linear-gradient(135deg, #218838 0%, #28a745 100%) !important;
                    }
                    .welcome-title {
                        text-shadow: 1px 1px 4px rgba(0,0,0,0.4);
                    }
                </style>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-1 welcome-title">Welcome, {{ isset($user) ? $user->name : ($supplier->contact_person ?? 'Supplier') }}</h2>
                            <p class="mb-0 opacity-75">Raw Material Supplier Dashboard - Supply Chain Management</p>
                        </div>

                        <div class="col-md-4 text-end">
                            <div class="d-flex justify-content-end">
                                <div class="me-3">
                                    <small class="opacity-75">Active Orders</small>
                                    <h4 class="mb-0">{{ $stats['active_orders'] ?? 0 }}</h4>
                                </div>
                                <div>
                                    <small class="opacity-75">Today's Shipments</small>
                                    <h4 class="mb-0">{{ $stats['pending_deliveries'] ?? 0 }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($linkedManufacturers->count())
    <div class="row mb-3">
        <div class="col-12 text-center">
            <h5 class="mb-3">Chat with Manufacturer</h5>
            @foreach($linkedManufacturers as $manu)
                <a href="{{ route('supplier.chat.index', ['partner' => $manu->user->id]) }}"
                   class="btn btn-outline-primary me-2 mb-2">
                   💬 {{ $manu->user->name }}
                </a>
            @endforeach
        </div>
    </div>
@else
    <p class="text-muted text-center">No manufacturers available for chat.</p>
@endif



    <!-- KPI Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Materials</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_materials'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Pending Orders</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_orders'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">In Transit</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_deliveries'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Monthly Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($stats['total_revenue'] ?? 0, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Tabs -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Material Catalog</h5>
                        <a href="{{ route('supplier.materials.create') }}" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>Add Material
                        </a>
                    </div>
                    <div class="row">
                        @forelse($topMaterials as $material)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title">{{ $material->name }}</h6>
                                        @if($material->stock_quantity > 20)
                                            <span class="badge bg-success">In Stock</span>
                                        @elseif($material->stock_quantity > 0)
                                            <span class="badge bg-warning">Low Stock</span>
                                        @else
                                            <span class="badge bg-danger">Out of Stock</span>
                                        @endif
                                    </div>
                                    <p class="card-text text-muted">{{ Str::limit($material->description, 50) }}</p>
                                    <div class="row text-center">
                                        <div class="col-6">
                                            <small class="text-muted">Price</small>
                                            <div class="fw-bold">${{ number_format($material->price, 2) }}/{{ $material->unit }}</div>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted">Stock</small>
                                            <div class="fw-bold">{{ $material->stock_quantity }} {{ $material->unit }}s</div>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('supplier.materials.edit', $material) }}" class="btn btn-outline-primary btn-sm me-2">Edit</a>
                                        <a href="{{ route('supplier.materials.stock.edit', $material) }}" class="btn btn-outline-success btn-sm">Update Stock</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <p class="text-muted">No materials found. <a href="{{ route('supplier.materials.create') }}">Add your first material.</a></p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mt-4">
        <div class="col-md-3 mb-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="fas fa-boxes fa-2x text-success mb-3"></i>
                    <h6>Material Catalog</h6>
                    <p class="text-muted small">Manage your material catalog and pricing</p>
                    <a href="{{ route('supplier.materials.index') }}" class="btn btn-outline-success btn-sm">Manage</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="fas fa-clipboard-list fa-2x text-primary mb-3"></i>
                    <h6>Order Management</h6>
                    <p class="text-muted small">View and process purchase orders</p>
                    <a href="{{ route('supplier.orders.index') }}" class="btn btn-outline-primary btn-sm">View Orders</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="fas fa-truck fa-2x text-warning mb-3"></i>
                    <h6>Delivery Tracking</h6>
                    <p class="text-muted small">Update delivery statuses</p>
                    <a href="{{ route('supplier.deliveries.index') }}" class="btn btn-outline-warning btn-sm">Track</a>
                </div>
            </div>
        </div>
        

    @if(isset($conversations))
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Manufacturer Chat</h5>
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
                        @php $editingMessageId = request('edit'); @endphp
                        @include('chat._chat-partial', ['conversation' => $conversation, 'editingMessageId' => $editingMessageId])
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="row mb-4">
        <div class="col-12">
            <supplier-chat :user-id="{{ auth()->id() }}"></supplier-chat>
        </div>
    </div>
</div>

<!-- Modals -->
<!-- Add Material Modal -->
<div class="modal fade" id="addMaterialModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Material Name</label>
                            <input type="text" class="form-control" placeholder="Enter material name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Category</label>
                            <select class="form-select">
                                <option>Select Category</option>
                                <option>Fabric</option>
                                <option>Thread</option>
                                <option>Zippers</option>
                                <option>Buttons</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Price per Unit</label>
                            <input type="number" class="form-control" placeholder="Enter price">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Unit</label>
                            <select class="form-select">
                                <option>Select Unit</option>
                                <option>m²</option>
                                <option>spools</option>
                                <option>units</option>
                                <option>kg</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Initial Stock</label>
                            <input type="number" class="form-control" placeholder="Enter initial stock">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Minimum Order Quantity</label>
                            <input type="number" class="form-control" placeholder="Enter MOQ">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" rows="3" placeholder="Material description..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success">Add Material</button>
            </div>
        </div>
    </div>
</div>

<!-- Update Delivery Modal -->
<div class="modal fade" id="updateDeliveryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Delivery Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Shipment</label>
                        <select class="form-select">
                            <option>Select Shipment</option>
                            <option>SH-001 - Cotton Fabric to FashionCorp</option>
                            <option>SH-002 - Thread to StyleMakers</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select">
                            <option>Select Status</option>
                            <option>Shipped</option>
                            <option>In Transit</option>
                            <option>Out for Delivery</option>
                            <option>Delivered</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tracking Number</label>
                        <input type="text" class="form-control" placeholder="Enter tracking number">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" rows="3" placeholder="Delivery notes..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Update Status</button>
            </div>
        </div>
    </div>
</div>

<!-- Update Inventory Modal -->
<div class="modal fade" id="updateInventoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Inventory</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Material</label>
                        <select class="form-select">
                            <option>Select Material</option>
                            <option>Cotton Fabric</option>
                            <option>Polyester Thread</option>
                            <option>Metal Zippers</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Stock Level</label>
                        <input type="number" class="form-control" placeholder="Enter new stock level">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <select class="form-select">
                            <option>Select Reason</option>
                            <option>New Stock Received</option>
                            <option>Stock Adjustment</option>
                            <option>Quality Control</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" rows="3" placeholder="Inventory update notes..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary">Update Inventory</button>
            </div>
        </div>
    </div>
</div>

@endsection 