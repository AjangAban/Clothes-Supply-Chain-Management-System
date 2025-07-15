@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h2>Workforce Distribution Report</h2>
    <form method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-3">
                <label for="date" class="form-label">Date</label>
                <input type="date" name="date" id="date" class="form-control" value="{{ $date }}">
            </div>
            <div class="col-md-3">
                <label for="shift" class="form-label">Shift</label>
                <select name="shift" id="shift" class="form-control">
                    <option value="Morning" @if($shift == 'Morning') selected @endif>Morning</option>
                    <option value="Evening" @if($shift == 'Evening') selected @endif>Evening</option>
                </select>
            </div>
            <div class="col-md-3 align-self-end">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </form>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Center</th>
                <th>Task</th>
                <th>Skill</th>
                <th>Assigned Workers</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report as $row)
                <tr>
                    <td>{{ $row['center'] }}</td>
                    <td>{{ $row['task'] }}</td>
                    <td>{{ $row['skill'] }}</td>
                    <td>{{ $row['assigned_workers'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection 