@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h2>Edit Task</h2>
    <form action="{{ route('tasks.update', $task->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Task Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $task->name }}" required>
        </div>
        <div class="mb-3">
            <label for="required_skill" class="form-label">Required Skill</label>
            <input type="text" name="required_skill" id="required_skill" class="form-control" value="{{ $task->required_skill }}" required>
        </div>
        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" name="quantity" id="quantity" class="form-control" value="{{ $task->quantity }}" min="1" required>
        </div>
        <div class="mb-3">
            <label for="center_id" class="form-label">Center</label>
            <select name="center_id" id="center_id" class="form-control" required>
                <option value="">Select Center</option>
                @foreach($centers as $center)
                    <option value="{{ $center->id }}" @if($task->center_id == $center->id) selected @endif>{{ $center->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-success">Update Task</button>
        <a href="{{ route('tasks.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection 