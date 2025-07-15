<?php

namespace App\Http\Controllers\Manufacturer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupplyCenter;

class SupplyCenterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $manufacturer = auth()->user()->manufacturer;
        $supplyCenters = $manufacturer ? SupplyCenter::where('manufacturer_id', $manufacturer->id)->get() : collect();
        return view('manufacturer.supply_centers.index', compact('supplyCenters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('manufacturer.supply_centers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
        ]);
        $manufacturer = auth()->user()->manufacturer;
        if (!$manufacturer) {
            return redirect()->back()->with('error', 'Manufacturer profile not found. Please complete your manufacturer profile first.');
        }
        SupplyCenter::create([
            'name' => $request->name,
            'location' => $request->location,
            'manufacturer_id' => $manufacturer->id,
        ]);
        return redirect()->route('supply-centers.index')->with('success', 'Supply center created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $supplyCenter = SupplyCenter::findOrFail($id);
        return view('manufacturer.supply_centers.edit', compact('supplyCenter'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
        ]);
        $supplyCenter = SupplyCenter::findOrFail($id);
        $supplyCenter->update($request->only(['name', 'location']));
        return redirect()->route('supply-centers.index')->with('success', 'Supply center updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $supplyCenter = SupplyCenter::findOrFail($id);
        $supplyCenter->delete();
        return redirect()->route('supply-centers.index')->with('success', 'Supply center deleted successfully.');
    }
}
