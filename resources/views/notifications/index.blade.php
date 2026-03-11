@extends(auth()->user()->hasRole('admin') ? 'layouts.admin' : 'layouts.factory')

@section('title', 'Notifications')

@section('content')
<h4 class="fw-bold mb-4">Notifications</h4>
<div class="card">
  <div class="card-body">
    @if($notifications->isEmpty())
      <p class="text-muted mb-0">No notifications yet.</p>
    @else
      <ul class="list-group list-group-flush">
        @foreach($notifications as $n)
        <li class="list-group-item d-flex justify-content-between align-items-start {{ $n->read_at ? '' : 'bg-light' }}">
          <div class="ms-2 me-auto">
            <a href="{{ $n->data['order_id'] ?? null ? (auth()->user()->hasRole('admin') ? route('admin.orders.show', $n->data['order_id']) : route('factory.orders.show', $n->data['order_id'])) : '#' }}" class="text-decoration-none">
              <span class="fw-medium">{{ $n->data['order_name'] ?? $n->data['order_code'] ?? 'New order' }}</span>
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
