@extends('layouts.app')
@section('content')
<div class="container py-4">
    <a href="{{ route('workforce.index') }}" class="btn btn-primary mb-3 me-2 disabled">Workers</a>
    <a href="{{ route('tasks.index') }}" class="btn btn-info mb-3 ms-2">Tasks</a>
    <a href="{{ route('supply-centers.index') }}" class="btn btn-success mb-3 ms-2">Supply Centers</a>
    <a href="{{ route('tasks.auto-assign') }}" class="btn btn-warning mb-3 ms-2">Auto Assign</a>
    <a href="{{ route('workforce.report') }}" class="btn btn-secondary mb-3 ms-2">Workforce Report</a>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <h4>Workers</h4>
    <a href="{{ route('workforce.create') }}" class="btn btn-primary mb-3">Add Worker</a>
    <table class="table table-bordered">
        <thead><tr><th>Name</th><th>Skill</th><th>Shift</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        @foreach($workers as $worker)
            <tr>
                <td>{{ $worker->name }}</td>
                <td>{{ $worker->skill }}</td>
                <td>{{ $worker->shift }}</td>
                <td>{{ ucfirst($worker->status) }}</td>
                <td>
                    <a href="{{ route('workforce.edit', $worker->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('workforce.destroy', $worker->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this worker?')">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection 