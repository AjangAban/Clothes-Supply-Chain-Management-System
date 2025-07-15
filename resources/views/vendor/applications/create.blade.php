@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Submit Vendor Application</h2>
    <div class="alert alert-info">
        <strong>Instructions:</strong> Please upload a single PDF document containing the following:
        <ul>
            <li><strong>Financial Stability:</strong> Recent financial statements, proof of annual revenue, net assets, and confirmation of no bankruptcy history.</li>
            <li><strong>Reputation:</strong> At least two reference letters, years in business, and disclosure of any legal disputes in the last 3 years.</li>
            <li><strong>Regulatory Compliance:</strong> Compliance certificates (e.g., ISO), business registration certificate, and proof of no regulatory violations in the last 3 years.</li>
        </ul>
        <p>Applications missing any of the above may be rejected.</p>
    </div>
    <form action="{{ route('vendor.applications.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="pdf" class="form-label">Application PDF</label>
            <input type="file" class="form-control" id="pdf" name="pdf" accept="application/pdf" required>
            @error('pdf')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Submit Application</button>
    </form>
</div>
@endsection 