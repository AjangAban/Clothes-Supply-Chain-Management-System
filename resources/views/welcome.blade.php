@extends('layouts.app')
@section('content')
<style>
    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 80px 0 60px 0;
        border-radius: 1rem;
        margin-bottom: 2rem;
    }
    .feature-icon {
        font-size: 2.5rem;
        color: #764ba2;
        margin-bottom: 1rem;
    }
    .role-icon {
        font-size: 2rem;
        color: #667eea;
        margin-bottom: 0.5rem;
    }
    .card.feature-hover {
        transition: transform 0.2s cubic-bezier(.4,2,.6,1), box-shadow 0.2s;
    }
    .card.feature-hover:hover {
        transform: translateY(-8px) scale(1.04);
        box-shadow: 0 8px 32px rgba(118,75,162,0.18), 0 1.5px 6px rgba(0,0,0,0.08);
        z-index: 2;
    }
</style>
    <div class="hero-section">
        <div class="row justify-content-center align-items-center" style="min-height: 60vh;">
            <div class="col-12 text-center">
                <h1 class="display-3 fw-bold mb-4">GenZ FashionZ Supply Chain Management System</h1>
                <p class="lead mb-4">A professional platform for managing the entire supply chain from raw materials to final delivery. Connect <strong>vendors</strong>, <strong>manufacturers</strong>, <strong>suppliers</strong>, <strong>retailers</strong>, and <strong>customers</strong> in one integrated system.</p>
                <a href="{{ route('register') }}" class="btn btn-outline-success btn-lg me-2">Register</a>
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Login</a>
            </div>
        </div>
    </div>
<div class="container">
    <div class="row text-center mb-5">
        <div class="col-12 mb-4">
            <h2 class="fw-bold">Key Features</h2>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0 feature-hover">
                <div class="card-body">
                    <div class="feature-icon mb-2"><i class="bi bi-graph-up"></i></div>
                    <h5 class="card-title">Analytics & ML</h5>
                    <p class="card-text">Advanced analytics with machine learning for demand prediction and customer segmentation.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0 feature-hover">
                <div class="card-body">
                    <div class="feature-icon mb-2"><i class="bi bi-box-seam"></i></div>
                    <h5 class="card-title">Inventory Management</h5>
                    <p class="card-text">Real-time inventory tracking across warehouses and retail stores with automated alerts.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0 feature-hover">
                <div class="card-body">
                    <div class="feature-icon mb-2"><i class="bi bi-chat-dots"></i></div>
                    <h5 class="card-title">Real-time Chat</h5>
                    <p class="card-text">Integrated communication for seamless collaboration between supply chain partners.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0 feature-hover">
                <div class="card-body">
                    <div class="feature-icon mb-2"><i class="bi bi-truck"></i></div>
                    <h5 class="card-title">Order & Delivery</h5>
                    <p class="card-text">Complete order processing and delivery tracking with automated logistics management.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0 feature-hover">
                <div class="card-body">
                    <div class="feature-icon mb-2"><i class="bi bi-file-earmark-check"></i></div>
                    <h5 class="card-title">Vendor Validation</h5>
                    <p class="card-text">Automated vendor validation system with PDF processing and facility visit scheduling.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm border-0 feature-hover">
                <div class="card-body">
                    <div class="feature-icon mb-2"><i class="bi bi-bar-chart"></i></div>
                    <h5 class="card-title">Reporting</h5>
                    <p class="card-text">Automated reporting system with scheduled reports for all stakeholders.</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row text-center mb-5 justify-content-center">
        <div class="col-12 mb-4">
            <h2 class="fw-bold">User Roles</h2>
        </div>
        <div class="col-lg-2 col-md-3 col-6 mb-4 d-flex justify-content-center">
            <div class="card h-100 shadow-sm border-0 feature-hover role-hover w-100">
                <div class="card-body">
                    <div class="role-icon"><i class="bi bi-building"></i></div>
                    <div>Vendor</div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-3 col-6 mb-4 d-flex justify-content-center">
            <div class="card h-100 shadow-sm border-0 feature-hover role-hover w-100">
                <div class="card-body">
                    <div class="role-icon"><i class="bi bi-truck"></i></div>
                    <div>Supplier</div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-3 col-6 mb-4 d-flex justify-content-center">
            <div class="card h-100 shadow-sm border-0 feature-hover role-hover w-100">
                <div class="card-body">
                    <div class="role-icon"><i class="bi bi-gear"></i></div>
                    <div>Manufacturer</div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-3 col-6 mb-4 d-flex justify-content-center">
            <div class="card h-100 shadow-sm border-0 feature-hover role-hover w-100">
                <div class="card-body">
                    <div class="role-icon"><i class="bi bi-shop"></i></div>
                    <div>Retailer</div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-3 col-6 mb-4 d-flex justify-content-center">
            <div class="card h-100 shadow-sm border-0 feature-hover role-hover w-100">
                <div class="card-body">
                    <div class="role-icon"><i class="bi bi-person-badge"></i></div>
                    <div>Administrator</div>
                </div>
            </div>
        </div>
    </div>
    @guest
    <div class="text-center my-5">
        <a href="{{ route('register') }}" class="btn btn-success btn-lg px-5 py-3">Get Started</a>
    </div>
    @endguest
</div>
<!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<style>
.role-hover {
    transition: transform 0.2s cubic-bezier(.4,2,.6,1), box-shadow 0.2s;
}
.role-hover:hover {
    transform: translateY(-8px) scale(1.07);
    box-shadow: 0 8px 32px rgba(102,126,234,0.18), 0 1.5px 6px rgba(0,0,0,0.08);
    z-index: 2;
}
</style>
@endsection