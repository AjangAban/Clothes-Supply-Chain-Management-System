<?php

namespace App\Http\Controllers\Manufacturer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\SupplyCenter;
use App\Models\Worker;
use Illuminate\Support\Carbon;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::with('center')->get();
        return view('manufacturer.tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $centers = SupplyCenter::all();
        return view('manufacturer.tasks.create', compact('centers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'required_skill' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'center_id' => 'required|exists:supply_centers,id',
        ]);
        Task::create($request->only(['name', 'required_skill', 'quantity', 'center_id']));
        return redirect()->route('tasks.index')->with('success', 'Task added successfully.');
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
        $task = Task::findOrFail($id);
        $centers = SupplyCenter::all();
        return view('manufacturer.tasks.edit', compact('task', 'centers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'required_skill' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'center_id' => 'required|exists:supply_centers,id',
        ]);
        $task = Task::findOrFail($id);
        $task->update($request->only(['name', 'required_skill', 'quantity', 'center_id']));
        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }

    // Manual assignment view and logic
    public function assign(Request $request, $taskId)
    {
        $task = Task::findOrFail($taskId);
        $availableWorkers = Worker::where('skill', $task->required_skill)
            ->where('status', 'available')
            ->where('shift', $request->input('shift', 'Morning'))
            ->get();
        if ($request->isMethod('post') && $request->has('worker_ids')) {
            $workerIds = $request->input('worker_ids', []);
            $assigned = 0;
            foreach ($workerIds as $workerId) {
                $worker = Worker::find($workerId);
                if ($worker && $worker->status === 'available' && $worker->skill === $task->required_skill) {
                    $worker->status = 'assigned';
                    $worker->save();
                    $assigned++;
                }
            }
            $task->assigned_count += $assigned;
            $task->save();
            return redirect()->route('tasks.index')->with('success', 'Workers assigned successfully.');
        }
        return view('manufacturer.tasks.assign', compact('task', 'availableWorkers'));
    }

    // Auto-assign: results table is per worker, showing assignment
    public function autoAssign(Request $request)
    {
        $tasks = Task::with('center')->get();
        $workers = Worker::all();
        $taskAssignments = [];
        // Track how many workers are assigned to each task
        $taskSlots = [];
        foreach ($tasks as $task) {
            $taskSlots[$task->id] = [
                'remaining' => $task->quantity,
                'task' => $task,
            ];
        }
        $results = [];
        // Track how many workers are assigned to each task in this run
        $assignedCounts = array_fill_keys($tasks->pluck('id')->all(), 0);
        foreach ($workers as $worker) {
            $assignedTask = null;
            // Try to assign to a task matching skill and shift, with available slots
            foreach ($taskSlots as $taskId => $slot) {
                $task = $slot['task'];
                if (
                    $slot['remaining'] > 0 &&
                    $worker->skill === $task->required_skill &&
                    $worker->shift === ($task->shift ?? $worker->shift)
                ) {
                    $assignedTask = $task;
                    $taskSlots[$taskId]['remaining']--;
                    $assignedCounts[$taskId]++;
                    break;
                }
            }
            $results[] = [
                'worker' => $worker->name,
                'skill' => $worker->skill,
                'task' => $assignedTask ? $assignedTask->name : 'Unassigned',
                'shift' => $worker->shift,
                'center' => $assignedTask && $assignedTask->center ? $assignedTask->center->name : ($assignedTask ? 'N/A' : ''),
                'status' => $assignedTask ? 'Assigned' : 'Unassigned',
            ];
            // Optionally update worker status in DB
            $worker->status = $assignedTask ? 'assigned' : 'available';
            $worker->save();
        }
        // Update assigned_count for each task based on in-memory count
        foreach ($tasks as $task) {
            $task->assigned_count = $assignedCounts[$task->id] ?? 0;
            $task->save();
        }
        return view('manufacturer.auto_assign_results', compact('results'));
    }

    // Workforce distribution report
    public function report(Request $request)
    {
        $shift = $request->input('shift', 'Morning');
        $date = $request->input('date', Carbon::now()->toDateString());
        $centers = SupplyCenter::all();
        $tasks = Task::with('center')->get();
        $report = [];
        foreach ($centers as $center) {
            $centerTasks = $tasks->where('center_id', $center->id);
            foreach ($centerTasks as $task) {
                $assignedWorkers = $task->assigned_count;
                $report[] = [
                    'center' => $center->name,
                    'task' => $task->name,
                    'skill' => $task->required_skill,
                    'assigned_workers' => $assignedWorkers,
                ];
            }
        }
        return view('manufacturer.tasks.report', compact('report', 'shift', 'date'));
    }
}
