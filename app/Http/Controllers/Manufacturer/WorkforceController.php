<?php

namespace App\Http\Controllers\Manufacturer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Worker;
use App\Models\Shift;
use App\Models\WorkerAssignment;
use App\Models\SupplyCenter;

class WorkforceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $manufacturerId = auth()->user()->manufacturer_id;
        $workers = Worker::where('manufacturer_id', $manufacturerId)->get();
        $shifts = Shift::where('manufacturer_id', $manufacturerId)->get();
        $assignments = WorkerAssignment::whereIn('worker_id', $workers->pluck('id'))->get();
        return view('manufacturer.workforce.index', compact('workers', 'shifts', 'assignments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $manufacturerId = auth()->user()->manufacturer_id;
        $supplyCenters = SupplyCenter::where('manufacturer_id', $manufacturerId)->get();
        return view('manufacturer.workforce.create', compact('supplyCenters'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'skill' => 'required|string|max:255',
            'shift' => 'required|in:Morning,Evening',
            'status' => 'required|in:available,assigned',
        ]);
        Worker::create([
            'name' => $request->name,
            'skill' => $request->skill,
            'shift' => $request->shift,
            'status' => $request->status,
            'manufacturer_id' => auth()->user()->manufacturer->id,
        ]);
        return redirect()->route('workforce.index')->with('success', 'Worker added successfully.');
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
        $worker = Worker::findOrFail($id);
        $manufacturerId = auth()->user()->manufacturer_id;
        $supplyCenters = SupplyCenter::where('manufacturer_id', $manufacturerId)->get();
        return view('manufacturer.workforce.edit', compact('worker', 'supplyCenters'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'skill' => 'required|string|max:255',
            'shift' => 'required|in:Morning,Evening',
            'status' => 'required|in:available,assigned',
        ]);
        $worker = Worker::findOrFail($id);
        $worker->update($request->only(['name', 'skill', 'shift', 'status']));
        return redirect()->route('workforce.index')->with('success', 'Worker updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $worker = Worker::findOrFail($id);
        $worker->delete();
        return redirect()->route('workforce.index')->with('success', 'Worker deleted successfully.');
    }

    // Auto-assign workers to day/evening shifts for a supply center
    public function autoAssign(Request $request)
    {
        $request->validate([
            'supply_center_id' => 'required|exists:supply_centers,id',
        ]);
        $supplyCenter = SupplyCenter::findOrFail($request->supply_center_id);
        $workers = $supplyCenter->workers()->get();
        $shifts = $supplyCenter->shifts()->whereIn('name', ['Day', 'Evening'])->get();
        if ($shifts->count() < 2) {
            return back()->with('error', 'Both Day and Evening shifts must exist for auto-assign.');
        }
        $dayShift = $shifts->where('name', 'Day')->first();
        $eveningShift = $shifts->where('name', 'Evening')->first();
        $half = ceil($workers->count() / 2);
        $i = 0;
        foreach ($workers as $worker) {
            $shiftId = ($i < $half) ? $dayShift->id : $eveningShift->id;
            \App\Models\WorkerAssignment::updateOrCreate(
                ['worker_id' => $worker->id],
                ['shift_id' => $shiftId, 'status' => 'assigned']
            );
            $i++;
        }
        return back()->with('success', 'Workers auto-assigned to shifts successfully.');
    }
}
