@extends('layouts.buyer')

@section('title', __('buyer.quotes.title') . ' ' . $quotation->quote_code)

@section('content')
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card mb-4 overflow-hidden">
  <div class="card-header text-white py-4 d-flex flex-wrap justify-content-between align-items-center gap-2" style="background: linear-gradient(135deg, #0B1F3A 0%, #123A6D 100%) !important; border: none;">
    <h4 class="mb-0">Quotation #{{ $quotation->quote_code }}</h4>
    @if($quotation->valid_until && $quotation->status === 'sent')
    <div class="d-flex align-items-center gap-2">
      <span class="badge bg-{{ $quotation->valid_until->isPast() ? 'danger' : 'warning text-dark' }}" id="validityBadge">
        @if($quotation->valid_until->isPast())
          Expired
        @else
          Valid for <span id="validityCountdown">{{ $quotation->valid_until->diffInDays(now()) }}</span> days
        @endif
      </span>
      <small class="opacity-75">(until {{ $quotation->valid_until->format('M j, Y') }})</small>
    </div>
    @endif
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-lg-8">
        <div class="hanzo-landed-cost p-4 rounded mb-4">
          <h5 class="mb-3">Cost Breakdown</h5>
          <table class="table table-sm mb-0">
            <tr><td>Product Cost</td><td class="text-end">{{ money($quotation->product_cost_usd) }}</td></tr>
            <tr><td>Shipping & Freight</td><td class="text-end">{{ money($quotation->freight_cost + $quotation->china_local_shipping + $quotation->export_handling + ($quotation->insurance_cost ?? 0)) }}</td></tr>
            <tr><td>Customs & Clearing</td><td class="text-end">{{ money($quotation->clearing_cost) }}</td></tr>
            <tr><td>Local Delivery</td><td class="text-end">{{ money($quotation->local_delivery_cost) }}</td></tr>
            @if(($quotation->hanzo_fee ?? 0) > 0)
            <tr><td>Fees & Handling</td><td class="text-end">{{ money($quotation->hanzo_fee) }}</td></tr>
            @endif
          </table>
          <hr>
          <div class="d-flex justify-content-between align-items-center">
            <strong>Total Landed Cost</strong>
            <span class="fs-4 fw-bold text-primary">{{ money($quotation->total_landed_cost) }}</span>
          </div>
        </div>
        <p class="text-muted small mb-0">{{ __('labels.rfq') }}: {{ $quotation->rfq->code }} &bull; {{ __('buyer.quotes.valid_until') }} {{ $quotation->valid_until?->format('M j, Y') }}</p>
      </div>
      <div class="col-lg-4">
        @if($quotation->status === 'sent')
        <div class="d-grid gap-2">
          <form method="POST" action="{{ route('buyer.quotes.accept', $quotation) }}">
            @csrf
            <button type="submit" class="btn btn-warning w-100 text-dark fw-semibold">
              <i class="bx bx-check me-1"></i> Accept Quote
            </button>
          </form>
          <a href="#" class="btn btn-outline-primary w-100">Ask a Question</a>
          <button type="button" class="btn btn-link text-danger p-0 small mt-2" data-bs-toggle="modal" data-bs-target="#rejectModal">Reject Quote</button>
          <div class="modal fade" id="rejectModal" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <form method="POST" action="{{ route('buyer.quotes.reject', $quotation) }}">
                  @csrf
                  <div class="modal-header">
                    <h5 class="modal-title">Reject Quote</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <p class="text-muted small mb-2">Optionally share why you're rejecting so we can improve future quotes.</p>
                    <label class="form-label">Reason (optional)</label>
                    <textarea name="rejection_reason" class="form-control" rows="3" placeholder="e.g. Price too high, timeline doesn't work..."></textarea>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Quote</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        @else
        <span class="badge bg-{{ $quotation->status === 'accepted' ? 'success' : 'secondary' }} fs-6">{{ trans_status($quotation->status) }}</span>
        @endif
      </div>
    </div>
  </div>
</div>

<a href="{{ route('buyer.quotes.index') }}" class="btn btn-outline-secondary">{{ __('buyer.quotes.back') }}</a>

@if($quotation->valid_until && $quotation->status === 'sent' && !$quotation->valid_until->isPast())
@push('page-js')
<script>
(function() {
  var until = new Date('{{ $quotation->valid_until->toIso8601String() }}');
  function update() {
    var now = new Date();
    if (now >= until) {
      var badge = document.getElementById('validityBadge');
      var span = document.getElementById('validityCountdown');
      if (badge) { badge.className = 'badge bg-danger'; badge.textContent = 'Expired'; }
      if (span) span.textContent = '0';
      return;
    }
    var days = Math.ceil((until - now) / (24*60*60*1000));
    var span = document.getElementById('validityCountdown');
    if (span) span.textContent = days;
  }
  update();
  setInterval(update, 60000);
})();
</script>
@endpush
@endif
@endsection
