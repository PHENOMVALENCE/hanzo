@extends('layouts.admin')

@section('title', 'Payment Details')

@section('content')
<h4 class="fw-bold mb-4">Payment Details</h4>
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
<div class="row">
  <div class="col-lg-8">
    <div class="card mb-4">
      <div class="card-header">Payment Information</div>
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6"><strong>Order</strong><br><a href="{{ route('admin.orders.show', $payment->order) }}">{{ $payment->order->order_code }}</a></div>
          <div class="col-md-6"><strong>Buyer</strong><br>{{ $payment->order->buyer?->name }} ({{ $payment->order->buyer?->email }})</div>
          <div class="col-md-6"><strong>Type</strong><br>{{ \App\Models\Payment::TYPES[$payment->type] ?? $payment->type }}</div>
          <div class="col-md-6"><strong>Amount</strong><br>${{ number_format($payment->amount_usd, 2) }}</div>
          <div class="col-md-6"><strong>Method</strong><br>{{ $payment->method ?: '—' }}</div>
          <div class="col-md-6"><strong>Reference</strong><br><code>{{ $payment->reference ?: '—' }}</code></div>
          <div class="col-md-6"><strong>Submitted</strong><br>{{ $payment->created_at->format('M j, Y H:i') }}</div>
          <div class="col-md-6">
            <strong>Status</strong><br>
            <span class="badge bg-{{ $payment->status === 'verified' ? 'success' : ($payment->status === 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($payment->status) }}</span>
          </div>
          @if($payment->verified_at)
          <div class="col-md-6"><strong>Verified By</strong><br>{{ $payment->verifiedBy?->name ?? '—' }} on {{ $payment->verified_at->format('M j, Y') }}</div>
          @endif
          @if($payment->rejection_reason)
          <div class="col-12">
            <strong>Rejection Reason</strong>
            <div class="alert alert-warning py-2 mt-1 mb-0">{{ $payment->rejection_reason }}</div>
          </div>
          @endif
          @if($payment->admin_notes)
          <div class="col-12">
            <strong>Admin Notes</strong>
            <p class="text-muted small mb-0">{{ $payment->admin_notes }}</p>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card mb-4">
      <div class="card-header">Proof of Payment</div>
      <div class="card-body">
        @if($payment->proof_path)
          @if(str_starts_with($payment->mime_type ?? '', 'image/'))
          <a href="{{ route('admin.payments.proof', $payment) }}" target="_blank">
            <img src="{{ route('admin.payments.proof', $payment) }}" alt="Proof" class="img-fluid rounded border" style="max-height: 300px;">
          </a>
          @else
          <a href="{{ route('admin.payments.proof', $payment) }}" target="_blank" class="btn btn-primary w-100">
            <i class="bx bx-download me-1"></i> Download Proof (PDF)
          </a>
          @endif
        @else
          <p class="text-muted mb-0">No proof uploaded.</p>
        @endif
      </div>
    </div>
    @if($payment->status === 'pending')
    <div class="card">
      <div class="card-header">Actions</div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.payments.verify', $payment) }}" class="mb-2">
          @csrf
          <div class="mb-2">
            <label class="form-label small">Admin Notes (optional)</label>
            <textarea name="admin_notes" class="form-control form-control-sm" rows="2" placeholder="Internal note"></textarea>
          </div>
          <button type="submit" class="btn btn-success w-100">Verify Payment</button>
        </form>
        <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">Reject Payment</button>
        <div class="modal fade" id="rejectModal" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content">
              <form method="POST" action="{{ route('admin.payments.reject', $payment) }}">
                @csrf
                <div class="modal-header"><h5 class="modal-title">Reject Payment</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                  <label class="form-label">Rejection Reason (shown to buyer)</label>
                  <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="e.g. Proof unclear..."></textarea>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-danger">Reject</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endif
  </div>
</div>
<a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">Back to Payments</a>
@endsection
