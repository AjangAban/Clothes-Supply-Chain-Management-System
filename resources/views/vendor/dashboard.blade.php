@extends('layouts.app')

@section('content')
<div class="container py-5">
    {{-- Personalized Greeting --}}
    <h2 class="fw-bold mb-4">Welcome, {{ auth()->user()->name }} ({{ $vendor->name ?? 'Vendor' }})</h2>

    {{-- Progress Bar --}}
    <div class="progress mb-4" style="height: 30px;">
      <div class="progress-bar bg-success" style="width: 33%">Application Submitted</div>
      <div class="progress-bar bg-info" style="width: 33%">Validation</div>
      <div class="progress-bar bg-secondary" style="width: 34%">Facility Visit</div>
    </div>

    {{-- Application Status --}}
    <div class="card mb-4 shadow-sm animate__animated animate__fadeIn">
      <div class="card-header">Application Status</div>
      <div class="card-body">
        @if($latestApplication)
          <p><strong>Status:</strong> <span class="badge bg-{{ $latestApplication->status === 'approved' ? 'success' : ($latestApplication->status === 'pending' ? 'warning' : ($latestApplication->status === 'validating' ? 'info' : 'danger')) }}">{{ ucfirst($latestApplication->status) }}</span></p>
          <p><strong>Application PDF:</strong> <a href="{{ asset('storage/' . $latestApplication->pdf_path) }}" target="_blank">Download/View</a></p>
          @if($latestApplication->validation_results)
            <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#validationResultsModal">View Validation Results</button>
          @else
            <span class="btn btn-outline-secondary btn-sm disabled">No Results</span>
          @endif
          @if($latestApplication->status === 'rejected')
            <a href="{{ route('vendor.applications.create') }}" class="btn btn-primary mt-3">Apply Again</a>
          @endif
        @else
          <p>No application found. Please submit your application to get started.</p>
          <a href="{{ route('vendor.applications.create') }}" class="btn btn-primary mt-3">Apply Now</a>
        @endif
      </div>
    </div>

    {{-- Validation Results Section --}}
    @if($latestApplication)
      @php $results = json_decode($latestApplication->validation_results, true); @endphp
      <h5 class="mt-4">Validation Results <span data-bs-toggle="tooltip" title="See how your application was evaluated."><i class="bi bi-info-circle"></i></span></h5>
      <ul class="list-group mb-3">
          <li class="list-group-item d-flex justify-content-between align-items-center">
              Financial Stability
              <span data-bs-toggle="tooltip" title="We check your revenue, net assets, and bankruptcy history.">
                  @if(isset($results['financial_stability']) && $results['financial_stability'] >= 0.7)
                      <span class="badge bg-success"><i class="bi bi-check-circle"></i> Pass</span>
                  @else
                      <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Fail</span>
                  @endif
              </span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
              Reputation
              <span data-bs-toggle="tooltip" title="We check for reference letters, years in business, and legal disputes.">
                  @if(isset($results['reputation']) && $results['reputation'] >= 0.7)
                      <span class="badge bg-success"><i class="bi bi-check-circle"></i> Pass</span>
                  @else
                      <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Fail</span>
                  @endif
              </span>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center">
              Regulatory Compliance
              <span data-bs-toggle="tooltip" title="We check for compliance certificates, registration, and regulatory history.">
                  @if(isset($results['compliance']) && $results['compliance'] >= 0.7)
                      <span class="badge bg-success"><i class="bi bi-check-circle"></i> Pass</span>
                  @else
                      <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Fail</span>
                  @endif
              </span>
          </li>
      </ul>

      {{-- Improved Validation Notes Display --}}
      @if($latestApplication->validation_notes)
          <div class="alert alert-info animate__animated animate__fadeIn">
              <strong>Validation Notes:</strong>
              @php
                  $notes = $latestApplication->validation_notes;
              @endphp
              @if(Str::contains($notes, 'reason(s):'))
                  <ul class="mb-0">
                      @foreach(explode('.', $notes) as $reason)
                          @if(trim($reason) && !Str::startsWith($reason, 'Your application was not approved'))
                              <li>{{ trim($reason) }}</li>
                          @endif
                      @endforeach
                  </ul>
              @else
                  {{ $notes }}
              @endif
          </div>
      @endif

      {{-- Facility Visit Status --}}
      @if($latestApplication->status === 'approved' && $latestVisit)
          <h5 class="mt-4">Facility Visit</h5>
          <div class="card">
              <div class="card-body">
                  <p><strong>Status:</strong> <span class="badge bg-{{ $latestVisit->status === 'completed' ? 'success' : ($latestVisit->status === 'scheduled' ? 'info' : ($latestVisit->status === 'in_progress' ? 'warning' : 'secondary')) }}">{{ ucfirst(str_replace('_', ' ', $latestVisit->status)) }}</span></p>
          <p><strong>Scheduled Date:</strong> {{ $latestVisit->scheduled_date ? $latestVisit->scheduled_date->format('M d, Y') : 'N/A' }}</p>
                  @if($latestVisit->inspector_name && $latestVisit->inspector_name !== 'To be assigned')
                      <p><strong>Inspector:</strong> {{ $latestVisit->inspector_name }}</p>
                  @endif
          @if($latestVisit->visit_notes)
              <div class="alert alert-secondary">
                  <strong>Visit Notes:</strong> {{ $latestVisit->visit_notes }}
              </div>
          @endif
                  @if($latestVisit->status === 'scheduled')
                      <div class="alert alert-info">
                          <strong>Next Steps:</strong> An inspector will be assigned to your facility visit. You will be notified when the inspector is assigned and can contact them to coordinate the visit details.
                      </div>
                  @endif
              </div>
          </div>
      @elseif($latestApplication->status === 'approved')
          <div class="alert alert-warning">
              <strong>Facility Visit:</strong> Your facility visit will be scheduled automatically. Please check back later for updates.
          </div>
      @endif

      {{-- What's Next Section --}}
      <div class="mt-4">
          <h6>What's Next?</h6>
          @if($latestApplication->status === 'pending')
              <p>Your application is pending review. Please wait for validation.</p>
          @elseif($latestApplication->status === 'validating')
              <p>Your application is being validated. Please wait for results.</p>
          @elseif($latestApplication->status === 'approved')
              <p>Your application has been approved. Prepare for the facility visit. You will be notified of the date and inspector.</p>
          @elseif($latestApplication->status === 'rejected')
              <p>Your application was rejected. Please review the notes and consider reapplying. If you have questions, contact support below.</p>
          @endif
      </div>
    @endif

    {{-- Application History Table --}}
    @if($vendor->applications && $vendor->applications->count() > 1)
        <h5 class="mt-5">Application History</h5>
        <table class="table table-striped table-responsive">
          <thead>
            <tr>
              <th>Date</th>
              <th>Status</th>
              <th>Notes</th>
              <th>PDF</th>
            </tr>
          </thead>
          <tbody>
            @foreach($vendor->applications->sortByDesc('created_at') as $app)
              <tr>
                <td>{{ $app->created_at->format('M d, Y') }}</td>
                <td><span class="badge bg-{{ $app->status === 'approved' ? 'success' : ($app->status === 'pending' ? 'warning' : ($app->status === 'validating' ? 'info' : 'danger')) }}">{{ ucfirst($app->status) }}</span></td>
                <td>{{ Str::limit($app->validation_notes, 50) }}</td>
                <td>
                  <a href="{{ route('vendor.applications.show', $app->id) }}" class="btn btn-sm btn-info">View</a>
                  <a href="{{ asset('storage/' . $app->pdf_path) }}" target="_blank" class="btn btn-sm btn-secondary ms-1">View {{ $app->original_filename ?? basename($app->pdf_path) }}</a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
    @endif

    {{-- Contact/Support Info --}}
    {{-- Removed duplicate support footer include --}}

    {{-- Tooltips for Validation Criteria and Animate on Load --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function (tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Auto-refresh dashboard every 30 seconds to check for facility visit updates
            setInterval(function() {
                // Only refresh if the user is on the dashboard page and not actively interacting
                if (document.visibilityState === 'visible' && !document.hasFocus()) {
                    window.location.reload();
                }
            }, 30000); // 30 seconds
        });
    </script>
</div>

{{-- Modal for Validation Results --}}
@if($latestApplication && $latestApplication->validation_results)
  @php $results = json_decode($latestApplication->validation_results, true); @endphp
  <div class="modal fade" id="validationResultsModal" tabindex="-1" aria-labelledby="validationResultsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="validationResultsModalLabel">Validation Results</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <ul>
            <li><strong>Financial Stability:</strong> {{ $results['financial_stability'] ?? 'N/A' }}</li>
            <li><strong>Reputation:</strong> {{ $results['reputation'] ?? 'N/A' }}</li>
            <li><strong>Compliance:</strong> {{ $results['compliance'] ?? 'N/A' }}</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
@endif
@endsection 