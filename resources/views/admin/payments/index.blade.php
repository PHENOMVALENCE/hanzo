@extends('layouts.admin')

@section('title', 'Payments')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
  <div>
    <h4 class="fw-bold mb-1">Payments</h4>
    <p class="text-muted mb-0 small">Review and verify payment submissions</p>
  </div>
</div>
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif
<div class="card">
  <div class="card-body p-0">
    @if($payments->isEmpty())
      <p class="text-muted mb-0 p-4">No payments yet.</p>
    @else
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead>
            <tr>
              <th style="min-width: 110px;">Order</th>
              <th>Buyer</th>
              <th>Type</th>
              <th>Amount</th>
              <th>Method</th>
              <th>Reference</th>
              <th>Submitted</th>
              <th>Status</th>
              <th style="min-width: 120px;">Verified By</th>
              <th style="min-width: 180px;" class="text-end">Actions</th>
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
                  {{ trans_status($p->status) }}
                </span>
              </td>
              <td>{{ $p->verifiedBy?->name ?? '—' }} {{ $p->verified_at ? '(' . $p->verified_at->format('M j') . ')' : '' }}</td>
              <td class="text-end">
                @if($p->proof_path)
                <a href="{{ route('admin.payments.proof', $p) }}" class="btn btn-sm btn-outline-info" target="_blank" title="View proof">
                  <i class="bx bx-file"></i>
                </a>
                @endif
                <a href="{{ route('admin.payments.show', $p) }}" class="btn btn-sm btn-outline-light">Details</a>
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
      <div class="p-3 border-top border-secondary">
        {{ $payments->links() }}
      </div>
    @endif
  </div>
</div>
@endsection
