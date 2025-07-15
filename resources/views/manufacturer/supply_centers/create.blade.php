@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h2>Add Supply Center</h2>
    <form action="{{ route('supply-centers.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="location" class="form-label">Location</label>
            <input type="text" name="location" id="location" class="form-control">
        </div>
        <button type="submit" class="btn btn-success">Create</button>
        <a href="{{ route('supply-centers.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection 