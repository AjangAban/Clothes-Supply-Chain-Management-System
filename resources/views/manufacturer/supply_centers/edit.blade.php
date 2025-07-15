@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h2>Edit Supply Center</h2>
    <form action="{{ route('supply-centers.update', $supplyCenter->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $supplyCenter->name }}" required>
        </div>
        <div class="mb-3">
            <label for="location" class="form-label">Location</label>
            <input type="text" name="location" id="location" class="form-control" value="{{ $supplyCenter->location }}">
        </div>
        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ route('supply-centers.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection 