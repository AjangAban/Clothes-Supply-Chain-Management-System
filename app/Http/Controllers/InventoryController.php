<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\RetailStore;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display inventory dashboard
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Inventory::with(['product', 'warehouse', 'retailStore']);

        // Filter based on user role
        switch ($user->role->name) {
            case 'retailer':
                $query->where('retail_store_id', $user->retailStore->id ?? 0);
                break;
            case 'manufacturer':
                $query->whereHas('product', function ($q) use ($user) {
                    $q->where('manufacturer_id', $user->id);
                });
                break;
        }

        // Apply filters
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('location_type')) {
            $query->where('location_type', $request->location_type);
        }

        if ($request->filled('low_stock')) {
            $query->where('quantity', '<=', DB::raw('min_stock_level'));
        }

        $inventories = $query->paginate(20);
        $products = Product::active()->get();
        $warehouses = Warehouse::all();
        $retailStores = RetailStore::all();

        return view('inventory.index', compact('inventories', 'products', 'warehouses', 'retailStores'));
    }

    /**
     * Show inventory analytics
     */
    public function analytics()
    {
        $user = Auth::user();
        $query = Inventory::with('product');

        // Filter based on user role
        switch ($user->role->name) {
            case 'retailer':
                $query->where('retail_store_id', $user->retailStore->id ?? 0);
                break;
        }

        // Analytics data
        $totalProducts = $query->count();
        $totalValue = $query->join('products', 'inventories.product_id', '=', 'products.id')
            ->selectRaw('SUM(inventories.quantity * products.cost) as total_value')
            ->value('total_value') ?? 0;

        $lowStockItems = $query->where('quantity', '<=', DB::raw('min_stock_level'))->count();
        $outOfStockItems = $query->where('quantity', 0)->count();

        // Top products by value
        $topProductsByValue = $query->join('products', 'inventories.product_id', '=', 'products.id')
            ->select('products.name', 'products.sku', DB::raw('SUM(inventories.quantity * products.cost) as total_value'))
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('total_value')
            ->limit(10)
            ->get();

        // Stock levels by category
        $stockByCategory = $query->join('products', 'inventories.product_id', '=', 'products.id')
            ->select('products.category', DB::raw('SUM(inventories.quantity) as total_quantity'))
            ->groupBy('products.category')
            ->get();

        return view('inventory.analytics', compact(
            'totalProducts',
            'totalValue',
            'lowStockItems',
            'outOfStockItems',
            'topProductsByValue',
            'stockByCategory'
        ));
    }

    /**
     * Show inventory for specific location
     */
    public function showLocation($locationType, $locationId)
    {
        $query = Inventory::with('product')->where('location_type', $locationType);

        switch ($locationType) {
            case 'warehouse':
                $location = Warehouse::findOrFail($locationId);
                $query->where('warehouse_id', $locationId);
                break;
            case 'retail_store':
                $location = RetailStore::findOrFail($locationId);
                $query->where('retail_store_id', $locationId);
                break;
            default:
                abort(404);
        }

        $inventories = $query->paginate(20);

        return view('inventory.location', compact('inventories', 'location', 'locationType'));
    }

    /**
     * Update stock levels
     */
    public function updateStock(Request $request)
    {
        $request->validate([
            'inventory_id' => 'required|exists:inventories,id',
            'quantity' => 'required|integer|min:0',
            'operation' => 'required|in:add,subtract,set',
            'notes' => 'nullable|string'
        ]);

        $inventory = Inventory::findOrFail($request->inventory_id);
        $oldQuantity = $inventory->quantity;

        switch ($request->operation) {
            case 'add':
                $inventory->quantity += $request->quantity;
                break;
            case 'subtract':
                if ($inventory->quantity < $request->quantity) {
                    return back()->with('error', 'Cannot subtract more than available stock.');
                }
                $inventory->quantity -= $request->quantity;
                break;
            case 'set':
                $inventory->quantity = $request->quantity;
                break;
        }

        $inventory->last_restocked = now();
        $inventory->save();

        // Log the stock movement
        $this->logStockMovement($inventory, $oldQuantity, $inventory->quantity, $request->operation, $request->notes);

        return back()->with('success', 'Stock updated successfully!');
    }

    /**
     * Create new inventory record
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'location_type' => 'required|in:warehouse,retail_store',
            'location_id' => 'required|integer',
            'quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'max_stock_level' => 'nullable|integer|min:0',
            'location_code' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        // Check if inventory already exists for this product and location
        $existingInventory = Inventory::where('product_id', $request->product_id)
            ->where('location_type', $request->location_type)
            ->where($request->location_type . '_id', $request->location_id)
            ->first();

        if ($existingInventory) {
            return back()->with('error', 'Inventory record already exists for this product and location.');
        }

        Inventory::create([
            'product_id' => $request->product_id,
            'location_type' => $request->location_type,
            'warehouse_id' => $request->location_type === 'warehouse' ? $request->location_id : null,
            'retail_store_id' => $request->location_type === 'retail_store' ? $request->location_id : null,
            'quantity' => $request->quantity,
            'min_stock_level' => $request->min_stock_level,
            'max_stock_level' => $request->max_stock_level,
            'location' => $request->location_code,
            'last_restocked' => now(),
            'is_active' => true,
        ]);

        return back()->with('success', 'Inventory record created successfully!');
    }

    /**
     * Update inventory record
     */
    public function update(Request $request, $id)
    {
        $inventory = Inventory::findOrFail($id);

        $request->validate([
            'quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'max_stock_level' => 'nullable|integer|min:0',
            'location' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $inventory->update($request->only([
            'quantity', 'min_stock_level', 'max_stock_level', 'location', 'is_active'
        ]));

        return back()->with('success', 'Inventory updated successfully!');
    }

    /**
     * Delete inventory record
     */
    public function destroy($id)
    {
        $inventory = Inventory::findOrFail($id);
        
        if ($inventory->quantity > 0) {
            return back()->with('error', 'Cannot delete inventory with stock remaining.');
        }

        $inventory->delete();

        return back()->with('success', 'Inventory record deleted successfully!');
    }

    /**
     * Bulk stock update
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'updates' => 'required|array|min:1',
            'updates.*.inventory_id' => 'required|exists:inventories,id',
            'updates.*.quantity' => 'required|integer|min:0',
            'updates.*.operation' => 'required|in:add,subtract,set'
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->updates as $update) {
                $inventory = Inventory::find($update['inventory_id']);
                $oldQuantity = $inventory->quantity;

                switch ($update['operation']) {
                    case 'add':
                        $inventory->quantity += $update['quantity'];
                        break;
                    case 'subtract':
                        if ($inventory->quantity < $update['quantity']) {
                            throw new \Exception("Cannot subtract more than available stock for product {$inventory->product->name}");
                        }
                        $inventory->quantity -= $update['quantity'];
                        break;
                    case 'set':
                        $inventory->quantity = $update['quantity'];
                        break;
                }

                $inventory->last_restocked = now();
                $inventory->save();

                $this->logStockMovement($inventory, $oldQuantity, $inventory->quantity, $update['operation'], 'Bulk update');
            }

            DB::commit();
            return back()->with('success', 'Bulk stock update completed successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Bulk update failed: ' . $e->getMessage());
        }
    }

    /**
     * Get low stock alerts
     */
    public function lowStockAlerts()
    {
        $user = Auth::user();
        $query = Inventory::with('product')->where('quantity', '<=', DB::raw('min_stock_level'));

        // Filter based on user role
        switch ($user->role->name) {
            case 'retailer':
                $query->where('retail_store_id', $user->retailStore->id ?? 0);
                break;
        }

        $lowStockItems = $query->get();

        return response()->json([
            'count' => $lowStockItems->count(),
            'items' => $lowStockItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_name' => $item->product->name,
                    'sku' => $item->product->sku,
                    'current_quantity' => $item->quantity,
                    'min_stock_level' => $item->min_stock_level,
                    'location' => $item->location
                ];
            })
        ]);
    }

    /**
     * Log stock movement
     */
    private function logStockMovement($inventory, $oldQuantity, $newQuantity, $operation, $notes = null)
    {
        // This would typically log to a stock_movements table
        // For now, we'll just update the last_restocked timestamp
        $inventory->last_restocked = now();
        $inventory->save();
    }
}
