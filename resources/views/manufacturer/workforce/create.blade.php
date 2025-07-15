@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h2>Add Worker</h2>
    <form action="{{ route('workforce.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="skill" class="form-label">Skill</label>
            <input type="text" name="skill" id="skill" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="shift" class="form-label">Shift</label>
            <select name="shift" id="shift" class="form-control" required>
                <option value="">Select Shift</option>
                <option value="Morning">Morning</option>
                <option value="Evening">Evening</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-control" required>
                <option value="available">Available</option>
                <option value="assigned">Assigned</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Add Worker</button>
        <a href="{{ route('workforce.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection 