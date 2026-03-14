@extends('layouts.admin')

@section('title', ucfirst($type) . ' Approval')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
  <div>
    <h4 class="fw-bold mb-1">{{ ucfirst($type) }} Pending Approval</h4>
    <p class="text-muted mb-0 small">Review and approve or reject {{ $type }} applications</p>
  </div>
</div>
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
<div class="card">
  <div class="card-body p-0">
    @if($users->isEmpty())
      <p class="text-muted mb-0 p-4">No pending {{ $type }}.</p>
    @else
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Company</th>
              <th>Country</th>
              @if($type === 'buyers')<th>Details</th>@endif
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($users as $user)
            <tr>
              <td>
                <strong>{{ $user->name }}</strong>
                @if($user->job_title)<br><small class="text-muted">{{ $user->job_title }}</small>@endif
              </td>
              <td>{{ $user->email }}</td>
              <td>{{ $user->company_name ?? '-' }}</td>
              <td>{{ $user->country ?? '-' }}</td>
              @if($type === 'buyers')
              <td>
                @if($user->business_registration_path)
                  <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($user->business_registration_path) }}" target="_blank" class="btn btn-sm btn-outline-info">Doc</a>
                @else
                  <span class="small text-muted">—</span>
                @endif
              </td>
              @endif
              <td>
                <form method="POST" action="{{ route('admin.approvals.approve', $user->id) }}" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-success">Approve</button>
                </form>
                @if($type === 'buyers')
                <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#requestInfoModal{{ $user->id }}">Request Info</button>
                @endif
                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $user->id }}">Reject</button>
                <div class="modal fade" id="rejectModal{{ $user->id }}" tabindex="-1">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form method="POST" action="{{ route('admin.approvals.reject', $user->id) }}">
                        @csrf
                        <div class="modal-header">
                          <h5 class="modal-title">Reject {{ $user->name }}</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <label class="form-label">Message (sent to {{ $type === 'buyers' ? 'buyer' : 'factory' }})</label>
                          <textarea name="message" class="form-control" rows="3" placeholder="Reason for rejection..."></textarea>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                          <button type="submit" class="btn btn-danger">Reject</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
                @if($type === 'buyers')
                <div class="modal fade" id="requestInfoModal{{ $user->id }}" tabindex="-1">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form method="POST" action="{{ route('admin.approvals.requestMoreInfo', $user->id) }}">
                        @csrf
                        <div class="modal-header">
                          <h5 class="modal-title">Request More Info – {{ $user->name }}</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <label class="form-label">Message (sent to buyer)</label>
                          <textarea name="message" class="form-control" rows="4" required placeholder="Specify what documents or information you need..."></textarea>
                          <small class="text-muted">Buyer stays pending. They can update their profile and you can re-review.</small>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                          <button type="submit" class="btn btn-warning">Send Request</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
</div>
@endsection
