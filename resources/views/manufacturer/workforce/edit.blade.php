@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h2>Edit Worker</h2>
    <form action="{{ route('workforce.update', $worker->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $worker->name }}" required>
        </div>
        <div class="mb-3">
            <label for="skill" class="form-label">Skill</label>
            <input type="text" name="skill" id="skill" class="form-control" value="{{ $worker->skill }}" required>
        </div>
        <div class="mb-3">
            <label for="shift" class="form-label">Shift</label>
            <select name="shift" id="shift" class="form-control" required>
                <option value="Morning" @if($worker->shift == 'Morning') selected @endif>Morning</option>
                <option value="Evening" @if($worker->shift == 'Evening') selected @endif>Evening</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-control" required>
                <option value="available" @if($worker->status == 'available') selected @endif>Available</option>
                <option value="assigned" @if($worker->status == 'assigned') selected @endif>Assigned</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Update Worker</button>
        <a href="{{ route('workforce.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection 