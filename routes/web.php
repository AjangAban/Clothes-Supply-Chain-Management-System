<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Auth\CustomRegisterController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\Vendor\ProfileController as VendorProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->prefix('supplier')->name('supplier.')->group(function () {
    Route::get('/chat/{partner}', [App\Http\Controllers\Supplier\ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/send', [App\Http\Controllers\Supplier\ChatController::class, 'store'])->name('chat.send');
    Route::get('/chat/fetch/{partner}', [App\Http\Controllers\Supplier\ChatController::class, 'fetch'])->name('chat.fetch');
});


Route::middleware(['auth'])->prefix('manufacturer')->name('manufacturer.')->group(function () {
    Route::get('/chat/{partner}', [App\Http\Controllers\Manufacturer\ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/send', [App\Http\Controllers\Manufacturer\ChatController::class, 'store'])->name('chat.send');
    Route::get('/chat/fetch/{partner}', [App\Http\Controllers\Manufacturer\ChatController::class, 'fetch'])->name('chat.fetch');
});

// Custom registration routes
Route::get('/register', [CustomRegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [CustomRegisterController::class, 'register']);

// Other auth routes
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
// POST route for logging out (required for form-based logout)
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Password reset routes
Route::get('/password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

// Redirect authenticated users to their role-specific dashboard
Route::get('/home', function () {
    $user = auth()->user();
    if ($user && $user->role) {
        return redirect($user->role->getDashboardRoute());
    }
    return redirect('/dashboard');
})->name('home');

// Default dashboard for users without roles
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user && $user->role && method_exists($user->role, 'getDashboardRoute')) {
        return redirect($user->role->getDashboardRoute());
    }
    return redirect('/'); // Or show a 404 or error page
})->name('dashboard');

// Vendor Routes
Route::prefix('vendor')->middleware(['auth', 'role:vendor'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Vendor\DashboardController::class, 'index'])->name('vendor.dashboard');
    
    // Products Management
    Route::get('/products', [App\Http\Controllers\Vendor\DashboardController::class, 'products'])->name('vendor.products');
    Route::get('/products/create', [App\Http\Controllers\Vendor\DashboardController::class, 'createProduct'])->name('vendor.products.create');
    Route::post('/products', [App\Http\Controllers\Vendor\DashboardController::class, 'storeProduct'])->name('vendor.products.store');
    Route::get('/products/{id}', [App\Http\Controllers\Vendor\DashboardController::class, 'showProduct'])->name('vendor.products.show');
    Route::put('/products/{id}', [App\Http\Controllers\Vendor\DashboardController::class, 'updateProduct'])->name('vendor.products.update');
    
    // Applications Management
    Route::get('/applications', [App\Http\Controllers\Vendor\DashboardController::class, 'applications'])->name('vendor.applications');
    Route::get('/applications/create', [App\Http\Controllers\Vendor\DashboardController::class, 'createApplication'])->name('vendor.applications.create');
    Route::post('/applications', [App\Http\Controllers\Vendor\DashboardController::class, 'storeApplication'])->name('vendor.applications.store');
    Route::get('/applications/{id}', [App\Http\Controllers\Vendor\DashboardController::class, 'showApplication'])->name('vendor.applications.show');
    Route::put('/applications/{id}', [App\Http\Controllers\Vendor\DashboardController::class, 'updateApplication'])->name('vendor.applications.update');
    
    // Facility Visits
    Route::get('/facility-visits', [App\Http\Controllers\Vendor\DashboardController::class, 'facilityVisits'])->name('vendor.facility-visits');
    Route::get('/facility-visits/{id}', [App\Http\Controllers\Vendor\DashboardController::class, 'showFacilityVisit'])->name('vendor.facility-visits.show');
    Route::put('/facility-visits/{id}', [App\Http\Controllers\Vendor\DashboardController::class, 'updateFacilityVisit'])->name('vendor.facility-visits.update');
    
    // Designs Management
    Route::get('/designs', [App\Http\Controllers\Vendor\DashboardController::class, 'designs'])->name('vendor.designs');
    Route::get('/designs/create', [App\Http\Controllers\Vendor\DashboardController::class, 'createDesign'])->name('vendor.designs.create');
    Route::post('/designs', [App\Http\Controllers\Vendor\DashboardController::class, 'storeDesign'])->name('vendor.designs.store');
    Route::get('/designs/{id}', [App\Http\Controllers\Vendor\DashboardController::class, 'showDesign'])->name('vendor.designs.show');
    
    // Samples Management
    Route::get('/samples', [App\Http\Controllers\Vendor\DashboardController::class, 'samples'])->name('vendor.samples');
    Route::get('/samples/create', [App\Http\Controllers\Vendor\DashboardController::class, 'createSample'])->name('vendor.samples.create');
    Route::post('/samples', [App\Http\Controllers\Vendor\DashboardController::class, 'storeSample'])->name('vendor.samples.store');
    Route::get('/samples/{id}', [App\Http\Controllers\Vendor\DashboardController::class, 'showSample'])->name('vendor.samples.show');
    
    // Analytics
    Route::get('/analytics', [App\Http\Controllers\Vendor\DashboardController::class, 'analytics'])->name('vendor.analytics');
    
    // Profile Management
    Route::get('/profile', [App\Http\Controllers\Vendor\DashboardController::class, 'profile'])->name('vendor.profile');
    Route::put('/profile', [App\Http\Controllers\Vendor\DashboardController::class, 'updateProfile'])->name('vendor.profile.update');
    // Profile creation route
    Route::get('/profile/create', [App\Http\Controllers\Vendor\ProfileController::class, 'create'])->name('vendor.profile.create');
    Route::post('/profile', [App\Http\Controllers\Vendor\ProfileController::class, 'store'])->name('vendor.profile.store');
});

// Manufacturer Routes
Route::middleware(['auth', 'role:manufacturer'])->prefix('manufacturer')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Manufacturer\DashboardController::class, 'index'])->name('manufacturer.dashboard');
    
    // Production Orders
    Route::get('/production-orders', [App\Http\Controllers\Manufacturer\DashboardController::class, 'productionOrders'])->name('manufacturer.production-orders');
    Route::get('/production-orders/create', [App\Http\Controllers\Manufacturer\DashboardController::class, 'createProductionOrder'])->name('manufacturer.production-orders.create');
    Route::post('/production-orders', [App\Http\Controllers\Manufacturer\DashboardController::class, 'storeProductionOrder'])->name('manufacturer.production-orders.store');
    Route::get('/production-orders/{id}', [App\Http\Controllers\Manufacturer\DashboardController::class, 'showProductionOrder'])->name('manufacturer.production-orders.show');
    Route::put('/production-orders/{id}/status', [App\Http\Controllers\Manufacturer\DashboardController::class, 'updateProductionOrderStatus'])->name('manufacturer.production-orders.update-status');
    
    // Production order actions
    Route::post('/production-orders/{id}/accept', [App\Http\Controllers\Manufacturer\DashboardController::class, 'acceptProductionOrder'])->name('manufacturer.production-orders.accept');
    Route::post('/production-orders/{id}/reject', [App\Http\Controllers\Manufacturer\DashboardController::class, 'rejectProductionOrder'])->name('manufacturer.production-orders.reject');
    Route::post('/production-orders/{id}/start', [App\Http\Controllers\Manufacturer\DashboardController::class, 'startProductionOrder'])->name('manufacturer.production-orders.start');
    Route::post('/production-orders/{id}/complete', [App\Http\Controllers\Manufacturer\DashboardController::class, 'completeProductionOrder'])->name('manufacturer.production-orders.complete');
    Route::post('/production-orders/{id}/ship', [App\Http\Controllers\Manufacturer\DashboardController::class, 'markReadyToShip'])->name('manufacturer.production-orders.ship');
    Route::post('/manufacturer/production-orders/{id}/deliver', [App\Http\Controllers\Manufacturer\DashboardController::class, 'deliverProductionOrder'])
        ->name('manufacturer.production-orders.deliver')
        ->middleware(['auth', 'role:manufacturer']);
    
    // Production Stages
    Route::get('/production-stages', [App\Http\Controllers\Manufacturer\DashboardController::class, 'productionStages'])->name('manufacturer.production-stages');
    Route::put('/production-stages/{id}/status', [App\Http\Controllers\Manufacturer\DashboardController::class, 'updateStageStatus'])->name('manufacturer.production-stages.update-status');
    
    // Quality Checks
    Route::get('/quality-checks', [App\Http\Controllers\Manufacturer\DashboardController::class, 'qualityChecks'])->name('manufacturer.quality-checks');
    Route::get('/quality-checks/{id}', [App\Http\Controllers\Manufacturer\DashboardController::class, 'showQualityCheck'])->name('manufacturer.quality-checks.show');
    Route::put('/quality-checks/{id}', [App\Http\Controllers\Manufacturer\DashboardController::class, 'updateQualityCheck'])->name('manufacturer.quality-checks.update');
    
    // Suppliers
    Route::get('/suppliers', [App\Http\Controllers\Manufacturer\DashboardController::class, 'suppliers'])->name('manufacturer.suppliers');
    
    // Inventory
    Route::get('/inventory', [App\Http\Controllers\Manufacturer\DashboardController::class, 'inventory'])->name('manufacturer.inventory');
    
    // Analytics
    Route::get('/analytics', [App\Http\Controllers\Manufacturer\DashboardController::class, 'analytics'])->name('manufacturer.analytics');
    
    // Raw Materials
    Route::get('/materials/browse', [App\Http\Controllers\Manufacturer\DashboardController::class, 'browseMaterials'])->name('manufacturer.materials.browse');
    
    // Profile Management
    Route::get('/profile', [App\Http\Controllers\Manufacturer\DashboardController::class, 'showProfile'])->name('manufacturer.profile');
    Route::get('/profile/edit', [App\Http\Controllers\Manufacturer\DashboardController::class, 'editProfile'])->name('manufacturer.profile.edit');
    Route::post('/profile/edit', [App\Http\Controllers\Manufacturer\DashboardController::class, 'updateProfile'])->name('manufacturer.profile.update');

    // Raw Material Order
    Route::get('/materials/order/{material}', [App\Http\Controllers\Manufacturer\DashboardController::class, 'orderMaterial'])->name('manufacturer.materials.order');
    Route::post('/manufacturer/materials/order/{material}', [App\Http\Controllers\Manufacturer\DashboardController::class, 'placeMaterialOrder'])->name('manufacturer.materials.order.place');

    // Finished products (clothes) CRUD
    Route::get('/products/create', [App\Http\Controllers\Manufacturer\ProductController::class, 'create'])->name('manufacturer.products.create');
    Route::post('/products', [App\Http\Controllers\Manufacturer\ProductController::class, 'store'])->name('manufacturer.products.store');
    Route::get('/products/{id}/edit', [App\Http\Controllers\Manufacturer\ProductController::class, 'edit'])->name('manufacturer.products.edit');
    Route::put('/products/{id}', [App\Http\Controllers\Manufacturer\ProductController::class, 'update'])->name('manufacturer.products.update');
    Route::delete('/products/{id}', [App\Http\Controllers\Manufacturer\ProductController::class, 'destroy'])->name('manufacturer.products.destroy');

    // Purchase Orders
    Route::get('/purchase-orders', [App\Http\Controllers\Manufacturer\DashboardController::class, 'purchaseOrders'])->name('manufacturer.purchase-orders');
    Route::get('/purchase-orders/{order}/edit', [App\Http\Controllers\Manufacturer\DashboardController::class, 'editPurchaseOrder'])->name('manufacturer.purchase-orders.edit');
    Route::put('/purchase-orders/{order}', [App\Http\Controllers\Manufacturer\DashboardController::class, 'updatePurchaseOrder'])->name('manufacturer.purchase-orders.update');
    Route::delete('/purchase-orders/{order}', [App\Http\Controllers\Manufacturer\DashboardController::class, 'cancelPurchaseOrder'])->name('manufacturer.purchase-orders.cancel');

    // Retailer Orders Management
    Route::get('/retailer-orders', [App\Http\Controllers\Manufacturer\DashboardController::class, 'retailerOrders'])->name('manufacturer.retailer-orders');

    // Workforce Management
    Route::resource('workforce', App\Http\Controllers\Manufacturer\WorkforceController::class);
    Route::resource('tasks', App\Http\Controllers\Manufacturer\TaskController::class);
    Route::post('tasks/{task}/assign', [App\Http\Controllers\Manufacturer\TaskController::class, 'assign'])->name('tasks.assign');
    Route::get('workforce-report', [App\Http\Controllers\Manufacturer\TaskController::class, 'report'])->name('workforce.report');
    // Inventory Management
    Route::resource('inventory', App\Http\Controllers\Manufacturer\InventoryController::class);
    // Order Processing
    Route::get('/order-processing', [App\Http\Controllers\Manufacturer\DashboardController::class, 'orderProcessing'])->name('manufacturer.order-processing');
    Route::resource('supply-centers', App\Http\Controllers\Manufacturer\SupplyCenterController::class);
    Route::get('auto-assign', [App\Http\Controllers\Manufacturer\TaskController::class, 'autoAssign'])->name('tasks.auto-assign');
});

// Redirect old workers URLs to new workforce management
Route::redirect('/manufacturer/workers', '/manufacturer/workforce');
Route::redirect('/manufacturer/workers/create', '/manufacturer/workforce/create');
Route::redirect('/manufacturer/workers/{any}', '/manufacturer/workforce', 301)->where('any', '.*');

// Retailer Routes
Route::prefix('retailer')->middleware(['auth', 'role:retailer'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Retailer\DashboardController::class, 'index'])->name('retailer.dashboard');
    
    // Profile routes
    Route::get('/profile/create', [App\Http\Controllers\Retailer\DashboardController::class, 'createProfile'])->name('retailer.profile.create');
    Route::post('/profile/create', [App\Http\Controllers\Retailer\DashboardController::class, 'storeProfile'])->name('retailer.profile.store');
    
    // Orders routes
    Route::get('/orders', [App\Http\Controllers\Retailer\DashboardController::class, 'orders'])->name('retailer.orders');
    Route::get('/orders/{id}', [App\Http\Controllers\Retailer\DashboardController::class, 'showOrder'])->name('retailer.orders.show');
    Route::put('/orders/{id}/status', [App\Http\Controllers\Retailer\DashboardController::class, 'updateOrderStatus'])->name('retailer.orders.update-status');
    
    // Inventory routes
    Route::get('/inventory', [App\Http\Controllers\Retailer\DashboardController::class, 'inventory'])->name('retailer.inventory');
    Route::put('/retailer/inventory/{id}', [App\Http\Controllers\Retailer\DashboardController::class, 'retailerUpdateInventory'])->name('retailer.inventory.update');
    Route::post('/inventory/add', [App\Http\Controllers\Retailer\DashboardController::class, 'addToInventory'])->name('retailer.inventory.add');
    
    // Returns routes
    Route::get('/returns', [App\Http\Controllers\Retailer\DashboardController::class, 'returns'])->name('retailer.returns');
    Route::post('/returns/{orderId}/process', [App\Http\Controllers\Retailer\DashboardController::class, 'processReturn'])->name('retailer.returns.process');
    
    // Analytics routes
    Route::get('/analytics', [App\Http\Controllers\Retailer\DashboardController::class, 'analytics'])->name('retailer.analytics');
    
    // Profile routes
    Route::get('/profile', [App\Http\Controllers\Retailer\DashboardController::class, 'profile'])->name('retailer.profile');
    Route::put('/profile', [App\Http\Controllers\Retailer\DashboardController::class, 'updateProfile'])->name('retailer.profile.update');

    // Browse manufacturer products
    Route::get('/products/browse', [App\Http\Controllers\Retailer\ProductController::class, 'browseManufacturerProducts'])->name('retailer.products.browse');
    // Place production order
    Route::get('/production-orders/create/{product}', [App\Http\Controllers\Retailer\ProductController::class, 'createProductionOrder'])->name('retailer.production-orders.create');
    Route::post('/production-orders', [App\Http\Controllers\Retailer\ProductController::class, 'storeProductionOrder'])->name('retailer.production-orders.store');

    // Production Orders routes
    Route::get('/production-orders', [App\Http\Controllers\Retailer\ProductController::class, 'productionOrdersIndex'])->name('retailer.production-orders.index');

    // New routes for editing and updating retailer orders
    Route::get('/retailer/orders/{id}/edit', [App\Http\Controllers\Retailer\DashboardController::class, 'editOrder'])->name('retailer.orders.edit');
    Route::put('/retailer/orders/{id}', [App\Http\Controllers\Retailer\DashboardController::class, 'updateOrder'])->name('retailer.orders.update');

    // Retailer inventory routes
    Route::get('/retailer/inventory/create', [App\Http\Controllers\Retailer\DashboardController::class, 'createInventory'])->name('retailer.inventory.create');
    Route::post('/retailer/inventory', [App\Http\Controllers\Retailer\DashboardController::class, 'storeInventory'])->name('retailer.inventory.store');
    Route::get('/retailer/inventory/{id}/edit', [App\Http\Controllers\Retailer\DashboardController::class, 'editInventory'])->name('retailer.inventory.edit');
    Route::put('/retailer/inventory/{id}', [App\Http\Controllers\Retailer\DashboardController::class, 'updateInventory'])->name('retailer.inventory.update');
    Route::delete('/retailer/inventory/{id}', [App\Http\Controllers\Retailer\DashboardController::class, 'destroyInventory'])->name('retailer.inventory.destroy');
});

// Inventory Management Routes
Route::prefix('inventory')->middleware(['auth'])->group(function () {
    Route::get('/', [InventoryController::class, 'index'])->name('inventory.dashboard');
    Route::get('/analytics', [InventoryController::class, 'analytics'])->name('inventory.analytics');
    Route::get('/location/{locationType}/{locationId}', [InventoryController::class, 'showLocation'])->name('inventory.location');
    Route::post('/update-stock', [InventoryController::class, 'updateStock'])->name('inventory.update-stock');
});

// Order Management Routes
Route::prefix('orders')->middleware(['auth'])->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('orders.dashboard');
    Route::post('/', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('/{id}/return', [OrderController::class, 'processReturn'])->name('orders.process-return');
    Route::get('/analytics', [OrderController::class, 'analytics'])->name('orders.analytics');
});

// Chat Routes

//Route::prefix('chat')->name('chat.')->middleware('auth')->group(function () {
    //Route::get('/', [ChatController::class, 'index'])->name('index');
    //Route::get('/{conversation}', [ChatController::class, 'show'])->name('show');
    //Route::post('/{conversation}/messages', [ChatController::class, 'store'])->name('store');
    //Route::get('{conversation}/messages/{message}/edit', [ChatController::class, 'editMessage'])->name('message.edit');
    //Route::put('{conversation}/messages/{message}', [ChatController::class, 'updateMessage'])->name('message.update');
    //Route::delete('{conversation}/messages/{message}', [ChatController::class, 'destroyMessage'])->name('message.destroy');
//});

// Analytics Routes
Route::prefix('analytics')->middleware(['auth'])->group(function () {
    Route::get('/', [AnalyticsController::class, 'index'])->name('analytics.dashboard');
    Route::get('/demand-forecasting', [AnalyticsController::class, 'demandForecasting'])->name('analytics.demand-forecasting');
    Route::get('/customer-segmentation', [AnalyticsController::class, 'customerSegmentation'])->name('analytics.customer-segmentation');
    Route::post('/generate-predictions', [AnalyticsController::class, 'generatePredictions'])->name('analytics.generate-predictions');
    Route::post('/generate-segments', [AnalyticsController::class, 'generateSegments'])->name('analytics.generate-segments');
    Route::get('/sales', [AnalyticsController::class, 'salesAnalytics'])->name('analytics.sales');
    Route::get('/supplier', [AnalyticsController::class, 'supplierAnalytics'])->name('analytics.supplier');
});

// Common authenticated routes
Route::group(['middleware' => 'auth'], function () {
    Route::resource('user', UserController::class, ['except' => ['show']]);
    Route::get('profile', ['as' => 'profile.edit', 'uses' => ProfileController::class . '@edit']);
    Route::patch('profile', ['as' => 'profile.update', 'uses' => ProfileController::class . '@update']);
    Route::patch('profile/password', ['as' => 'profile.password', 'uses' => ProfileController::class . '@password']);
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Page routes
Route::group(['middleware' => 'auth'], function () {
    Route::get('{page}', ['as' => 'page.index', 'uses' => PageController::class . '@index']);
});

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/users/{roleName}', [App\Http\Controllers\Admin\DashboardController::class, 'usersByRole'])->name('admin.users-by-role');
    Route::get('/system-overview', [App\Http\Controllers\Admin\DashboardController::class, 'systemOverview'])->name('admin.system-overview');
    Route::get('/supply-chain-monitoring', [App\Http\Controllers\Admin\DashboardController::class, 'supplyChainMonitoring'])->name('admin.supply-chain-monitoring');
    Route::resource('user', UserController::class)->except(['create', 'store']);
    Route::post('/impersonate', [App\Http\Controllers\Admin\DashboardController::class, 'impersonate'])->name('admin.impersonate');
    Route::post('/stop-impersonate', [App\Http\Controllers\Admin\DashboardController::class, 'stopImpersonate'])->name('admin.stopImpersonate');
});

Route::get('/admin/stop-impersonate', function () {
    return redirect('/admin/dashboard');
});

// Supplier Routes
Route::prefix('supplier')->middleware(['auth', 'role:supplier,raw_material_supplier'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Supplier\DashboardController::class, 'index'])->name('supplier.dashboard');
    
    // Material Catalog Management
    Route::resource('materials', \App\Http\Controllers\Supplier\MaterialController::class, ['as' => 'supplier']);
    
    // Stock Management
    Route::get('materials/{material}/stock', [\App\Http\Controllers\Supplier\MaterialController::class, 'showStockForm'])->name('supplier.materials.stock.edit');
    Route::post('materials/{material}/stock', [\App\Http\Controllers\Supplier\MaterialController::class, 'updateStock'])->name('supplier.materials.stock.update');
    
    // Order Management
    Route::get('orders', [\App\Http\Controllers\Supplier\OrderController::class, 'index'])->name('supplier.orders.index');
    Route::get('orders/{order}', [\App\Http\Controllers\Supplier\OrderController::class, 'show'])->name('supplier.orders.show');
    Route::post('orders/{order}/status', [\App\Http\Controllers\Supplier\OrderController::class, 'updateStatus'])->name('supplier.orders.updateStatus');
    Route::get('orders/{order}/assign-delivery', [App\Http\Controllers\Supplier\OrderController::class, 'assignDeliveryPersonnel'])->name('supplier.orders.assignDelivery');
    Route::post('orders/{order}/ship', [App\Http\Controllers\Supplier\OrderController::class, 'shipOrder'])->name('supplier.orders.ship');
    
    // Deliveries Management
    Route::get('deliveries', [\App\Http\Controllers\Supplier\DeliveryController::class, 'index'])->name('supplier.deliveries.index');
    Route::get('deliveries/{delivery}', [\App\Http\Controllers\Supplier\DeliveryController::class, 'show'])->name('supplier.deliveries.show');
    Route::post('deliveries/{delivery}/status', [\App\Http\Controllers\Supplier\DeliveryController::class, 'updateStatus'])->name('supplier.deliveries.updateStatus');
    Route::put('supplier/deliveries/{delivery}/cancel', [App\Http\Controllers\Supplier\OrderController::class, 'cancelDelivery'])->name('supplier.deliveries.cancel');
    
    // Manufacturers
    Route::get('/manufacturers', [App\Http\Controllers\Supplier\DashboardController::class, 'manufacturers'])->name('supplier.manufacturers');
    
    // Analytics
    Route::get('/analytics', [App\Http\Controllers\Supplier\DashboardController::class, 'analytics'])->name('supplier.analytics');
    
    // Profile Management
    Route::get('/profile', [App\Http\Controllers\Supplier\DashboardController::class, 'profile'])->name('supplier.profile');
    Route::put('/profile', [App\Http\Controllers\Supplier\DashboardController::class, 'updateProfile'])->name('supplier.profile.update');
    Route::get('/supplier/profile/create', [\App\Http\Controllers\Supplier\ProfileController::class, 'create'])
        ->name('supplier.profile.create')
        ->middleware(['auth', 'role:supplier,raw_material_supplier']);
    Route::post('/supplier/profile/create', [\App\Http\Controllers\Supplier\ProfileController::class, 'store'])
        ->name('supplier.profile.store')
        ->middleware(['auth', 'role:supplier,raw_material_supplier']);
    Route::get('/supplier/profile/edit', [\App\Http\Controllers\Supplier\ProfileController::class, 'edit'])
        ->name('supplier.profile.edit')
        ->middleware(['auth', 'role:supplier,raw_material_supplier']);
    Route::delete('/supplier/profile/destroy', [\App\Http\Controllers\Supplier\ProfileController::class, 'destroy'])
        ->name('supplier.profile.destroy')
        ->middleware(['auth', 'role:supplier,raw_material_supplier']);
    Route::put('/supplier/profile/update', [\App\Http\Controllers\Supplier\ProfileController::class, 'update'])
        ->name('supplier.profile.update')
        ->middleware(['auth', 'role:supplier,raw_material_supplier']);
    Route::get('/supplier/reports', function () {
        return view('supplier.reports.index');
    })->name('supplier.reports')->middleware(['auth', 'role:supplier,raw_material_supplier']);
    Route::get('/supplier/payments', function () {
        return view('supplier.payments.index');
    })->name('supplier.payments')->middleware(['auth', 'role:supplier,raw_material_supplier']);
});

// Real-time and backend routes for all dashboards
Route::middleware('auth')->group(function () {
    Route::resource('orders', App\Http\Controllers\OrderController::class);
    Route::post('chat/send', [App\Http\Controllers\ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('chat/messages/{userId}', [App\Http\Controllers\ChatController::class, 'fetchMessages'])->name('chat.fetch');
    Route::get('notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
});

// Product Management Routes
Route::prefix('products')->middleware(['auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
    Route::get('/create', [App\Http\Controllers\ProductController::class, 'create'])->name('products.create');
    Route::post('/', [App\Http\Controllers\ProductController::class, 'store'])->name('products.store');
    Route::get('/{id}', [App\Http\Controllers\ProductController::class, 'show'])->name('products.show');
    Route::get('/{id}/edit', [App\Http\Controllers\ProductController::class, 'edit'])->name('products.edit');
    Route::put('/{id}', [App\Http\Controllers\ProductController::class, 'update'])->name('products.update');
    Route::delete('/{id}', [App\Http\Controllers\ProductController::class, 'destroy'])->name('products.destroy');
    Route::patch('/{id}/toggle-status', [App\Http\Controllers\ProductController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::get('/{id}/analytics', [App\Http\Controllers\ProductController::class, 'analytics'])->name('products.analytics');
    Route::post('/bulk-action', [App\Http\Controllers\ProductController::class, 'bulkAction'])->name('products.bulk-action');
});

// Debug route for troubleshooting
Route::get('/debug-auth', function () {
    if (auth()->check()) {
        $user = auth()->user();
        dd([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'role_id' => $user->role_id,
            'role' => $user->role ? $user->role->name : 'No role',
            'session_id' => session()->getId(),
            'is_authenticated' => auth()->check(),
        ]);
    } else {
        dd('Not authenticated');
    }
})->middleware('auth');

// Supplier Order Resource Route
Route::resource('supplier/orders', App\Http\Controllers\Supplier\OrderController::class)->names('supplier.orders');

Route::get('/chat/dashboard', function () {
    return view('chat.dashboard');
})->name('chat.dashboard')->middleware(['auth', 'role:admin']);

// Temporary debug route to inspect products
Route::get('/debug/products', function () {
    return \App\Models\Product::orderBy('id', 'desc')->limit(10)->get(['id','name','manufacturer_id','supplier_id','is_active']);
});

Route::get('/admin/supplier-dashboard/{supplier_id}', [\App\Http\Controllers\Admin\DashboardController::class, 'viewSupplierDashboard'])->middleware('auth');

Route::get('/admin/ml-analytics', [MLAnalyticsController::class, 'adminAnalytics'])->middleware('auth');
Route::get('/user/recommendations', [MLAnalyticsController::class, 'userRecommendations'])->middleware('auth');

Route::get('/admin/analytics-dashboard', [AnalyticsController::class, 'dashboard'])->middleware('auth');
Route::get('/admin/analytics-dashboard/export/csv', [AnalyticsController::class, 'exportCsv'])->middleware('auth');
Route::get('/admin/analytics-dashboard/export/excel', [AnalyticsController::class, 'exportExcel'])->middleware('auth');
Route::get('/admin/analytics-dashboard/export/pdf', [AnalyticsController::class, 'exportPdf'])->middleware('auth');


Route::middleware(['auth'])->group(function () {
    Route::get('/chat/contacts', [ChatController::class, 'contacts'])->name('chat.contacts');
    Route::get('/chat/messages/{other}', [ChatController::class, 'messages'])->name('chat.messages');
    Route::post('/chat/send/{other}', [ChatController::class, 'send'])->name('chat.send');
    Route::post('/chat/mark-as-read/{other}', [ChatController::class, 'markAsRead'])->name('chat.markAsRead');
    Route::post('/chat/typing/{other}', [ChatController::class, 'typing'])->name('chat.typing');
    Route::get('/chat/notifications', [ChatController::class, 'notifications'])->name('chat.notifications');
});

Route::view('/privacy-policy', 'pages.privacy-policy')->name('privacy.policy');
Route::view('/terms-of-service', 'pages.terms-of-service')->name('terms.service');


