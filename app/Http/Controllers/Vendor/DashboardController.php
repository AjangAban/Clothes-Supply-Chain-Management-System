<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\VendorApplication;
use App\Models\Product;
use App\Models\FacilityVisit;
use App\Models\User;
use App\Models\Design;
use App\Models\Sample;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show vendor dashboard
     */
    public function index()
    {
        $user = auth()->user();
        $vendor = $user->vendor;
        
        if (!$vendor) {
            return redirect()->route('vendor.profile.create')->with('error', 'Please complete your vendor profile first.');
        }

        // Statistics
        $stats = [
            'total_products' => Product::where('vendor_id', $vendor->id)->count(),
            'active_products' => Product::where('vendor_id', $vendor->id)->where('is_active', true)->count(),
            'pending_applications' => VendorApplication::where('vendor_id', $vendor->id)
                ->where('status', 'pending')->count(),
            'approved_applications' => VendorApplication::where('vendor_id', $vendor->id)
                ->where('status', 'approved')->count(),
            'total_designs' => Design::where('vendor_id', $vendor->id)->count(),
            'total_samples' => Sample::where('vendor_id', $vendor->id)->count(),
            'upcoming_visits' => FacilityVisit::where('vendor_id', $vendor->id)
                ->where('scheduled_date', '>=', now())->count(),
        ];

        // Recent applications
        $recentApplications = VendorApplication::where('vendor_id', $vendor->id)
            ->latest()
            ->take(5)
            ->get();

        // Upcoming facility visits
        $upcomingVisits = FacilityVisit::where('vendor_id', $vendor->id)
            ->where('scheduled_date', '>=', now())
            ->with(['inspector'])
            ->orderBy('scheduled_date')
            ->take(5)
            ->get();

        // Recent products
        $recentProducts = Product::where('vendor_id', $vendor->id)
            ->with(['design'])
            ->latest()
            ->take(5)
            ->get();

        // Application status distribution
        $applicationStats = VendorApplication::where('vendor_id', $vendor->id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        // Add these lines:
        $latestApplication = $vendor->applications()->latest()->first();
        $latestVisit = $vendor->facilityVisits()->latest('scheduled_date')->first();

        return view('vendor.dashboard', compact('stats', 'recentApplications', 'upcomingVisits', 'recentProducts', 'applicationStats', 'vendor', 'latestApplication', 'latestVisit'));
    }

    /**
     * Show products management
     */
    public function products(Request $request)
    {
        $user = auth()->user();
        $vendor = $user->vendor;
        
        $query = Product::where('vendor_id', $vendor->id)
            ->with(['design']);

        // Filter by status
        if ($request->status) {
            switch ($request->status) {
                case 'active':
                    $query->where('is_active', true);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
            }
        }

        // Filter by category
        if ($request->category) {
            $query->where('category', $request->category);
        }

        // Search by name
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->paginate(15);

        return view('vendor.products.index', compact('products'));
    }

    /**
     * Show product details
     */
    public function showProduct($id)
    {
        $user = auth()->user();
        $vendor = $user->vendor;
        
        $product = Product::where('vendor_id', $vendor->id)
            ->with(['design', 'samples'])
            ->findOrFail($id);

        return view('vendor.products.show', compact('product'));
    }

    /**
     * Create new product
     */
    public function createProduct()
    {
        $user = auth()->user();
        $vendor = $user->vendor;
        
        $designs = Design::where('vendor_id', $vendor->id)->get();

        return view('vendor.products.create', compact('designs'));
    }

    /**
     * Store new product
     */
    public function storeProduct(Request $request)
    {
        $user = auth()->user();
        $vendor = $user->vendor;
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'material' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'design_id' => 'nullable|exists:designs,id',
            'minimum_order' => 'required|integer|min:1',
            'lead_time' => 'required|integer|min:1',
        ]);

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'material' => $request->material,
            'price' => $request->price,
            'cost' => $request->cost,
            'category' => $request->category,
            'unit' => $request->unit,
            'sku' => 'VEND-' . time(),
            'vendor_id' => $vendor->id,
            'design_id' => $request->design_id,
            'is_active' => true,
        ]);

        return redirect()->route('vendor.products.show', $product->id)
            ->with('success', 'Product created successfully!');
    }

    /**
     * Update product
     */
    public function updateProduct(Request $request, $id)
    {
        $user = auth()->user();
        $vendor = $user->vendor;
        
        $product = Product::where('vendor_id', $vendor->id)->findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'minimum_order' => 'required|integer|min:1',
            'lead_time' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $product->update($request->only([
            'name', 'description', 'price', 'cost', 
            'minimum_order', 'lead_time', 'is_active'
        ]));

        return redirect()->route('vendor.products.show', $product->id)
            ->with('success', 'Product updated successfully!');
    }

    /**
     * Show applications
     */
    public function applications(Request $request)
    {
        $user = auth()->user();
        $vendor = $user->vendor;
        
        $query = VendorApplication::where('vendor_id', $vendor->id)
            ->with(['product']);

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $applications = $query->latest()->paginate(15);

        return view('vendor.applications.index', compact('applications'));
    }

    /**
     * Show application details
     */
    public function showApplication($id)
    {
        $user = auth()->user();
        $vendor = $user->vendor;
        
        $application = VendorApplication::where('vendor_id', $vendor->id)
            ->findOrFail($id);

        return view('vendor.applications.show', compact('application'));
    }

    /**
     * Create new application
     */
    public function createApplication()
    {
        $user = auth()->user();
        $vendor = $user->vendor;
        
        $products = Product::where('vendor_id', $vendor->id)->where('is_active', true)->get();

        return view('vendor.applications.create', compact('products'));
    }

    /**
     * Store new application
     */
    public function storeApplication(Request $request)
    {
        $user = auth()->user();
        $vendor = $user->vendor;
        
        $request->validate([
            'pdf' => 'required|file|mimes:pdf|max:10240', // 10MB max
        ]);

        // Store the PDF
        $file = $request->file('pdf');
        $pdfPath = $file->store('vendor_applications', 'public');
        $originalFilename = $file->getClientOriginalName();

        $application = \App\Models\VendorApplication::create([
            'vendor_id' => $vendor->id,
            'pdf_path' => $pdfPath,
            'original_filename' => $originalFilename,
            'status' => 'pending',
        ]);

        // Dispatch validation job
        \App\Jobs\ProcessVendorValidation::dispatch($application->id);

        return redirect()->route('vendor.dashboard')
            ->with('success', 'Application submitted successfully!');
    }

    /**
     * Update application
     */
    public function updateApplication(Request $request, $id)
    {
        $user = auth()->user();
        $vendor = $user->vendor;
        
        $application = VendorApplication::where('vendor_id', $vendor->id)->findOrFail($id);
        
        $request->validate([
            'description' => 'required|string',
            'expected_volume' => 'required|integer|min:1',
            'target_price' => 'required|numeric|min:0',
            'timeline' => 'required|string',
            'additional_notes' => 'nullable|string',
        ]);

        $application->update($request->only([
            'description', 'expected_volume', 'target_price', 
            'timeline', 'additional_notes'
        ]));

        return redirect()->route('vendor.applications.show', $application->id)
            ->with('success', 'Application updated successfully!');
    }

    /**
     * Show facility visits
     */
    public function facilityVisits(Request $request)
    {
        $user = auth()->user();
        $vendor = $user->vendor;
        
        $query = FacilityVisit::where('vendor_id', $vendor->id)
            ->with(['inspector']);

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->date_from) {
            $query->whereDate('scheduled_date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('scheduled_date', '<=', $request->date_to);
        }

        $visits = $query->latest()->paginate(15);

        return view('vendor.facility-visits.index', compact('visits'));
    }

    /**
     * Show facility visit details
     */
    public function showFacilityVisit($id)
    {
        $user = auth()->user();
        $vendor = $user->vendor;
        
        $visit = FacilityVisit::where('vendor_id', $vendor->id)
            ->with(['inspector', 'vendorApplication.product'])
            ->findOrFail($id);

        return view('vendor.facility-visits.show', compact('visit'));
    }

    /**
     * Update facility visit
     */
    public function updateFacilityVisit(Request $request, $id)
    {
        $user = auth()->user();
        $vendor = $user->vendor;
        
        $visit = FacilityVisit::where('vendor_id', $vendor->id)->findOrFail($id);
        
        $request->validate([
            'vendor_notes' => 'nullable|string',
            'preparation_status' => 'required|in:not_started,in_progress,completed',
        ]);

        $visit->update([
            'vendor_notes' => $request->vendor_notes,
            'preparation_status' => $request->preparation_status,
        ]);

        return redirect()->route('vendor.facility-visits.show', $visit->id)
            ->with('success', 'Facility visit updated successfully!');
    }

    /**
     * Show designs
     */
    public function designs(Request $request)
    {
        $user = auth()->user();
        $vendor = $user->vendor;
        
        $query = Design::where('vendor_id', $vendor->id);

        // Filter by status
        if ($request->status) {
            switch ($request->status) {
                case 'active':
                    $query->where('is_active', true);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
            }
        }

        // Filter by category
        if ($request->category) {
            $query->where('category', $request->category);
        }

        $designs = $query->latest()->paginate(15);

        return view('vendor.designs.index', compact('designs'));
    }

    /**
     * Show design details
     */
    public function showDesign($id)
    {
        $user = auth()->user();
        $vendor = $user->vendor;
        
        $design = Design::where('vendor_id', $vendor->id)
            ->with(['products', 'collection'])
            ->findOrFail($id);

        return view('vendor.designs.show', compact('design'));
    }

    /**
     * Create new design
     */
    public function createDesign()
    {
        return view('vendor.designs.create');
    }

    /**
     * Store new design
     */
    public function storeDesign(Request $request)
    {
        $user = auth()->user();
        $vendor = $user->vendor;
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:255',
            'target_gender' => 'required|in:male,female,unisex',
            'season' => 'required|string|max:255',
            'design_specifications' => 'nullable|string',
        ]);

        $design = Design::create([
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'target_gender' => $request->target_gender,
            'season' => $request->season,
            'design_specifications' => $request->design_specifications,
            'design_code' => 'DES-' . time(),
            'vendor_id' => $vendor->id,
            'is_active' => true,
        ]);

        return redirect()->route('vendor.designs.show', $design->id)
            ->with('success', 'Design created successfully!');
    }

    /**
     * Show samples
     */
    public function samples(Request $request)
    {
        $user = auth()->user();
        $vendor = $user->vendor;
        
        $query = Sample::where('vendor_id', $vendor->id)
            ->with(['product', 'design']);

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $samples = $query->latest()->paginate(15);

        return view('vendor.samples.index', compact('samples'));
    }

    /**
     * Show sample details
     */
    public function showSample($id)
    {
        $user = auth()->user();
        $vendor = $user->vendor;
        
        $sample = Sample::where('vendor_id', $vendor->id)
            ->with(['product', 'design'])
            ->findOrFail($id);

        return view('vendor.samples.show', compact('sample'));
    }

    /**
     * Create new sample
     */
    public function createSample()
    {
        $user = auth()->user();
        $vendor = $user->vendor;
        
        $products = Product::where('vendor_id', $vendor->id)->get();
        $designs = Design::where('vendor_id', $vendor->id)->get();

        return view('vendor.samples.create', compact('products', 'designs'));
    }

    /**
     * Store new sample
     */
    public function storeSample(Request $request)
    {
        $user = auth()->user();
        $vendor = $user->vendor;
        
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'design_id' => 'nullable|exists:designs,id',
            'sample_type' => 'required|in:prototype,production_sample,quality_sample',
            'description' => 'required|string',
            'specifications' => 'nullable|string',
            'sample_date' => 'required|date',
        ]);

        $sample = Sample::create([
            'product_id' => $request->product_id,
            'design_id' => $request->design_id,
            'sample_type' => $request->sample_type,
            'description' => $request->description,
            'specifications' => $request->specifications,
            'sample_date' => $request->sample_date,
            'sample_code' => 'SMP-' . time(),
            'vendor_id' => $vendor->id,
            'status' => 'pending_review',
        ]);

        return redirect()->route('vendor.samples.show', $sample->id)
            ->with('success', 'Sample created successfully!');
    }

    /**
     * Show analytics
     */
    public function analytics()
    {
        $user = auth()->user();
        $vendor = $user->vendor;
        
        // Application status distribution
        $applicationStats = VendorApplication::where('vendor_id', $vendor->id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        // Product category distribution
        $productCategories = Product::where('vendor_id', $vendor->id)
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->get();

        // Monthly applications
        $monthlyApplications = VendorApplication::where('vendor_id', $vendor->id)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->get();

        // Facility visit statistics
        $visitStats = FacilityVisit::where('vendor_id', $vendor->id)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        return view('vendor.analytics', compact('applicationStats', 'productCategories', 'monthlyApplications', 'visitStats'));
    }

    /**
     * Show profile
     */
    public function profile()
    {
        $user = auth()->user();
        $vendor = $user->vendor;
        
        return view('vendor.profile.index', compact('user', 'vendor'));
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $vendor = $user->vendor;
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'vendor_name' => 'required|string|max:255',
            'vendor_address' => 'required|string',
            'vendor_phone' => 'required|string|max:20',
            'specializations' => 'nullable|string',
            'certifications' => 'nullable|string',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);
        
        if ($vendor) {
            $vendor->update([
                'name' => $request->vendor_name,
                'address' => $request->vendor_address,
                'phone' => $request->vendor_phone,
                'specializations' => $request->specializations,
                'certifications' => $request->certifications,
            ]);
        }

        return redirect()->route('vendor.profile')->with('success', 'Profile updated successfully!');
    }

    /**
     * Show product offers from manufacturers.
     */
    public function productOffers()
    {
        $user = auth()->user();
        $vendor = $user->vendor;
        $offers = \App\Models\Product::whereNotNull('manufacturer_id')->where('is_active', true)->get();
        return view('vendor.product-offers', compact('offers'));
    }

    /**
     * Show form to place a bulk order.
     */
    public function createBulkOrder()
    {
        $user = auth()->user();
        $vendor = $user->vendor;
        $products = \App\Models\Product::where('vendor_id', $vendor->id)->orWhereNotNull('manufacturer_id')->get();
        return view('vendor.orders.create', compact('products'));
    }

    /**
     * Track orders placed by this vendor.
     */
    public function trackOrders()
    {
        $user = auth()->user();
        $vendor = $user->vendor;
        $orders = \App\Models\Order::where('vendor_id', $vendor->id)->with(['orderItems', 'manufacturer', 'warehouse'])->latest()->get();
        return view('vendor.orders.track', compact('orders'));
    }

    /**
     * List product for sale to retailers.
     */
    public function listProductForRetailers()
    {
        $user = auth()->user();
        $vendor = $user->vendor;
        $products = \App\Models\Product::where('vendor_id', $vendor->id)->get();
        return view('vendor.products.list-for-retailers', compact('products'));
    }

    /**
     * Show chat interface for vendor.
     */
    public function chat()
    {
        $user = auth()->user();
        $vendor = $user->vendor;
        // Fetch conversations with manufacturers, warehouses, retailers
        $conversations = \App\Models\Conversation::where('vendor_id', $vendor->id)->latest()->get();
        return view('vendor.chat', compact('conversations'));
    }
}
