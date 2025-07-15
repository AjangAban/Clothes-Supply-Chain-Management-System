@extends('layouts.app')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0">Auto Assignment Results</h1>
        <a href="{{ route('home') }}" class="btn btn-outline-primary">Back to Dashboard</a>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Worker</th>
                <th>Skill</th>
                <th>Task</th>
                <th>Shift</th>
                <th>Center</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $result)
                <tr>
                    <td>{{ $result['worker'] }}</td>
                    <td>{{ $result['skill'] }}</td>
                    <td>{{ $result['task'] }}</td>
                    <td>{{ $result['shift'] }}</td>
                    <td>{{ $result['center'] }}</td>
                    <td>
                        @if(strtolower($result['status']) === 'unassigned')
                            Unassigned
                            <span tabindex="0" data-bs-toggle="tooltip" data-bs-placement="top" title="All required positions for this task are already filled or constraints (shift, center, etc.) prevent assignment." style="cursor:pointer;">
                                <i class="fas fa-info-circle text-info"></i>
                            </span>
                        @else
                            {{ $result['status'] }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection 

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush 