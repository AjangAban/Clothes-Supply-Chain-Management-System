@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Application Details</h2>
    <div class="card mb-4">
        <div class="card-header">Status: <span class="badge bg-{{ $application->status === 'approved' ? 'success' : ($application->status === 'pending' ? 'warning' : ($application->status === 'validating' ? 'info' : 'danger')) }}">{{ ucfirst($application->status) }}</span></div>
        <div class="card-body">
            <p><strong>Submitted At:</strong> {{ $application->created_at->format('M d, Y H:i') }}</p>
            <p><strong>Application PDF:</strong> <a href="{{ asset('storage/' . $application->pdf_path) }}" target="_blank">Download/View</a></p>
            @if($application->pdf_path)
                <div class="my-4">
                    <embed src="{{ asset('storage/' . $application->pdf_path) }}" type="application/pdf" width="100%" height="600px" />
                </div>
            @endif
            @if($application->validation_notes)
                <p><strong>Validation Notes:</strong> {{ $application->validation_notes }}</p>
            @endif
            @if($application->validation_results)
                @php $results = json_decode($application->validation_results, true); @endphp
                <h5>Validation Results</h5>
                <ul>
                    <li><strong>Financial Stability:</strong> {{ $results['financial_stability'] ?? 'N/A' }}</li>
                    <li><strong>Reputation:</strong> {{ $results['reputation'] ?? 'N/A' }}</li>
                    <li><strong>Compliance:</strong> {{ $results['compliance'] ?? 'N/A' }}</li>
                </ul>
            @endif
        </div>
    </div>
    <a href="{{ route('vendor.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
</div>
@endsection 