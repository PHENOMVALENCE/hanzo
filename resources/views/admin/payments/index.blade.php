@extends('layouts.admin')

@section('title', 'Payments')

@section('content')
<h4 class="fw-bold mb-4">Payments</h4>
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif
<div class="card">
  <div class="card-body">
    @if($payments->isEmpty())
      <p class="text-muted mb-0">No payments yet.</p>
    @else
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>Order</th>
              <th>Buyer</th>
              <th>Type</th>
              <th>Amount</th>
              <th>Method</th>
              <th>Reference</th>
              <th>Submitted</th>
              <th>Status</th>
              <th>Verified By</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($payments as $p)
            <tr>
              <td>
                <a href="{{ route('admin.orders.show', $p->order) }}">{{ $p->order->order_code }}</a>
              </td>
              <td>{{ $p->order->buyer?->name }}</td>
              <td>{{ \App\Models\Payment::TYPES[$p->type] ?? $p->type }}</td>
              <td>${{ number_format($p->amount_usd, 2) }}</td>
              <td>{{ $p->method ?: '—' }}</td>
              <td><code class="small">{{ $p->reference ?: '—' }}</code></td>
              <td>{{ $p->created_at->format('M j, Y') }}</td>
              <td>
                <span class="badge bg-{{ $p->status === 'verified' ? 'success' : ($p->status === 'rejected' ? 'danger' : 'warning') }}">
                  {{ ucfirst($p->status) }}
                </span>
              </td>
              <td>{{ $p->verifiedBy?->name ?? '—' }} {{ $p->verified_at ? '(' . $p->verified_at->format('M j') . ')' : '' }}</td>
              <td>
                @if($p->proof_path)
                <a href="{{ route('admin.payments.proof', $p) }}" class="btn btn-sm btn-outline-info" target="_blank" title="View proof">
                  <i class="bx bx-file"></i>
                </a>
                @endif
                <a href="{{ route('admin.payments.show', $p) }}" class="btn btn-sm btn-outline-primary">Details</a>
                @if($p->status === 'pending')
                <form method="POST" action="{{ route('admin.payments.verify', $p) }}" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-success">Verify</button>
                </form>
                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $p->id }}">Reject</button>
                <div class="modal fade" id="rejectModal{{ $p->id }}" tabindex="-1">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form method="POST" action="{{ route('admin.payments.reject', $p) }}">
                        @csrf
                        <div class="modal-header">
                          <h5 class="modal-title">Reject Payment</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <p class="text-muted small">Reason will be shown to the buyer.</p>
                          <label class="form-label">Rejection Reason</label>
                          <textarea name="rejection_reason" class="form-control" rows="3" placeholder="e.g. Proof unclear, amount mismatch..."></textarea>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                          <button type="submit" class="btn btn-danger">Reject Payment</button>
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
      {{ $payments->links() }}
    @endif
  </div>
</div>
@endsection
