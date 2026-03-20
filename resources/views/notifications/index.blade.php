@extends(auth()->user()->hasRole('admin') ? 'layouts.admin' : (auth()->user()->hasRole('factory') ? 'layouts.factory' : 'layouts.buyer'))

@section('title', __('labels.notifications'))

@section('content')
<h4 class="fw-bold mb-4">{{ __('labels.notifications_alerts') }}</h4>
<p class="text-muted small mb-4">{{ __('labels.pending_items_subtitle') }}</p>

@if($pendingAlerts->isNotEmpty())
<div class="card mb-4">
  <div class="card-header py-2"><h6 class="mb-0">{{ __('labels.pending') }}</h6></div>
  <div class="card-body p-0">
    <ul class="list-group list-group-flush">
      @foreach($pendingAlerts as $alert)
      <li class="list-group-item">
        <a href="{{ $alert['url'] }}" class="text-decoration-none d-flex align-items-center">
          <i class="bx {{ $alert['icon'] }} me-2 text-warning"></i>
          <span class="fw-medium">{{ $alert['title'] }}</span>
          <small class="text-muted ms-2">– {{ __('labels.needs_attention') }}</small>
        </a>
      </li>
      @endforeach
    </ul>
  </div>
</div>
@endif

<div class="card">
  <div class="card-header py-2"><h6 class="mb-0">{{ __('labels.recent') }}</h6></div>
  <div class="card-body">
    @if($notifications->isEmpty())
      <p class="text-muted mb-0">{{ __('labels.no_recent_notifications') }}</p>
    @else
      <ul class="list-group list-group-flush">
        @foreach($notifications as $n)
        @php
          $type = $n->data['type'] ?? 'order';
          $url = \App\Helpers\NotificationHelper::urlForNotification($n, auth()->user());
          $title = \App\Helpers\NotificationHelper::titleForNotification($n);
        @endphp
        <li class="list-group-item d-flex justify-content-between align-items-start {{ $n->read_at ? '' : 'bg-light' }}">
          <div class="ms-2 me-auto">
            <a href="{{ $url }}" class="text-decoration-none">
              <span class="fw-medium">{{ $title }}</span>
            </a>
            <small class="d-block text-muted">{{ $n->created_at->diffForHumans() }}</small>
          </div>
          @if(!$n->read_at)
            <form method="POST" action="{{ route('notifications.read', $n->id) }}" class="d-inline">@csrf<button type="submit" class="btn btn-sm btn-outline-primary">{{ __('labels.mark_read') }}</button></form>
          @endif
        </li>
        @endforeach
      </ul>
    @endif
  </div>
</div>
@endsection
