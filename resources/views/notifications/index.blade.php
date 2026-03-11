@extends(auth()->user()->hasRole('admin') ? 'layouts.admin' : (auth()->user()->hasRole('factory') ? 'layouts.factory' : 'layouts.buyer'))

@section('title', 'Notifications')

@section('content')
<h4 class="fw-bold mb-4">Notifications &amp; Alerts</h4>
<p class="text-muted small mb-4">Pending items and recent updates appear here.</p>

@if($pendingAlerts->isNotEmpty())
<div class="card mb-4">
  <div class="card-header py-2"><h6 class="mb-0">Pending</h6></div>
  <div class="card-body p-0">
    <ul class="list-group list-group-flush">
      @foreach($pendingAlerts as $alert)
      <li class="list-group-item">
        <a href="{{ $alert['url'] }}" class="text-decoration-none d-flex align-items-center">
          <i class="bx {{ $alert['icon'] }} me-2 text-warning"></i>
          <span class="fw-medium">{{ $alert['title'] }}</span>
          <small class="text-muted ms-2">– needs attention</small>
        </a>
      </li>
      @endforeach
    </ul>
  </div>
</div>
@endif

<div class="card">
  <div class="card-header py-2"><h6 class="mb-0">Recent</h6></div>
  <div class="card-body">
    @if($notifications->isEmpty())
      <p class="text-muted mb-0">No recent notifications.</p>
    @else
      <ul class="list-group list-group-flush">
        @foreach($notifications as $n)
        @php
          $type = $n->data['type'] ?? 'order';
          $url = '#';
          if ($type === 'order' && !empty($n->data['order_id'])) {
            $url = auth()->user()->hasRole('admin') ? route('admin.orders.show', $n->data['order_id']) : (auth()->user()->hasRole('factory') ? route('factory.orders.show', $n->data['order_id']) : route('buyer.orders.show', $n->data['order_id']));
          } elseif ($type === 'quote_sent' && !empty($n->data['quotation_id'])) {
            $url = route('buyer.quotes.show', $n->data['quotation_id']);
          } elseif ($type === 'quote_rejected' && !empty($n->data['rfq_id'])) {
            $url = auth()->user()->hasRole('admin') ? route('admin.rfqs.show', $n->data['rfq_id']) : route('factory.rfqs.show', $n->data['rfq_id']);
          } elseif ($type === 'payment_pending' && !empty($n->data['payment_id']) && auth()->user()->hasRole('admin')) {
            $url = route('admin.payments.show', $n->data['payment_id']);
          }
          $title = match($type) {
            'quote_sent' => 'New quote: ' . ($n->data['quote_code'] ?? ''),
            'quote_rejected' => 'Quote rejected: ' . ($n->data['quote_code'] ?? '') . ' by ' . ($n->data['buyer_name'] ?? ''),
            'payment_pending' => 'Payment pending: $' . number_format($n->data['amount'] ?? 0, 2) . ' – ' . ($n->data['order_code'] ?? ''),
            default => $n->data['order_name'] ?? $n->data['order_code'] ?? 'New order',
          };
        @endphp
        <li class="list-group-item d-flex justify-content-between align-items-start {{ $n->read_at ? '' : 'bg-light' }}">
          <div class="ms-2 me-auto">
            <a href="{{ $url }}" class="text-decoration-none">
              <span class="fw-medium">{{ $title }}</span>
            </a>
            <small class="d-block text-muted">{{ $n->created_at->diffForHumans() }}</small>
          </div>
          @if(!$n->read_at)
            <form method="POST" action="{{ route('notifications.read', $n->id) }}" class="d-inline">@csrf<button type="submit" class="btn btn-sm btn-outline-primary">Mark read</button></form>
          @endif
        </li>
        @endforeach
      </ul>
    @endif
  </div>
</div>
@endsection
