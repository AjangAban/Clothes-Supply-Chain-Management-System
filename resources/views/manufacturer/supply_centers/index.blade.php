@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h2>Supply Centers</h2>
    <a href="{{ route('supply-centers.create') }}" class="btn btn-primary mb-3">Add Supply Center</a>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Location</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($supplyCenters as $center)
                <tr>
                    <td>{{ $center->name }}</td>
                    <td>{{ $center->location }}</td>
                    <td>
                        <a href="{{ route('supply-centers.edit', $center->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('supply-centers.destroy', $center->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this supply center?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection 