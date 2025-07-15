@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h2>Assign Workers to Task: {{ $task->name }}</h2>
    <form action="" method="POST">
        @csrf
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Select</th>
                    <th>Name</th>
                    <th>Skill</th>
                    <th>Shift</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($availableWorkers as $worker)
                    <tr>
                        <td><input type="checkbox" name="worker_ids[]" value="{{ $worker->id }}"></td>
                        <td>{{ $worker->name }}</td>
                        <td>{{ $worker->skill }}</td>
                        <td>{{ $worker->shift }}</td>
                        <td>{{ ucfirst($worker->status) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <button type="submit" class="btn btn-success">Assign Selected Workers</button>
        <a href="{{ route('tasks.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection 