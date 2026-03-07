@extends('layouts.admin')

@section('title', ucfirst($type) . ' Approval')

@section('content')
<h4 class="fw-bold mb-4">{{ ucfirst($type) }} Pending Approval</h4>
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
<div class="card">
  <div class="card-body">
    @if($users->isEmpty())
      <p class="text-muted mb-0">No pending {{ $type }}.</p>
    @else
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Company</th>
              <th>Country</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($users as $user)
            <tr>
              <td>{{ $user->name }}</td>
              <td>{{ $user->email }}</td>
              <td>{{ $user->company_name ?? '-' }}</td>
              <td>{{ $user->country ?? '-' }}</td>
              <td>
                <form method="POST" action="{{ route('admin.approvals.approve', $user->id) }}" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-success">Approve</button>
                </form>
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
                          <label class="form-label">Optional message (sent to buyer)</label>
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
