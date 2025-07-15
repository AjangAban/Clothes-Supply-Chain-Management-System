@extends('layouts.app')
@section('content')
<div class="container py-4">
    <a href="{{ route('workforce.index') }}" class="btn btn-primary mb-3 me-2">Workers</a>
    <a href="{{ route('tasks.index') }}" class="btn btn-info mb-3 ms-2 disabled">Tasks</a>
    <a href="{{ route('supply-centers.index') }}" class="btn btn-success mb-3 ms-2">Supply Centers</a>
    <a href="{{ route('tasks.auto-assign') }}" class="btn btn-warning mb-3 ms-2">Auto Assign</a>
    <a href="{{ route('workforce.report') }}" class="btn btn-secondary mb-3 ms-2">Workforce Report</a>
    <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
        <h1 class="h4 mb-0">Task Management</h1>
        <a href="{{ route('home') }}" class="btn btn-outline-primary">Back to Dashboard</a>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <h4>Tasks</h4>
    <a href="{{ route('tasks.create') }}" class="btn btn-primary mb-3">Add Task</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Task Name</th>
                <th>Required Skill</th>
                <th>Quantity</th>
                <th>Center</th>
                <th>Assigned</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tasks as $task)
                <tr>
                    <td>{{ $task->name }}</td>
                    <td>{{ $task->required_skill }}</td>
                    <td>{{ $task->quantity }}</td>
                    <td>{{ $task->center->name ?? 'N/A' }}</td>
                    <td>{{ $task->assigned_count }}</td>
                    <td>
                        <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this task?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection 