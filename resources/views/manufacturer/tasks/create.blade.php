@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h2>Add Task</h2>
    @if($centers->isEmpty())
        <div class="alert alert-warning">No supply centers found. Please <a href='{{ route('supply-centers.create') }}'>add a supply center</a> first.</div>
    @endif
    <form action="{{ route('tasks.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Task Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="required_skill" class="form-label">Required Skill</label>
            <input type="text" name="required_skill" id="required_skill" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" name="quantity" id="quantity" class="form-control" min="1" required>
        </div>
        <div class="mb-3">
            <label for="center_id" class="form-label">Center</label>
            <select name="center_id" id="center_id" class="form-control" required @if($centers->isEmpty()) disabled @endif>
                <option value="">Select Center</option>
                @foreach($centers as $center)
                    <option value="{{ $center->id }}">{{ $center->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-success" @if($centers->isEmpty()) disabled @endif>Add Task</button>
        <a href="{{ route('tasks.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection 